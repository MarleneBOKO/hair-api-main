<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends AuthenticatableUser implements Authenticatable
{
    use HasFactory, Notifiable , HasApiTokens , CanResetPassword;


    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'login_token',
        'user_type'
    ];
/**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }


    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

     // Override the createToken method to handle UUIDs
     public function generateAccessToken($name, $scopes = [])
     {
         $token = $this->createToken($name, $scopes);
 
         return $token->plainTextToken;
     }
    
}
