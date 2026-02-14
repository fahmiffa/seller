<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyDeduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-deduction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly deduction for premium users and deactivation for trial users based on creation date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $dayOfMonth = $today->day;

        // Ambil user yang active dan role 1 (Owner)
        $users = User::where('status', 'active')->where('role', 1)->get();

        $processedCount = 0;
        $deactivatedCount = 0;

        foreach ($users as $user) {
            $createdAt = Carbon::parse($user->created_at);

            // Cek apakah hari ini adalah hari yang sama dengan tanggal pembuatan akun
            // Jika hari ini akhir bulan (misal tgl 28/29 Feb) dan tgl dibuat > hari ini, tetap proses
            $isAnniversary = false;

            if ($createdAt->day == $dayOfMonth) {
                $isAnniversary = true;
            } elseif ($today->isLastOfMonth() && $createdAt->day > $dayOfMonth) {
                // Untuk menangani tanggal 29, 30, 31 di bulan yang lebih pendek
                $isAnniversary = true;
            }

            if ($isAnniversary && $createdAt->format('Y-m') !== $today->format('Y-m')) {
                // Jangan proses di bulan yang sama dengan pendaftaran

                if ($user->trial == 0) {
                    // 1. Jika akun active dan trial nilai 0, maka setiap bulannya dari tanggal akun di buat melakukan potongan dari saldo sejumlah limitnya
                    $amount = $user->limit;
                    DB::transaction(function () use ($user, $amount) {
                        $user->saldo -= $amount;
                        $user->save();

                        History::create([
                            'user_id' => $user->id,
                            'type' => 'deduction',
                            'amount' => $amount,
                            'description' => 'Potongan biaya bulanan otomatis',
                        ]);
                    });

                    $processedCount++;
                    $this->info("User {$user->name} dipotong Rp " . number_format($amount, 0, ',', '.'));
                } elseif ($user->trial == 1) {
                    // 2. jika akun active dan tiral nilai 1, maka setiap bulanya dari tanggal akun dibuat mengecek jika belum ada produk dan transaksi ubah akunya menjadi status inactive
                    $itemCount = $user->items()->count();
                    $transaksiCount = $user->transaksis()->count();

                    if ($itemCount == 0 && $transaksiCount == 0) {
                        $user->status = 'inactive';
                        $user->save();

                        $deactivatedCount++;
                        $this->info("User {$user->name} dinonaktifkan karena tidak ada aktivitas trial.");
                    } else {
                        // Jika ada aktivitas, ubah menjadi premium (trial = 0) agar bulan depan mulai dipotong saldo
                        $user->trial = 0;
                        $user->save();
                        $this->info("User {$user->name} telah melewati masa trial dan sekarang menjadi akun premium.");
                    }
                }
            }
        }

        $this->info("Selesai. {$processedCount} user dipotong saldo, {$deactivatedCount} user dinonaktifkan.");
    }
}
