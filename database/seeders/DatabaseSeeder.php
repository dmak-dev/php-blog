<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Демонстрационная учётная запись для входа в панель управления.
     * Пароль указан в README: его нужно заменить перед рабочим запуском.
     */
    private const DEMO_USER = [
        'name' => 'Администратор',
        'email' => 'admin@example.com',
    ];

    private const DEMO_PASSWORD = 'password';

    /**
     * Заполняет базу демонстрационными данными блога.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => self::DEMO_USER['email']],
            [
                'name' => self::DEMO_USER['name'],
                'password' => Hash::make(self::DEMO_PASSWORD),
            ],
        );

        $this->call(PostSeeder::class);
    }
}
