<?php namespace App\Repositories;
use Auth;
use DB;
use App\Libraries\Utils;
use App\Models\AccountDraftOrder;
use App\Models\AccountOrderItem;
use App\Models\AccountOrderAddress;
use App\Models\AccountOrder;
use App\Models\AccountOrderTransaction;
use App\Models\AccountSetting;

class AccountOrderRepository {


    public function all(){
        return AccountOrder::where('deleted_at', null)->get();
    }
    public function getDraftOrderById($accountId, $orderId){
        return AccountDraftOrder::where('account_id', $accountId)->where('new_order_id', $orderId)->first();
    }
    public function getByOrderNumber($accountId = 1, $orderNumber = ''){
        return AccountOrder::with('customer')->where('account_id', $accountId)->where('order_number', $orderNumber)->first();
    }
    public function save($data, $transactions = []){
        $accountId = isset($data['account_id']) ? $data['account_id'] : 1;
        $order = AccountOrder::where('account_id', $accountId)->where('order_id', $data['id'])->first();
        $isNew = false;
        if(!$order){
            $isNew = true;
            $order = new AccountOrder();
        }
        $accountSetting = AccountSetting::where('account_id', $order->account_id)->first();
        $orderNumber = str_replace('#', '', $data['order_number']);
        if($accountSetting && $accountSetting->order_prefixes){
            $orderPrefixes = explode(',', strtoupper($accountSetting->order_prefixes));
            $orderNumber = str_replace($orderPrefixes, '', strtoupper($orderNumber));
        }
        $order->account_id = $accountId;
        $order->order_id = isset($data['id']) ? $data['id'] : null;
        $order->order_number = $orderNumber;
        $order->name = isset($data['name']) ? str_replace('#', '', $data['name']) : null;
        $order->closed_at = isset($data['closed_at']) ? $data['closed_at'] : null;
        $order->currency = isset($data['currency']) ? $data['currency'] : null;
        $order->current_total_discounts = isset($data['current_total_discounts']) ? $data['current_total_discounts'] : 0;
        $order->current_total_price = isset($data['current_total_price']) ? $data['current_total_price'] : null;
        $order->current_subtotal_price = isset($data['current_subtotal_price']) ? $data['current_subtotal_price'] : 0;
        $order->current_total_tax = isset($data['current_total_tax']) ? $data['current_total_tax'] : 0;
        $order->customer_id = isset($data['customer']) ? $data['customer']['id'] : 0;
        $order->email = isset($data['email']) ? $data['email'] : null;
        $order->financial_status = isset($data['financial_status']) ? $data['financial_status'] : null;
        $order->fulfillment_status = isset($data['fulfillment_status']) ? $data['fulfillment_status'] : null;
        $order->gateway = isset($data['gateway']) ? $data['gateway'] : null;
        $order->transaction_id = isset($data['transaction_id']) ? $data['transaction_id'] : null;
        $order->phone = isset($data['phone']) ? $data['phone'] : null;
        $order->processed_at = isset($data['processed_at']) ? $data['processed_at'] : null;
        $order->processing_method = isset($data['processing_method']) ? $data['processing_method'] : null;
        $order->subtotal_price = isset($data['subtotal_price']) ? $data['subtotal_price'] : 0;
        $order->total_weight = isset($data['total_weight']) ? $data['total_weight'] : 0;
        $order->total_tax = isset($data['total_tax']) ? $data['total_tax'] : 0;
        $order->user_id = isset($data['user_id']) ? $data['user_id'] : null;
        $order->total_outstanding = isset($data['total_outstanding']) ? $data['total_outstanding'] : 0;
        $order->total_price = isset($data['total_price']) ? $data['total_price'] : 0;
        $order->total_line_items_price = isset($data['total_line_items_price']) ? $data['total_line_items_price'] : 0;
        $order->total_discounts = isset($data['total_discounts']) ? $data['total_discounts'] : 0;
        $order->shipping_amount = isset($data['total_shipping_price_set']) ? $data['total_shipping_price_set']['presentment_money']['amount'] : 0;
        $order->tags = isset($data['tags']) ? $data['tags'] : null;
        $order->save();
        if(isset($data['line_items'])){
            $items = $data['line_items'];
            foreach($items as $item){
                $orderItem = AccountOrderItem::where('account_id', $accountId)->where('order_id', $order->order_id)->where('item_id', $item['id'])->first();
                if(!$orderItem){
                    $orderItem = new AccountOrderItem();
                }
                $orderItem->order_id = isset($order->order_id) ? $order->order_id : null;
                $orderItem->account_id = isset($accountId) ? $accountId : null;
                $orderItem->item_id = isset($item['id']) ? $item['id'] : null;
                $orderItem->sku = isset($item['sku']) ? $item['sku'] : null;
                $orderItem->name = isset($item['name']) ? $item['name'] : null;
                $orderItem->price = isset($item['price']) ? $item['price'] : 0;
                $orderItem->title = isset($item['title']) ? $item['title'] : null;
                $orderItem->duties = isset($item['duties']) ? json_encode($item['duties']) : null;
                $orderItem->taxable = isset($item['taxable']) ? $item['taxable'] : null;
                $orderItem->quantity = isset($item['quantity']) ? $item['quantity'] : null;
                $orderItem->gift_card = isset($item['gift_card']) ? $item['gift_card'] : null;
                $orderItem->tax_lines = isset($item['tax_lines']) ? json_encode($item['tax_lines']) : null;
                $orderItem->product_id = isset($item['product_id']) ? $item['product_id'] : null;
                $orderItem->variant_id = isset($item['variant_id']) ? $item['variant_id'] : null;
                $orderItem->variant_title = isset($item['variant_title']) ? $item['variant_title'] : null;
                $orderItem->total_discount = isset($item['total_discount']) ? $item['total_discount'] : 0;
                $orderItem->requires_shipping = isset($item['requires_shipping']) ? $item['requires_shipping'] : null;
                $orderItem->fulfillment_status = isset($item['fulfillment_status']) ? $item['fulfillment_status'] : null;
                $orderItem->fulfillable_quantity = isset($item['fulfillable_quantity']) ? $item['fulfillable_quantity'] : 0;
                $orderItem->save();
            }
        }
        if(isset($data['shipping_address'])){
            $addressData = $data['shipping_address'];
            $addressData['address_type'] = 'shipping_address';
            $this->saveOrderAddress($order, $addressData);
        }
        if(isset($data['billing_address'])){
            $addressData = $data['billing_address'];
            $addressData['address_type'] = 'billing_address';
            $this->saveOrderAddress($order, $addressData);
        }
        
        $this->saveTransactions($order, $transactions);
        
        return $order;
    }

