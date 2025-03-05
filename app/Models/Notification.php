<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;


class Notification extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_notification'; 

    public $incrementing = false;

    protected $keyType = 'string';

     protected $fillable = [
        'type',
        'content',
        'date',
        
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

    public function users() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');

    }

}
