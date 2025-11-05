<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Information;

class InformationSeeder extends Seeder
{
    public function run(): void
    {
        Information::create([
            'content' => 'We are specialiste in clothes for all categories mens ,womens and kids',
        ]);

        Information::create([
            'content' => 'Our customer support is available from 8 AM to 6 PM, sunday through tuesday.',
        ]);

        Information::create([
            'content' => 'We offer free delivery for orders above 10,000 DZD.',
        ]);
    }
}
