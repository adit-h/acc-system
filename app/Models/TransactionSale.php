<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSale extends Model
{
    use HasFactory;
    protected $table = 'transaction_sale';

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
        'sale_id',
        'reference',
        'description',
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
