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
        $users = \App\Models\User::where('saldo', '>=', 50000)->get();

        foreach ($users as $user) {
            $user->saldo -= 50000;
            $user->save();

            \App\Models\History::create([
                'user_id' => $user->id,
                'type' => 'deduction',
                'amount' => 50000,
                'description' => 'Monthly fee deduction',
            ]);
        }

        $this->info('Monthly deduction processed for ' . $users->count() . ' users.');
    }
}
