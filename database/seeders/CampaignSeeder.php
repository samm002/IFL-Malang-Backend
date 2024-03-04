<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Campaign::factory()->create([
        'name' => 'Galang Dana Kemanusiaan',
        'type' => 'kemanusiaan',
        'description' => 'Ini adalah campaign kemanusiaan',
        'photo' => 'kemanusiaan_default.jpeg'
      ]);

      Campaign::factory()->create([
        'name' => 'Galang Dana Kesehatan',
        'type' => 'kesehatan',
        'description' => 'Ini adalah campaign kesehatan',
        'photo' => 'kesehatan_default.jpg'
      ]);

      Campaign::factory()->create([
        'name' => 'Galang Dana pPndidikan',
        'type' => 'pendidikan',
        'description' => 'Ini adalah campaign pendidikan',
        'photo' => 'pendidikan_default.jpeg'
      ]);

      Campaign::factory()->create([
        'name' => 'Galang Dana Tanggap Bencana',
        'type' => 'tanggap bencana',
        'description' => 'Ini adalah campaign tanggap bencana',
        'photo' => 'tanggap-bencana_default.jpg'
      ]);
    }
}
