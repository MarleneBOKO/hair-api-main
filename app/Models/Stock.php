<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;


class Stock extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_product'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'product_name',
        'quantity',
        'reorder_level',
        'description',
        'addition_date',
        'last_modification_date',

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
        return $this->belongsTo(Salon::class,'salon_id');
    }

    public function suppliers() : BelongsTo
    {
        return $this->belongsTo(Fournisseur::class,'fournisseur_id');
    }
}
