<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'code' => 'MK',
                'name' => [
                    'mk' => 'Македонија',
                    'sr' => 'Македонија',
                    'hr' => 'Sjeverna Makedonija',
                    'sq' => 'Maqedonia e Veriut',
                    'bg' => 'Северна Македония',
                    'el' => 'Βόρεια Μακεδονία',
                    'en' => 'North Macedonia',
                ],
            ],
            [
                'code' => 'RS',
                'name' => [
                    'mk' => 'Србија',
                    'sr' => 'Србија',
                    'hr' => 'Srbija',
                    'sq' => 'Serbia',
                    'bg' => 'Сърбия',
                    'el' => 'Σερβία',
                    'en' => 'Serbia',
                ],
            ],
            [
                'code' => 'HR',
                'name' => [
                    'mk' => 'Хрватска',
                    'sr' => 'Хрватска',
                    'hr' => 'Hrvatska',
                    'sq' => 'Kroacia',
                    'bg' => 'Хърватия',
                    'el' => 'Κροατία',
                    'en' => 'Croatia',
                ],
            ],
            [
                'code' => 'AL',
                'name' => [
                    'mk' => 'Албанија',
                    'sr' => 'Албанија',
                    'hr' => 'Albanija',
                    'sq' => 'Shqipëria',
                    'bg' => 'Албания',
                    'el' => 'Αλβανία',
                    'en' => 'Albania',
                ],
            ],
            [
                'code' => 'XK',
                'name' => [
                    'mk' => 'Косово',
                    'sr' => 'Косово',
                    'hr' => 'Kosovo',
                    'sq' => 'Kosova',
                    'bg' => 'Косово',
                    'el' => 'Κόσοβο',
                    'en' => 'Kosovo',
                ],
            ],
            [
                'code' => 'BG',
                'name' => [
                    'mk' => 'Бугарија',
                    'sr' => 'Бугарска',
                    'hr' => 'Bugarska',
                    'sq' => 'Bullgaria',
                    'bg' => 'България',
                    'el' => 'Βουλγαρία',
                    'en' => 'Bulgaria',
                ],
            ],
            [
                'code' => 'GR',
                'name' => [
                    'mk' => 'Грција',
                    'sr' => 'Грчка',
                    'hr' => 'Grčka',
                    'sq' => 'Greqia',
                    'bg' => 'Гърция',
                    'el' => 'Ελλάδα',
                    'en' => 'Greece',
                ],
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                ['name' => $country['name']],
            );
        }
    }
}