    protected function saveOrderAddress($order, $data){
        $address = AccountOrderAddress::where('account_id', $order->account_id)->where('order_id', $order->order_id)->where('address_type', $data['address_type'])->first();
        if(!$address){
            $address = new AccountOrderAddress();
        }
        $address->account_id = $order->account_id;
        $address->order_id = $order->order_id;
        $address->address_type = isset($data['address_type']) ? $data['address_type'] : null;
        $address->zip = isset($data['zip']) ? $data['zip'] : null;
        $address->city = isset($data['city']) ? $data['city'] : null;
        $address->name = isset($data['name']) ? $data['name'] : null;
        $address->phone = isset($data['phone']) ? $data['phone'] : null;
        $address->company = isset($data['company']) ? $data['company'] : null;
        $address->country = isset($data['country']) ? $data['country'] : null;
        $address->address1 = isset($data['address1']) ? $data['address1'] : null;
        $address->address2 = isset($data['address2']) ? $data['address2'] : null;
        $address->province = isset($data['province']) ? $data['province'] : null;
        $address->last_name = isset($data['last_name']) ? $data['last_name'] : null;
        $address->first_name = isset($data['first_name']) ? $data['first_name'] : null;
        $address->country_code = isset($data['country_code']) ? $data['country_code'] : null;
        $address->province_code = isset($data['province_code']) ? $data['province_code'] : null;
        $address->save();
        return $address;
    }


    protected function saveTransactions($order, $transactions){
        $_transactions = [];
        foreach($transactions as $transaction){
            if(!is_array($transaction)){
                $transaction = $transaction->toArray();
            }
            $_transactions[$transaction['id']] = $transaction;
        }
        foreach($_transactions as $_transaction){
            if($_transaction['status'] == 'success'  &&  ($_transaction['kind'] == 'capture' || $_transaction['kind'] == 'sale' || $_transaction['kind'] == 'refund')){
                $transaction = AccountOrderTransaction::where('account_id', $order->account_id)->where('order_id', $order->order_id)->where('transaction_id', $_transaction['id'])->first();
                if(!$transaction){
                    $transaction = new AccountOrderTransaction();
                    $transaction->account_id = $order->account_id;
                    $transaction->order_id = $order->order_id;
                    $transaction->transaction_id = $_transaction['id'];
                }
                
                $transaction->kind = $_transaction['kind'];
                $transaction->status = $_transaction['status'];
                $transaction->gateway = $_transaction['gateway'];
                $transaction->processed_at = $_transaction['processed_at'];
                $transaction->amount = $_transaction['amount'];
                $transaction->parent_id = $_transaction['parent_id'];
                $transaction->source_name = $this->getParentSourceName($_transaction, $_transactions);
                $transaction->currency = $_transaction['currency'];
                $transaction->save();
            }
        }
    }

    protected function getParentSourceName($transaction, $transactions){
        $parentId = $transaction['parent_id'];
        $sourceName = $transaction['source_name'];
        while($parentId != null){
            if(isset($transactions[$parentId])){
                $parentTransaction = $transactions[$parentId];
                $parentId = $parentTransaction['parent_id'];
                $sourceName = $parentTransaction['source_name'];
            }else{
                $parentId = null;
            }
        }
        return $sourceName;
    }

}