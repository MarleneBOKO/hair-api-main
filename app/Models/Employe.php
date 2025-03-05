<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Illuminate\Notifications\Notifiable;


class Employe extends Model
{
    use HasFactory , Notifiable;
    protected $primaryKey = 'id_employe'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'name',
        'skills',
        'description',
        'image',
        'hiring_date',
        'departure_date',
        'work_hours',
        'salary',
        'status',
        'phone',
        'email'

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
         $this->attributes['work_hours'] = json_encode($value);
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

     public function performances()
    {
        return $this->hasMany(Performance::class);
    }

    public function services()
    {
        return $this->belongsToMany(Historique_service::class,'employe_historique_services');
    }
    public function appointment()
    {
        return $this->belongsToMany(Rendez_vou::class,'employe_rendez_vous');
    }
    public function hairstyles()
    {
        return $this->belongsToMany(Type_coiffure::class,'employe_type_coiffures');
    }
}
