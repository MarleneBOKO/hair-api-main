<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type_coiffure extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_hairstyle_type';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'category',
        'image',
        'price'

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

    public function coiffures() : BelongsTo
    {
        return $this->belongsTo(Coiffure::class, 'coiffure_id');
    }
    public function employe()
    {
        return $this->belongsToMany(Employe::class,'employe_type_coiffures');
    }
    public function accessoires()
    {
        return $this->belongsToMany(Accessoire::class,'accessoire_type_coiffures');
    }


}
