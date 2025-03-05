<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;


class Historique_service extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_service_history'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'date',
        'amount_paid',
        'notes',
        'heure_fin',
        'heure_fin',
        'date_rdv',
        'hairstyle_name',
        'duration',
        'image',
        'review_send'
   

     ];
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    public function rendez_vous() : BelongsToMany
    {
        return $this->BelongsToMany(Rendez_vou::class, 'appointment_id');

    }

   

    public function employes() : BelongsToMany
    {
        return $this->BelongsToMany(Employe::class, 'employe_rendez_vous');

    }

    public function salons() : BelongsTo
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

    public function performance() : BelongsTo
    {
        return $this->belongsTo(Performance::class);

    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    

   
}
