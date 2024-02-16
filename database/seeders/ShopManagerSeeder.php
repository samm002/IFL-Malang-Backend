<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class ShopManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $shopManager = User::factory()->create([
        'username' => 'shopManager',
        'email' => 'shopManager@gmail.com',
        'password' => bcrypt('shopManager0123'),
        'email_verified_at' => now(),
        'remember_token' => Str::random(10),
      ]);
  
      $role = Role::where('name', 'shop manager')->first();
      $shopManager->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);

      $users = User::factory(2)->create();
  
      foreach ($users as $user) {
        $user->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);
      }
    }
}
