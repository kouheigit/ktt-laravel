<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
      'member_id','name','email','password',
        'last_name','first_name','last_kana','first_kana',
        'zip1','zip2','address1','address2','tel',
        'company_name','company_kana','company_kana','company_zip1','company_zip2',
        'company_address1','company_address2','company_tel','company_fax',
        'send_name','send_kana','send_zip1','send_zip2',
        'send_adress1','send_adress2','send_tel','type','agree','status','user_id',
    ];
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
        ];
    }
}
