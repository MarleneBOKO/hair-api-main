<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

class Accessoire extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_accessory';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'quantity',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    public function salons() : BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');

    }

    public function hairstyles() : BelongsToMany
    {
        return $this->belongsToMany(Type_coiffure::class, 'accessoire_type_coiffures');

    }

    public function rendez_vous() : BelongsToMany
    {
        return $this->BelongsToMany(Rendez_vou::class, 'accessoire_rendez_vous');

    }

}
