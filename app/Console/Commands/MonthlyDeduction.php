<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonthlyDeduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-deduction';

    protected $description = 'Monthly deduction of 50,000 from users with sufficient balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Potongan bulanan diambil dari nilai 'limit' masing-masing user
        // Hanya memotong jika saldo mencukupi (saldo >= limit)
        // Kita exclude role 3 (Operator) karena mereka menggunakan saldo parent
        $users = \App\Models\User::whereIn('role', [1, 2])
            ->where('limit', '>', 0)
            ->whereRaw('saldo >= `limit`')
            ->get();

        foreach ($users as $user) {
            $amount = $user->limit;
            $user->saldo -= $amount;
            $user->save();

            \App\Models\History::create([
                'user_id' => $user->id,
                'type' => 'deduction',
                'amount' => $amount,
                'description' => 'Potongan biaya bulanan',
            ]);
        }

        $this->info('Potongan bulanan diproses untuk ' . $users->count() . ' user.');
    }
}
