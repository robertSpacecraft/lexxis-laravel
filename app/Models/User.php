<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => \App\Enums\UserRole::class,
        ];
    }

    /**
     * Aunque el valor por defecto en la migración ya es customer, sobreescribo el método
     * para reforzar la instrucción
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->role){
                $user->role = \App\Enums\UserRole::CUSTOMER;
            }
        });
    }

    //Para poder ver la lista de Addresses de un usuario usando $user->addresses
    public function addresses(){
        return $this->hasMany(Address::class);
    }

    //para comprobar el rol del usuario
    public function isAdmin(): bool {
        return $this->role === \App\Enums\UserRole::ADMIN;
    }

    public function isCustomer(): bool {
        return $this->role === \App\Enums\UserRole::CUSTOMER;
    }
    public function cart()
    {
        return $this->hasOne(\App\Models\Cart::class);
    }

    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class);
    }


}
