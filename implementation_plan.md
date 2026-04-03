# Analisa & Best Practice: Flutter Offline/Online Mode + SQLite

## Analisa Kondisi Saat Ini

### Flutter (`sales/`)
| Aspek | Kondisi Saat Ini | Status |
|---|---|---|
| State Management | GetX | ✅ Sudah |
| HTTP Client | `http` package | ✅ Sudah |
| Local Storage | `get_storage` (key-value) | ⚠️ Tidak cukup untuk data relasional |
| SQLite / Database Lokal | ❌ Tidak ada | ❌ Belum |
| Offline Detection | `connectivity_plus` ✅ sudah ada | ⚠️ Belum dipakai untuk fallback |
| Cache Master Data | ❌ Tidak ada | ❌ Belum |
| Offline Write Queue | ❌ Tidak ada | ❌ Belum |
| Sync ke Server | ❌ Tidak ada | ❌ Belum |

### Laravel (`web/`)
| Aspek | Kondisi Saat Ini | Status |
|---|---|---|
| REST API | ✅ Lengkap (auth, items, transaksi, dll.) | ✅ Sudah |
| Delta Sync endpoint | ❌ Tidak ada | ❌ Belum |
| Offline batch upload | ❌ Tidak ada | ❌ Belum |

---

## Arsitektur Target (Best Practice)

```
┌──────────────────────────────────────────────┐
│              Flutter App (GetX)               │
│                                               │
│  View ←→ Controller ←→ Repository            │
│                            ↓                  │
│              ┌─────────────────────────┐      │
│              │    ConnectivityService  │      │
│              └──────┬──────────────────┘      │
│                     │                         │
│           Online?   │   Offline?              │
│              ↓      │      ↓                  │
│         ApiService  │  LocalDb (SQLite)        │
│              ↓      │      ↓                  │
│         Laravel API │  SyncQueue (pending)    │
│                     │      ↓ (saat online)    │
│                     └──→ Sync ke Laravel       │
└──────────────────────────────────────────────┘
```

### Prinsip Utama
1. **Repository Pattern** — Controller tidak panggil API langsung, tapi lewat Repository
2. **Offline-First untuk Baca** — data master (items, customer) selalu di-cache ke SQLite
3. **Write Queue untuk Tulis** — transaksi offline disimpan di tabel `pending_sync`, lalu di-upload saat online
4. **Auto Sync** — saat koneksi kembali, sync otomatis berjalan di background

---

## Langkah Perubahan

### TAHAP 1: Flutter — Tambah Package

#### [MODIFY] [pubspec.yaml](file:///home/qq/Desktop/pos/sales/pubspec.yaml)

