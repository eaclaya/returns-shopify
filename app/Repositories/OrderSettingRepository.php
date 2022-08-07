<?php namespace App\Repositories;

use App\Models\OrderSetting;

class OrderSettingRepository {

    public function getByOrder($accountId = 1, $order_number ){
       return OrderSetting::where('account_id', $accountId)->where('order_number', $order_number)->first();;
    }
}