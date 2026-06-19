<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'      => 'Administrator',
                'email'     => 'admin@example.com',
                'password'  => password_hash('admin123', PASSWORD_DEFAULT),
                'role'      => 'admin',
                'is_active' => 1,
            ],
            [
                'name'      => 'Guru User',
                'email'     => 'guru@example.com',
                'password'  => password_hash('guru123', PASSWORD_DEFAULT),
                'role'      => 'guru',
                'is_active' => 1,
            ],
            [
                'name'      => 'Siswa User',
                'email'     => 'siswa@example.com',
                'password'  => password_hash('siswa123', PASSWORD_DEFAULT),
                'role'      => 'siswa',
                'is_active' => 1,
            ]
        ];

        // Simple check to avoid duplicates if run multiple times
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        foreach ($data as $user) {
            $exists = $builder->where('email', $user['email'])->countAllResults();
            if ($exists == 0) {
                $builder->insert($user);
            }
        }
    }
}
