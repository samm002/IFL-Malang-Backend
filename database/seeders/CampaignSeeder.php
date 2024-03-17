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
      $this->call(CategorySeeder::class);

      Campaign::factory(2)->create()->each(function ($campaign) {
        $campaign->categories()->attach(Category::inRandomOrder()->limit(rand(1, 3))->pluck('id')->toArray());
      });
    }
}
