<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'video-production',
                'icon' => '🎬',
                'name' => [
                    'mk' => 'Видео продукција',
                    'sr' => 'Видео продукција',
                    'hr' => 'Video produkcija',
                    'sq' => 'Produksion video',
                    'bg' => 'Видео продукция',
                    'el' => 'Παραγωγή βίντεο',
                    'en' => 'Video Production',
                ],
            ],
            [
                'slug' => 'photography',
                'icon' => '📷',
                'name' => [
                    'mk' => 'Фотографија',
                    'sr' => 'Фотографија',
                    'hr' => 'Fotografija',
                    'sq' => 'Fotografi',
                    'bg' => 'Фотография',
                    'el' => 'Φωτογραφία',
                    'en' => 'Photography',
                ],
            ],
            [
                'slug' => 'digital-marketing',
                'icon' => '📈',
                'name' => [
                    'mk' => 'Дигитален маркетинг',
                    'sr' => 'Дигитални маркетинг',
                    'hr' => 'Digitalni marketing',
                    'sq' => 'Marketing dixhital',
                    'bg' => 'Дигитален маркетинг',
                    'el' => 'Ψηφιακό μάρκετινγκ',
                    'en' => 'Digital Marketing',
                ],
            ],
            [
                'slug' => 'video-editing',
                'icon' => '✂️',
                'name' => [
                    'mk' => 'Видео едитинг',
                    'sr' => 'Видео монтажа',
                    'hr' => 'Video montaža',
                    'sq' => 'Montazh video',
                    'bg' => 'Видео монтаж',
                    'el' => 'Μοντάζ βίντεο',
                    'en' => 'Video Editing',
                ],
            ],
            [
                'slug' => 'design',
                'icon' => '🎨',
                'name' => [
                    'mk' => 'Дизајн',
                    'sr' => 'Дизајн',
                    'hr' => 'Dizajn',
                    'sq' => 'Dizajn',
                    'bg' => 'Дизайн',
                    'el' => 'Σχεδιασμός',
                    'en' => 'Design',
                ],
            ],
            [
                'slug' => 'content-creator',
                'icon' => '🎥',
                'name' => [
                    'mk' => 'Креатор на содржини (UGC)',
                    'sr' => 'Kreator sadržaja (UGC)',
                    'hr' => 'Kreator sadržaja (UGC)',
                    'sq' => 'Krijues përmbajtjeje (UGC)',
                    'bg' => 'Създател на съдържание (UGC)',
                    'el' => 'Δημιουργός περιεχομένου (UGC)',
                    'en' => 'Content Creator (UGC)',
                ],
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'icon' => $category['icon']],
            );
        }
    }
}
