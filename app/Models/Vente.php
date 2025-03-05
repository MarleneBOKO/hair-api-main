<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;


class Vente extends Model
{
    use HasFactory;


    protected $primaryKey = 'id_sale'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'date',
        'total_amount',
        'payment_method',
        'notes'

     ];
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    public function hairstyles() : BelongsTo
    {
        return $this->belongsTo(Type_coiffure::class, 'hairstyle_type_id');

    }

    public function clients() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');

    }

    public function salons() : BelongsTo
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

    public function users() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');

    }
}
