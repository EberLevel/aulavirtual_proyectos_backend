<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPasswordStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:password-status {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password_changed status for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            $user = DB::table('users')->where('email', $email)->first();
            
            if (!$user) {
                $this->error("Usuario no encontrado con email: {$email}");
                return 1;
            }
            
            $this->info("Usuario encontrado: {$user->name} {$user->lastname}");
            $this->info("Estado anterior password_changed: " . ($user->password_changed ? 'true' : 'false'));
            
            // Resetear el estado
            DB::table('users')->where('id', $user->id)->update([
                'password_changed' => false,
                'password_changed_at' => null
            ]);
            
            $this->info("✅ Estado password_changed reseteado a false");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 