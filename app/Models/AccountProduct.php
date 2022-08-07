<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountProduct extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function collections(){
        return $this->hasMany('App\Models\AccountCollectionProduct', 'product_id', 'parent_id');
    }
}
