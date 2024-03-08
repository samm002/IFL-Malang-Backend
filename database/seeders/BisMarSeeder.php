<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class BisMarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $role = Role::where('name', 'bismar')->first();

      User::factory()->create([
        'username' => 'bisMar',
        'email' => 'bisMar@gmail.com',
        'password' => bcrypt('bisMar0123'),
        'email_verified_at' => now(),
        'remember_token' => Str::random(10),
        'role_id' => $role->id,
      ]);
  
      User::factory(2)->create([
        'role_id' => $role->id,
      ]);
    }
}
