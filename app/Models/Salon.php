<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

class Salon extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_salon'; 

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'salon_name',
        'address',
        'phone_number',
        'email',
        'password',
        'description',
        'image',
        'opening_hours',
        'website',
        'creation_date',
        'last_update_date',
        'longitude',
        'latitude',
        'percent',
        'heure_fin',
        'heure_debut'

    ];


    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

     // Mutateur pour l'attribut "Heures d'ouverture"
     public function setOpeningHoursAttribute($value)
     {
         $this->attributes['opening_hours'] = json_encode($value);
     }
 
     // Accesseur pour l'attribut "Heures d'ouverture"
     public function getOpeningHoursAttribute($value)
     {
         return json_decode($value, true);
     }

     public function users() : BelongsTo
        {
            return $this->belongsTo(User::class, 'user_id');

        }

        public function hairstyles()
        {
            return $this->hasMany(Type_coiffure::class, 'salon_id');
        }

        
}
