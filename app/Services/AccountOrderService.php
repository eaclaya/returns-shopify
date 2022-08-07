<?php namespace App\Services;

use App\Libraries\Utils;
use Shopify\Rest\Admin2021_10\Order;
use Shopify\Rest\Admin2021_10\Transaction;
use Shopify\Exception\RestResourceRequestException;

class AccountOrderService {

    public function fetchOne($orderId, $code = null){
        if(!$orderId){
            return 'Order id is not valid';
        }
        $session = Utils::getAccountSession($code);
        try{
            $order = Order::find($session,$orderId);
            return $order->toArray();
        }catch(RestResourceRequestException $e){
            return 'Order not found';
        }
    }

    public function fetchTransactions($orderId, $code = null){
        
        if(!$orderId){
            return 'Order id is not valid';
        }
        $session = Utils::getAccountSession($code);
        try{
            $transactions = Transaction::all($session,['order_id' => $orderId]);
            return $transactions;
        }catch(RestResourceRequestException $e){
            return 'Transactions not found';
        }
        
    }

    public function getByOrderAndEmail($order_number = null, $email = null, $code = null){
        $session = Utils::getAccountSession($code);
        $response = Order::all($session, [], ["status" => "any", 'name' => $order_number, 'fields' => 'line_items,id,order_number,name,email,customer,created_at,financial_status,total_price,discount_applications,subtotal_price,processed_at,tags,tax_lines,total_discounts,total_discounts_set,total_price,total_tax,shipping_address,currency,taxes_included']);
		$order = null;
		if(count($response) > 0){
			foreach($response as $item){
				if(strtolower($item->email) == strtolower($email)){
					$order = $item->toArray();
					break;
				}
			}
		}
        return $order;
    }
}