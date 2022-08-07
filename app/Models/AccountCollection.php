<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCollection extends Model
{
    use HasFactory;

    public function products(){
        return $this->hasMany('App\Models\AccountCollectionProduct', 'collection_id', 'collection_id');
    }
}
