<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerModel extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email'
    ];

    public function address(): HasOne
    {
        return $this->hasOne(AddressModel::class, 'customer_id', 'id');
    }
}