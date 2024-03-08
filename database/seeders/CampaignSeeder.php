<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
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
      $kemanusiaan = Category::factory()->create([
        'name' => 'kemanusiaan',
        'description' => 'This is kemanusiaan category'
      ]);
      
      $kesehatan = Category::factory()->create([
        'name' => 'kesehatan',
        'description' => 'This is kesehatan category'
      ]);
      
      $pendidikan = Category::factory()->create([
        'name' => 'pendidikan',
        'description' => 'This is pendidikan category'
      ]);
      
      $tanggapBencana = Category::factory()->create([
        'name' => 'tanggap bencana',
        'description' => 'This is tanggap bencana category'
      ]);

      Campaign::factory()->create([
        'name' => 'Galang Dana Kemanusiaan',
        'type' => 'kemanusiaan',
        'description' => 'Ini adalah campaign kemanusiaan',
        'photo' => 'kemanusiaan_default.jpeg'
      ])->categories()->attach($kemanusiaan);

      Campaign::factory()->create([
        'name' => 'Galang Dana Kesehatan',
        'type' => 'kesehatan',
        'description' => 'Ini adalah campaign kesehatan',
        'photo' => 'kesehatan_default.jpg'
      ])->categories()->attach($kesehatan);

      Campaign::factory()->create([
        'name' => 'Galang Dana Pendidikan',
        'type' => 'pendidikan',
        'description' => 'Ini adalah campaign pendidikan',
        'photo' => 'pendidikan_default.jpeg'
      ])->categories()->attach($pendidikan);

      Campaign::factory()->create([
        'name' => 'Galang Dana Tanggap Bencana',
        'type' => 'tanggap bencana',
        'description' => 'Ini adalah campaign tanggap bencana',
        'photo' => 'tanggap-bencana_default.jpg'
      ])->categories()->attach($tanggapBencana);
    }
}
