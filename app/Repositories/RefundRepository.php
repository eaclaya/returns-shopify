<?php namespace App\Repositories;
use App\Libraries\Utils;
use App\Models\AccountRefundType;
use App\Models\RefundTypeRule;

class RefundRepository {
    public function getAvailableRefundTypes($accountId = null){
		$refundTypes = AccountRefundType::where('account_id', $accountId)->get()->keyBy('refund_type_id');
		$refundTypeRules = RefundTypeRule::where('account_id', $accountId)->where('is_active', true)->get()->keyBy('refund_type_id');
		if($refundTypeRules->first()){
			foreach($refundTypeRules as $item){
				$_conditions = '';
				$conditions = json_decode($item->conditions);
				foreach($conditions as $condition){
					$nextOperator = isset($condition->next_operator) ? $condition->next_operator : '';
					$value = is_numeric($condition->value) ? floatval($condition->value) : strtoupper("'{$condition->value}'");
					$condition->key = str_replace('total_price', 'newSubtotal', $condition->key);
					$condition->key = str_replace('item_price', 'itemPrice', $condition->key);
					$condition->key = str_replace('orders_count', 'ordersCount', $condition->key);
					if(strpos($condition->operator, 'includes') !== false){
						$result = $condition->operator == 'includes' ? true : false;
						$condition->operator = 'includes';
						$_conditions .= " {$condition->key}.toUpperCase().{$condition->operator}({$value}) == {$result} {$nextOperator}";
					}elseif(strpos($condition->operator, 'distinct') !== false){
						$result = $condition->operator == 'distinct' ? true : false;
						$condition->operator = 'includes';
						$_conditions .= " {$condition->key}.toUpperCase().{$condition->operator}({$value}) != {$result} {$nextOperator}";
					}else{
						$_conditions .= " {$condition->key} {$condition->operator} {$value} {$nextOperator}";
					}	
				}
				$refundTypes[$item->refund_type_id]->conditions = $_conditions;
				$refundTypes[$item->refund_type_id]->extra_amount = $item->extra_amount;
				$refundTypes[$item->refund_type_id]->action = $item->action;
			}
		}
		return $refundTypes;
	}
}