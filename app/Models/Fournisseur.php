<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;


class Fournisseur extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_supplier'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'supplier_name',
        'address',
        'phone_number',
        'email',
        'website',
        'contact',
        'notes',

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
}
