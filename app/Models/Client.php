<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;


class Client extends Model
{
    use HasFactory,Notifiable;

    protected $primaryKey = 'id_client'; 

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
        'birth_date',
        'gender',
        'notes',
        'first_visit_date',
        'last_visit_date'

    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    public function services()
    {
        return $this->belongsToMany(Historique_service::class);
    }

    public function users() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');

    }
    public function salons() : BelongsTo
    {
          return $this->belongsTo(Salon::class , 'salon_id');
    }

    
}
