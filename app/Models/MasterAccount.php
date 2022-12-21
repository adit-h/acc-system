<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'status',
    ];

    /**
     * Get the category associated with the account.
     */
    public function accountCategory() {
        //return $this->hasOne(accountCategory::class, 'id', 'category_id');
        return $this->hasOne('App\Models\AccountCategory', 'id', 'category_id');
    }

    /**
     * Get the receive_from id associated with the account.
     */
    public function receiveFromAccount() {
        return $this->belongsTo(TransactionIn::class, 'receive_from');
    }

    /**
     * Get the store_to id associated with the account.
     */
    public function storeToAccount() {
        return $this->belongsTo(TransactionIn::class, 'store_to');
    }
}