Tambahkan di bagian [dependencies](file:///home/qq/Desktop/pos/sales/.flutter-plugins-dependencies):
```yaml
  sqflite: ^2.3.3+1        # SQLite database
  sqflite_common_ffi: ^2.3.4 # Untuk support Linux/Windows desktop
```

> `connectivity_plus` sudah ada, tidak perlu tambah.

**Jalankan:**
```bash
cd /home/qq/Desktop/pos/sales
flutter pub get
```

---

### TAHAP 2: Flutter — Buat Local Database Helper

#### [NEW] `lib/app/data/local/database_helper.dart`

```dart
import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class DatabaseHelper {
  static Database? _db;
  static const int _version = 1;

  static Future<Database> get database async {
    _db ??= await _initDb();
    return _db!;
  }

  static Future<Database> _initDb() async {
    final path = join(await getDatabasesPath(), 'pos_local.db');
    return openDatabase(path, version: _version, onCreate: _onCreate);
  }

  static Future<void> _onCreate(Database db, int version) async {
    // Cache master data
    await db.execute('''
      CREATE TABLE items (
        id INTEGER PRIMARY KEY,
        kode TEXT, nama TEXT, harga_jual REAL, harga_beli REAL,
        stok REAL, satuan_id INTEGER, foto TEXT,
        updated_at TEXT
      )
    ''');
    await db.execute('''
      CREATE TABLE customers (
        id INTEGER PRIMARY KEY,
        nama TEXT, telepon TEXT, alamat TEXT, updated_at TEXT
      )
    ''');
    // Antrian transaksi offline
    await db.execute('''
      CREATE TABLE pending_sync (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        endpoint TEXT NOT NULL,
        method TEXT NOT NULL,
        payload TEXT NOT NULL,
        created_at TEXT NOT NULL,
        status TEXT DEFAULT 'pending'
      )
    ''');
  }
}
```

---

### TAHAP 3: Flutter — Buat ConnectivityService

#### [NEW] `lib/app/services/connectivity_service.dart`

```dart
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:get/get.dart';

class ConnectivityService extends GetxService {
  final isOnline = true.obs;

  @override
  void onInit() {
    super.onInit();
    _listen();
  }

  void _listen() {
    Connectivity().onConnectivityChanged.listen((result) {
      isOnline.value = result != ConnectivityResult.none;
      if (isOnline.value) {
        // Trigger sync otomatis saat online
        SyncService.syncPending();
      }
    });
  }
}
```

Daftarkan di [main.dart](file:///home/qq/Desktop/pos/sales/lib/main.dart):
```dart
await Get.putAsync(() async => ConnectivityService());
```

---

### TAHAP 4: Flutter — Buat Repository Pattern

#### [NEW] `lib/app/data/repositories/item_repository.dart`

```dart
class ItemRepository {
  final _api = ApiService();
  final _db = DatabaseHelper.database;

  /// Selalu baca dari SQLite, sync dari API jika online
  Future<List<Item>> getItems() async {
    final db = await _db;
    final connectivity = Get.find<ConnectivityService>();

    if (connectivity.isOnline.value) {
      try {
        final response = await ApiService.get('/items');
        final List data = jsonDecode(response.body)['data'];
        final items = data.map((e) => Item.fromJson(e)).toList();
        await _cacheItems(db, items); // Simpan ke SQLite
        return items;
      } catch (_) {
        return _getLocalItems(db); // Fallback ke lokal
      }
    } else {
      return _getLocalItems(db); // Offline: baca lokal
    }
  }

  Future<List<Item>> _getLocalItems(Database db) async {
    final rows = await db.query('items');
    return rows.map((r) => Item.fromJson(r)).toList();
  }

  Future<void> _cacheItems(Database db, List<Item> items) async {
    final batch = db.batch();
    for (final item in items) {
      batch.insert('items', item.toJson(),
          conflictAlgorithm: ConflictAlgorithm.replace);
    }
    await batch.commit(noResult: true);
  }
}
```

Buat juga `CustomerRepository` dengan pola yang sama.

---

### TAHAP 5: Flutter — Offline Write Queue untuk Transaksi

#### [NEW] `lib/app/data/local/sync_queue.dart`

```dart
import 'dart:convert';
import 'package:sqflite/sqflite.dart';
import 'database_helper.dart';

class SyncQueue {
  /// Tambahkan transaksi ke antrian offline
  static Future<void> enqueue({
    required String endpoint,
    required String method,
    required Map<String, dynamic> payload,
  }) async {
    final db = await DatabaseHelper.database;
    await db.insert('pending_sync', {
      'endpoint': endpoint,
      'method': method,
      'payload': jsonEncode(payload),
      'created_at': DateTime.now().toIso8601String(),
      'status': 'pending',
    });
  }

  /// Ambil semua pending
  static Future<List<Map<String, dynamic>>> getPending() async {
    final db = await DatabaseHelper.database;
    return db.query('pending_sync', where: 'status = ?', whereArgs: ['pending']);
  }

  /// Tandai berhasil
  static Future<void> markDone(int id) async {
    final db = await DatabaseHelper.database;
    await db.delete('pending_sync', where: 'id = ?', whereArgs: [id]);
  }

  /// Tandai gagal
  static Future<void> markFailed(int id) async {
    final db = await DatabaseHelper.database;
    await db.update('pending_sync', {'status': 'failed'},
        where: 'id = ?', whereArgs: [id]);
  }
}
```

---

### TAHAP 6: Flutter — SyncService (Upload pending saat online)

#### [NEW] `lib/app/services/sync_service.dart`

```dart
import 'dart:convert';
import '../data/local/sync_queue.dart';
import 'api_service.dart';

class SyncService {
  static bool _isSyncing = false;

  static Future<void> syncPending() async {
    if (_isSyncing) return;
    _isSyncing = true;
    try {
      final pending = await SyncQueue.getPending();
      for (final item in pending) {
        try {
          final payload = jsonDecode(item['payload']);
          final method = item['method'];
          final endpoint = item['endpoint'];
          if (method == 'POST') {
            await ApiService.post(endpoint, body: payload);
          } else if (method == 'PUT') {
            await ApiService.put(endpoint, body: payload);
          }
          await SyncQueue.markDone(item['id']);
        } catch (_) {
          await SyncQueue.markFailed(item['id']);
        }
      }
    } finally {
      _isSyncing = false;
    }
  }
}
```

---

### TAHAP 7: Flutter — Update Controller Transaksi (Penjualan)

#### [MODIFY] [lib/app/modules/penjualan/controllers/penjualan_controller.dart](file:///home/qq/Desktop/pos/sales/lib/app/modules/penjualan/controllers/penjualan_controller.dart)

Ubah logika simpan transaksi:

```dart
// SEBELUM (hanya online):
final response = await ApiService.post('/transaksis', body: payload);

// SESUDAH (online/offline aware):
final connectivity = Get.find<ConnectivityService>();
if (connectivity.isOnline.value) {
  // Langsung kirim ke API
  final response = await ApiService.post('/transaksis', body: payload);
  // handle response...
} else {
  // Simpan ke antrian offline
  await SyncQueue.enqueue(
    endpoint: '/transaksis',
    method: 'POST',
    payload: payload,
  );
  // Simpan juga ke SQLite lokal untuk tampilan history
  CustomDialog.success('Transaksi disimpan (akan sync saat online)');
}
```

---

### TAHAP 8: Laravel — Tambah Delta Sync Endpoint

#### [MODIFY] [api.php](file:///home/qq/Desktop/pos/web/routes/api.php)

Tambahkan route untuk delta sync:
```php
Route::get('items',      [ItemController::class, 'index']);     // sudah ada
// Tambah filter updated_at untuk delta sync:
// GET /api/items?updated_after=2024-01-01T00:00:00
```

#### [NEW] `app/Http/Controllers/Api/SyncController.php`

```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * Terima batch transaksi dari offline queue
     */
    public function uploadOffline(Request $request)
    {
        $items = $request->input('items', []);
        $results = [];

        foreach ($items as $item) {
            try {
                $transaksi = Transaksi::create($item);
                $results[] = ['success' => true, 'id' => $transaksi->id];
            } catch (\Exception $e) {
                $results[] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return response()->json(['results' => $results]);
    }
}
```

Di [api.php](file:///home/qq/Desktop/pos/web/routes/api.php), tambahkan:
```php
Route::middleware('auth:api')->group(function () {
    // ...existing routes...
    Route::post('sync-offline', [SyncController::class, 'uploadOffline']);
});
```

#### [MODIFY] `app/Http/Controllers/Api/ItemController.php`

Tambahkan filter delta sync:
```php
public function index(Request $request)
{
    $query = Item::query();
    if ($request->filled('updated_after')) {
        $query->where('updated_at', '>=', $request->updated_after);
    }
    return ItemResource::collection($query->paginate(500));
}
```

---

## Alur Lengkap Online/Offline

```
ONLINE MODE:
  App Start → ConnectivityService.isOnline = true
  → Repository.getItems() → ApiService.get('/items') → cache ke SQLite
  → Transaksi baru → ApiService.post('/transaksis') langsung

OFFLINE MODE:
  Koneksi putus → ConnectivityService.isOnline = false
  → Repository.getItems() → baca dari SQLite (data terakhir)
  → Transaksi baru → SyncQueue.enqueue() → simpan ke pending_sync

SAAT ONLINE KEMBALI:
  ConnectivityService mendeteksi koneksi kembali
  → SyncService.syncPending() otomatis berjalan
  → Upload semua pending_sync ke Laravel API
  → Hapus dari antrian
```

---

## Checklist Perubahan File

| # | File | Aksi |
|---|---|---|
| 1 | [sales/pubspec.yaml](file:///home/qq/Desktop/pos/sales/pubspec.yaml) | MODIFY — tambah `sqflite` |
| 2 | [sales/lib/main.dart](file:///home/qq/Desktop/pos/sales/lib/main.dart) | MODIFY — daftar `ConnectivityService` |
| 3 | `sales/lib/app/data/local/database_helper.dart` | NEW |
| 4 | `sales/lib/app/data/local/sync_queue.dart` | NEW |
| 5 | `sales/lib/app/services/connectivity_service.dart` | NEW |
| 6 | `sales/lib/app/services/sync_service.dart` | NEW |
| 7 | `sales/lib/app/data/repositories/item_repository.dart` | NEW |
| 8 | `sales/lib/app/data/repositories/customer_repository.dart` | NEW |
| 9 | [sales/lib/app/modules/penjualan/controllers/penjualan_controller.dart](file:///home/qq/Desktop/pos/sales/lib/app/modules/penjualan/controllers/penjualan_controller.dart) | MODIFY |
| 10 | `web/app/Http/Controllers/Api/SyncController.php` | NEW |
| 11 | [web/app/Http/Controllers/Api/ItemController.php](file:///home/qq/Desktop/pos/web/app/Http/Controllers/Api/ItemController.php) | MODIFY |
| 12 | [web/routes/api.php](file:///home/qq/Desktop/pos/web/routes/api.php) | MODIFY |

---

## Verification Plan

### Manual Testing Flutter
1. Jalankan app → pastikan data item/customer muncul (online)
2. Matikan WiFi/internet di HP
3. Buka menu Item → data masih muncul dari SQLite (offline)
4. Buat transaksi baru offline → muncul pesan "disimpan, akan sync"
5. Nyalakan kembali koneksi → tunggu 3 detik → cek Laravel DB → transaksi masuk

### Manual Testing Laravel
```bash
cd /home/qq/Desktop/pos/web
php artisan serve
# Test endpoint baru:
curl -X POST http://localhost:8000/api/sync-offline \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"items": [{"customer_id": 1, ...}]}'
```
