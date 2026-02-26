<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressModel extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'street',
        'number',
        'city',
        'state',
        'zipcode'
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id', 'id');
    }
}
