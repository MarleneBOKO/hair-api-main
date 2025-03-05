<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_transaction';

    protected $fillable = [
        'performed_at',
        'received_at',
        'status',
        'amount',
        'source',
        'source_common_name',
        'fees',
        'net',
        'externalTransactionId',
        'acc_fullname',
        'acc_phone',
        'acc_email',
        'acc_person',
        'transactionId',
        'transaction_object',
        'appointment_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }
}
