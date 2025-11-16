<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BasicSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles si no existen
        $roles = ['ADMIN', 'COORDINADOR', 'DOCENTE', 'ESTUDIANTE'];
        foreach ($roles as $roleName) {
            if (!DB::table('roles')->where('name', $roleName)->exists()) {
                DB::table('roles')->insert([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Crear usuario admin si no existe
        if (!DB::table('users')->where('email', 'admin@ficct.edu.bo')->exists()) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Administrador',
                'email' => 'admin@ficct.edu.bo',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('role_user')->insert([
                'user_id' => $adminId,
                'role_id' => 1,
            ]);

            echo "✅ Usuario admin creado\n";
        }

        // Crear 3 coordinadores de ejemplo
        for ($i = 1; $i <= 3; $i++) {
            $email = "coordinador{$i}@ficct.edu.bo";
            if (!DB::table('users')->where('email', $email)->exists()) {
                $userId = DB::table('users')->insertGetId([
                    'name' => "Coordinador {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => 2, // ROL COORDINADOR
                ]);
            }
        }

        // Crear 5 docentes de ejemplo
        for ($i = 1; $i <= 5; $i++) {
            $email = "docente{$i}@ficct.edu.bo";
            if (!DB::table('users')->where('email', $email)->exists()) {
                $userId = DB::table('users')->insertGetId([
                    'name' => "Docente {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => 3,
                ]);

                DB::table('teachers')->insert([
                    'user_id' => $userId,
                    'name' => "Docente {$i}",
                    'email' => $email,
                    'dni' => '1234567' . $i,
                    'phone' => '7012345' . $i,
                    'department' => 'Sistemas',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear 5 aulas de ejemplo
        for ($i = 1; $i <= 5; $i++) {
            $roomName = "A-10{$i}";
            if (!DB::table('rooms')->where('name', $roomName)->exists()) {
                DB::table('rooms')->insert([
                    'name' => $roomName,
                    'capacity' => 30 + $i,
                    'location' => 'Edificio A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear 5 estudiantes de ejemplo
        for ($i = 1; $i <= 5; $i++) {
            $email = "estudiante{$i}@ficct.edu.bo";
            if (!DB::table('users')->where('email', $email)->exists()) {
                $userId = DB::table('users')->insertGetId([
                    'name' => "Estudiante {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => 4,
                ]);
            }
        }

        echo "✅ Datos de ejemplo creados: 1 admin, 3 coordinadores, 5 docentes, 5 aulas, 5 estudiantes\n";
        echo "📧 Login: admin@ficct.edu.bo / password\n";
    }
}
