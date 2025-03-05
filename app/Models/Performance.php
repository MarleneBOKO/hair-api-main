<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;


class Performance extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_performance'; 

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'revenue_generated',
        'clients_served',
       

    ];


    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    public function employes()
    {
        return $this->belongsTo(Employe::class);
    }

    public function historiqueService() : BelongsTo
    {
        return $this->belongsTo(Historique_service::class,'service_history_id');
    }
}
