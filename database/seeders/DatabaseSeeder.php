<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdPlanSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'Edx@gmail.com',
        ], [
            'name' => 'Admin Edx',
            'password' => bcrypt('Edeal007Wifi2025'),
            'is_admin' => true,
            'is_blocked' => false,
        ]);

        $categories = [
            ['name' => 'Électronique', 'slug' => 'electronique'],
            ['name' => 'Mode', 'slug' => 'mode'],
            ['name' => 'Maison', 'slug' => 'maison'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate([
                'slug' => $c['slug'],
            ], [
                'name' => $c['name'],
            ]);
        }

        $electronics = Category::where('slug', 'electronique')->first();

        $products = [
            ['name' => 'Smartphone X', 'slug' => 'smartphone-x', 'price' => 699.00, 'stock' => 50, 'category_id' => optional($electronics)->id],
            ['name' => 'Casque Audio', 'slug' => 'casque-audio', 'price' => 129.99, 'stock' => 100, 'category_id' => optional($electronics)->id],
            ['name' => 'Montre Connectée', 'slug' => 'montre-connectee', 'price' => 199.99, 'stock' => 75, 'category_id' => optional($electronics)->id],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['slug' => $p['slug']], $p);
        }

        $this->call(AdPlanSeeder::class);
    }
}
