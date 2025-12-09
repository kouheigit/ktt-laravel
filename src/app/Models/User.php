<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'member_id','name','email','password','last_name',
        'first_name','last_kana','first_kana','zip1','zip2',
        'address1','address2','tel','company_name','company_kana',
        'company_zip1','company_zip2','company_address1','company_address2',
        'company_tel','company_fax','send_name','send_kana','send_zip1','send_zip2',
        'send_address1','send_address2','send_tel','type','agree','status','user_id',
    ];
    public function hotels()
    {
        return $this->belongsToMany(Holt::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function userPoints()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime'];
    }
}

