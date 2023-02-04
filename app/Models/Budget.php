<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

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
        'acc_id',
        'value',
        'description',
    ];

    /**
     * Get the account.
     */
    public function hasAccount() {
        return $this->hasOne(MasterAccount::class, 'id', 'acc_id');
    }

}
