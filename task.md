# Task: Offline/Online SQLite Best Practice Analysis

## Planning
- [x] Explore project structure (Flutter & Laravel)
- [x] Read pubspec.yaml — identify current packages
- [x] Read api_service.dart — understand current data flow
- [x] Read Laravel routes/api.php — map API endpoints
- [/] Write implementation_plan.md with step-by-step changes

## Execution (if approved)
- [ ] Flutter: Add SQLite packages to pubspec.yaml
- [ ] Flutter: Create local database helper (DatabaseHelper)
- [ ] Flutter: Create offline-aware repository layer
- [ ] Flutter: Update api_service.dart with connectivity fallback
- [ ] Flutter: Update item/customer/transaksi controllers
- [ ] Flutter: Create sync queue for offline write operations
- [ ] Laravel: Add sync endpoint (`POST /api/sync-offline`)
- [ ] Laravel: Add `updated_at` filter support for delta sync

## Verification
- [ ] Test offline mode: disable network, create transaksi
- [ ] Test sync: re-enable network, verify data synced to server
- [ ] Test online mode: normal flow still works
