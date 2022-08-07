<?php
namespace App\Repositories;

use App\Models\AccountSetting;
use App\Models\OrderSetting;
use App\Models\ShippingSetting;

class SettingRepository {

     public function getOrderSetting($accountId = 1, $order_number ){
       return OrderSetting::where('account_id', $accountId)->where('order_number', $order_number)->first();;
    }

    public function getAccountSetting($accountId = 1){
        return AccountSetting::where('account_id', $accountId)->first();
    }
    public function getShippingSetting($accountId = 1, $order){
        $shippingSettings = ShippingSetting::where('account_id', $accountId)->where('is_active', true)->get();
        foreach($shippingSettings as $key => &$shippingSetting){
            $conditions = '';
            $shippingSetting->conditions = json_decode($shippingSetting->conditions);
            foreach($shippingSetting->conditions as $condition){
                if($condition->key == 'tags'){
                    if(strpos($order->tags, $condition->value) === false){
                        unset($shippingSettings[$key]);
                    }
                }
                $nextOperator = isset($condition->next_operator) ? $condition->next_operator : '';
                $value = is_numeric($condition->value) ? floatval($condition->value) : strtoupper("'{$condition->value}'");
                $condition->key = str_replace('total_price', 'newGrandTotalBalance', $condition->key);
                if(strpos($condition->operator, 'includes') !== false){
                    $result = $condition->operator == 'includes' ? true : false;
                    $condition->operator = 'includes';
                    $conditions .= " {$condition->key}.toUpperCase().{$condition->operator}({$value}) == {$result} {$nextOperator}";
                }else{
                    $conditions .= " {$condition->key} {$condition->operator} {$value} {$nextOperator}";
                }
            }
            $shippingSetting->conditions = trim($conditions);
        }
        $shippingSetting = $shippingSettings->first() ? $shippingSettings->first() : [];
        return $shippingSetting;
    }

}