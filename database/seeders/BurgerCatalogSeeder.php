<?php

namespace Database\Seeders;

use App\Models\Burger;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BurgerCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Classiques', 'description' => 'Burgers traditionnels les plus demandes.'],
            ['name' => 'Gourmets', 'description' => 'Recettes premium avec ingredients speciaux.'],
            ['name' => 'Vegetariens', 'description' => 'Alternatives sans viande.'],
        ])->mapWithKeys(function (array $category) {
            $model = Category::query()->firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']],
            );

            return [$category['name'] => $model];
        });

        $burgers = [
            [
                'category' => 'Classiques',
                'name' => 'ISI Classic',
                'price' => 3500,
                'description' => 'Steak, cheddar, salade, tomate, sauce maison.',
                'stock_quantity' => 30,
                'image_path' => null,
            ],
            [
                'category' => 'Classiques',
                'name' => 'Double ISI',
                'price' => 4800,
                'description' => 'Double steak, fromage fondu et oignons croustillants.',
                'stock_quantity' => 20,
                'image_path' => null,
            ],
            [
                'category' => 'Gourmets',
                'name' => 'Cheese Supreme',
                'price' => 5500,
                'description' => 'Pain brioche, bacon, double cheddar, cornichons.',
                'stock_quantity' => 18,
                'image_path' => null,
            ],
            [
                'category' => 'Vegetariens',
                'name' => 'Green Burger',
                'price' => 4200,
                'description' => 'Galette legumineuses, avocat, crudites et sauce yaourt.',
                'stock_quantity' => 15,
                'image_path' => null,
            ],
        ];

        foreach ($burgers as $burger) {
            Burger::query()->updateOrCreate(
                ['name' => $burger['name']],
                [
                    'category_id' => $categories[$burger['category']]->id,
                    'slug' => Str::slug($burger['name']),
                    'price' => $burger['price'],
                    'description' => $burger['description'],
                    'stock_quantity' => $burger['stock_quantity'],
                    'image_path' => $burger['image_path'],
                    'is_archived' => false,
                ],
            );
        }
    }
}
