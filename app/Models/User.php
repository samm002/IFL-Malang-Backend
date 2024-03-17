<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
  use HasApiTokens, HasFactory, HasUuids, Notifiable;

  const ROLE_ADMIN = 'admin';
  const ROLE_USER = 'user';
  const ROLE_COPYWRITER = 'copywriter';
  const ROLE_SHOP_MANAGER = 'shop manager';

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'username',
    'email',
    'google_id',
    'password',
    'phone_number',
    'gender',
    'birth_date',
    'address',
    'about_me',
    'profile_picture',
    "background_picture",
    'email_verified_at',

    'role_id',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function role()
  {
    return $this->belongsTo(Role::class);
  }

  public function donations()
  {
    return $this->hasMany(Donation::class);
  }

  public function transactions()
  {
    return $this->hasMany(Transaction::class);
  }

  public function hasRole($role)
  {
    return $this->role->name === $role;
  }
}
