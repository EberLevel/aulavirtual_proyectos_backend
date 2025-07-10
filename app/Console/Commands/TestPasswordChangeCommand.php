<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestPasswordChangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-change {email} {new_password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password change functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $newPassword = $this->argument('new_password');
        
        $this->info("Testing password change for: {$email}");
        
        try {
            // Buscar el usuario
            $user = DB::table('users')->where('email', $email)->first();
            
            if (!$user) {
                $this->error("Usuario no encontrado con email: {$email}");
                return 1;
            }
            
            $this->info("Usuario encontrado: {$user->name} {$user->lastname}");
            $this->info("Estado actual password_changed: " . ($user->password_changed ? 'true' : 'false'));
            
            // Simular cambio de contraseña
            DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($newPassword),
                'password_changed' => true,
                'password_changed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->info("Contraseña cambiada exitosamente");
            
            // Verificar el cambio
            $updatedUser = DB::table('users')->where('id', $user->id)->first();
            $this->info("Nuevo estado password_changed: " . ($updatedUser->password_changed ? 'true' : 'false'));
            $this->info("Fecha de cambio: " . $updatedUser->password_changed_at);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 