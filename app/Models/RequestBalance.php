<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestBalance extends Model
{
    use HasFactory;

    public function items(){
        return $this->hasMany(RequestItemBalance::class, 'original_order_number', 'original_order_number');
    }
}
