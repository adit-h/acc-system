<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionIn extends Model
{
    use HasFactory;
    protected $table = 'transaction_in';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trans_date'
    ];

    protected $fillable = [
        'trans_date',
        'receive_from',
        'store_to',
        'value',
        'link_id',
        'reference',
        'description',
        'createby',
        'updateby'
    ];

    /**
     * Get the account that owns the receive_from id.
     */
    public function receiveFrom()
    {
        return $this->hasOne(MasterAccount::class, 'id', 'receive_from');
    }

    /**
     * Get the account that owns the store_to id.
     */
    public function storeTo()
    {
        return $this->hasOne(MasterAccount::class, 'id', 'store_to');
    }
}
