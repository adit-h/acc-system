<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the account that owns the category.
     */
    public function account()
    {
        return $this->belongsTo(MasterAccount::class, 'category_id');
    }
}
