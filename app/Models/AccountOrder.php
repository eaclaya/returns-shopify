<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountOrder extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo(AccountCustomer::class, 'customer_id', 'customer_id');
    }
    public function transactions(){
        return $this->hasMany(AccountOrderTransaction::class, 'order_id', 'order_id');
    }
    public function items(){
        return $this->hasMany(AccountOrderItem::class, 'order_id', 'order_id');
    }
    public function addresses(){
        return $this->hasMany(AccountOrderAddress::class, 'order_id', 'order_id');
    }
}
