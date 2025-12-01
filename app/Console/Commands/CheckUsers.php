<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users table for issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== UTILISATEURS ===');
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            $this->line("ID: '{$user->id}' - Email: {$user->email}");
        }

        $this->info('=== STRUCTURE TABLE USERS ===');
        $columns = \DB::select("SHOW COLUMNS FROM users");
        foreach ($columns as $column) {
            $this->line("{$column->Field}: {$column->Type} ({$column->Key})");
        }

        $this->info('=== VÉRIFICATION IDS ===');
        $emptyIds = \DB::table('users')->whereNull('id')->orWhere('id', '')->count();
        $this->line("IDs vides ou null: {$emptyIds}");

        $duplicateIds = \DB::select("SELECT id, COUNT(*) as count FROM users GROUP BY id HAVING COUNT(*) > 1");
        $this->line("IDs dupliqués: " . count($duplicateIds));
        foreach ($duplicateIds as $dup) {
            $this->line("  ID '{$dup->id}': {$dup->count} occurrences");
        }
        
        return 0;
    }
}
