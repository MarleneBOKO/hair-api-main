<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;


class Evaluation extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_review'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'date',
        'rating',
        'comment'

     ];
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

   

    public function clients() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');

    }

    public function employes() : BelongsTo
    {
        return $this->belongsTo(Employe::class, 'employe_id');

    }

    public function salons() : BelongsTo
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

    public function service()
    {
        return $this->belongsTo(Historique_service::class, 'service_history_id');
    }
}
