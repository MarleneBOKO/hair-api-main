<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;


class Rendez_vou extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_appointment';

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'date_and_time',
        'status',
        'hairstyle_name',
        'payment_method',
        'duration',
        'image',
        'total_amount',
        'accompte'

     ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    public function hairstyle() : BelongsTo
    {
        return $this->belongsTo(Type_coiffure::class, 'hairstyle_type_id');

    }

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');

    }

    public function employes() : BelongsToMany
    {
        return $this->BelongsToMany(Employe::class, 'employe_rendez_vous','appointment_id', 'employe_id');

    }

    public function salons() : BelongsTo
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

    public function accessories() : BelongsToMany
    {
        return $this->BelongsToMany(Accessoire::class, 'accessoire_rendez_vous','appointment_id', 'accessory_id');

    }


}
