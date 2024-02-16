<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class CopyWriterSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $copyWritter = User::factory()->create([
      'username' => 'copyWritter',
      'email' => 'copyWritter@gmail.com',
      'password' => bcrypt('copyWritter0123'),
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
    ]);

    $role = Role::where('name', 'copywriter')->first();
    $copyWritter->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);

    $users = User::factory(2)->create();

    foreach ($users as $user) {
      $user->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);
    }
  }
}
