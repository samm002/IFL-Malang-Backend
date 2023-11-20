<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = User::factory()->create([
      'name' => 'admin',
      'email' => 'admin@email.com',
      'password' => bcrypt('12345678'),
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
    ]);

    $role = Role::where('name', 'admin')->first();
    $user->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);
  }
}
