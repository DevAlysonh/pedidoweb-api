<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;

class CustomerModel extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'customers';

    protected $fillable = [
        'id',
        'name',
        'email',
        'user_id',
    ];

    public function address(): HasOne
    {
        return $this->hasOne(AddressModel::class, 'customer_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}