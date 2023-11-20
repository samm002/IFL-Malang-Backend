<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $users = User::factory(2)->create();
    $role = Role::where('name', 'user')->first();

    foreach ($users as $user) {
      $user->roles()->attach($role, ['created_at' => now(), 'updated_at' => now()]);
    }
  }
}
