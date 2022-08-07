<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountCollectionProduct extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function product(){
        return $this->belongsTo(AccountProduct::class, 'product_id', 'parent_id');
    }
}
