<?php namespace App\Repositories;
use App\Libraries\Utils;
use App\Models\AccountOrder;
use App\Models\AccountSetting;
use App\Models\OrderSetting;
use App\Models\Request;
use App\Models\RequestBalance;
use App\Models\RequestItem;
use App\Models\RequestShippingAddress;
use App\Models\RequestTransaction;
use App\Models\SurveyResponse;

class RequestRepository {


    public function getByOrder($accountId = 1, $orderNumber = null){
        return Request::with('items')
                        ->where('account_id', $accountId)->where('is_active', 1)
                        ->where(function($query) use ($orderNumber){
			                $query->where('order_number', $orderNumber)->orWhere('final_order_id', $orderNumber);
		                })
                        ->get();

    }
    public function findByOrder($accountId = 1, $orderId = null){
        return Request::where('account_id', $accountId)->where('order_id', $orderId)->first();

    }
    public function getBalanceByOrder($accountId = 1, $orderNumber = null){
        return RequestBalance::with('items')->where('account_id', $accountId)->where('original_order_number', $orderNumber)->first();

    }
    public function save($data){
        $request = new Request();
        $accountId = isset($data['account_id']) ? $data['account_id'] : 1;
        if(isset($data['id'])){
            $request = Request::find($data['id']);
        }
        $accountSetting = AccountSetting::where('account_id', $accountId)->first();
        $orderNumber = str_replace('#', '', $data['order_number']);
        if($accountSetting && $accountSetting->order_prefixes){
            $orderPrefixes = explode(',', strtoupper($accountSetting->order_prefixes));
            $orderNumber = str_replace($orderPrefixes, '', strtoupper($orderNumber));
        }
        $request->transaction_number = strtoupper(substr(hash('sha256', mt_rand() . microtime()), 0, 20));
        $request->account_id = $accountId;
        $request->confirmed_return = isset($data['confirmed_return']) ? $data['confirmed_return'] : null;
        $request->confirmed_exchange = isset($data['confirmed_exchange']) ? $data['confirmed_exchange'] : null;
        $request->transaction_type = isset($data['transaction_type']) ? $data['transaction_type'] : null;
        $request->state_administrator = isset($data['state_administrator']) ? $data['state_administrator'] : 'New Request';
        $request->status_administrator = isset($data['status_administrator']) ? $data['status_administrator'] : 'New Request';
        $request->customer_id = isset($data['customer_id']) ? $data['customer_id'] : null;
        $request->firstname = isset($data['firstname']) ? $data['firstname'] : null;
        $request->lastname = isset($data['lastname']) ? $data['lastname'] : null;
        $request->order_id = isset($data['order_id']) ? $data['order_id'] : null;
        $request->order_number = $orderNumber;
        $request->refund_order_id = isset($data['refund_order_id']) ? $data['refund_order_id'] : null;
        $request->final_order_id = isset($data['final_order_id']) ? $data['final_order_id'] : null;
        $request->is_active = isset($data['is_active']) ? $data['is_active'] : true;
        $request->customer_email = isset($data['customer_email']) ? $data['customer_email'] : null;
        $request->total_return = isset($data['total_return']) ? $data['total_return'] : 0;
        $request->total_charge = isset($data['total_charge']) ? $data['total_charge'] : 0;
        $request->total_return_original = isset($data['total_return_original']) ? $data['total_return_original'] : 0;
        $request->order_credit_amount = isset($data['order_credit_amount']) ? $data['order_credit_amount'] : 0;
        $request->restocking_fee = isset($data['restocking_fee']) ? $data['restocking_fee'] : 0;
        $request->additional_discount = isset($data['additional_discount']) ? $data['additional_discount'] : 0;
        $request->original_tax_amount = isset($data['original_tax_amount']) ? $data['original_tax_amount'] : 0;
        $request->original_tax_rate = isset($data['original_tax_rate']) ? $data['original_tax_rate'] : 0;
        $request->original_grand_total = isset($data['original_grand_total']) ? $data['original_grand_total'] : 0;
        $request->original_subtotal = isset($data['original_subtotal']) ? $data['original_subtotal'] : 0;
        $request->original_coupon_code = isset($data['original_coupon_code']) ? $data['original_coupon_code'] : null;
        $request->original_discount_amount = isset($data['original_discount_amount']) ? $data['original_discount_amount'] : 0;
        $request->original_shipping_amount = isset($data['original_shipping_amount']) ? $data['original_shipping_amount'] : 0;
        $request->restocking_charge = isset($data['restocking_charge']) ? $data['restocking_charge'] : 0;
        $request->original_qty = isset($data['original_qty']) ? $data['original_qty'] : 0;
        $request->qty_return = isset($data['qty_return']) ? $data['qty_return'] : 0;
        $request->qty_exchange = isset($data['qty_exchange']) ? $data['qty_exchange'] : 0;
        $request->remains_qty = isset($data['remains_qty']) ? $data['remains_qty'] : 0;
        $request->new_tax_amount = isset($data['new_tax_amount']) ? $data['new_tax_amount'] : 0;
        $request->new_tax_rate = isset($data['new_tax_rate']) ? $data['new_tax_rate'] : 0;
        $request->new_grand_total = isset($data['new_grand_total']) ? $data['new_grand_total'] : 0;
        $request->new_subtotal = isset($data['new_subtotal']) ? $data['new_subtotal'] : 0;
        $request->new_discount_amount = isset($data['new_discount_amount']) ? $data['new_discount_amount'] : 0;
        $request->new_shipping_amount = isset($data['new_shipping_amount']) ? $data['new_shipping_amount'] : 0;
        $request->approval_date = isset($data['approval_date']) ? $data['approval_date'] : null;
        $request->refund_type_id = isset($data['refund_type_id']) ? $data['refund_type_id'] : 2;
        $request->bonus_discount = isset($data['bonus_discount']) ? $data['bonus_discount'] : null;
        $request->giftcard_amount = isset($data['giftcard_amount']) ? $data['giftcard_amount'] : null;
        $request->remote_ip = $_SERVER['REMOTE_ADDR'];
        $request->tags = isset($data['tags']) ? $data['tags'] : null;
        $request->save();
        $order = AccountOrder::where('account_id', $request->account_id)->where('name', $orderNumber)->first();
        $this->saveItems($request, $data['items']);
        if($request->transaction_type != 'return'){
            $this->saveRequestAddress($request, $data);
        }
        if($request->additional_discount > 0){
            $this->updateAdditionalDiscount($request);
        }
        if($order){
            $this->saveRequestTransaction($request, $order);
        }
        $request->items = $request->items;
        return $request;
    }

    protected function saveItems(&$request, $items){
        if(isset($items) && is_array($items)){
            foreach($items as $data){
                $item = new RequestItem();
                $item->account_id = $request->account_id;
                $item->request_id = $request->id;
                $item->transaction_type = isset($data['transaction_type']) ? $data['transaction_type'] : null;
                $item->original_order_id = isset($data['original_order_id']) ? $data['original_order_id'] : null;
                $item->original_order_number = $request->order_number;
                $item->order_item_id = isset($data['order_item_id']) ? $data['order_item_id'] : null;
                $item->item_key = isset($data['item_key']) ? $data['item_key'] : null;
                $item->original_product_id = isset($data['original_product_id']) ? $data['original_product_id'] : null;
                $item->original_variant_id = isset($data['original_variant_id']) ? $data['original_variant_id'] : null;
                $item->original_sku = isset($data['original_sku']) ? $data['original_sku'] : null;
                $item->original_price = isset($data['original_price']) ? $data['original_price'] : 0;
                $item->original_discount = isset($data['original_discount']) ? $data['original_discount'] : 0;
                $item->original_row_total = isset($data['original_row_total']) ? $data['original_row_total'] : 0;
                $item->original_tax_amount = isset($data['original_tax_amount']) ? $data['original_tax_amount'] : 0;
                $item->original_qty = isset($data['original_qty']) ? $data['original_qty'] : 0;
                $item->original_name = isset($data['original_name']) ? $data['original_name'] : null;
                $item->original_image = isset($data['original_image']) ? $data['original_image'] : null;
                $item->exclude = isset($data['exclude']) ? $data['exclude'] : false;
                $item->tags = isset($data['tags']) ? $data['tags'] : '';
                if($item->transaction_type == 'exchange'){
                    $item->new_product_id = isset($data['new_product_id']) ? $data['new_product_id'] : null;
                    $item->new_variant_id = isset($data['new_variant_id']) ? $data['new_variant_id'] : null;
                    $item->new_sku = isset($data['new_sku']) ? $data['new_sku'] : null;
                    $item->new_price = isset($data['new_price']) ? $data['new_price'] : 0;
                    $item->new_discount = isset($data['new_discount']) ? $data['new_discount'] : 0;
                    $item->new_tax_amount = isset($data['new_tax_amount']) ? $data['new_tax_amount'] : 0;
                    $item->new_name = isset($data['new_name']) ? $data['new_name'] : null;
                    $item->new_image = isset($data['new_image']) ? $data['new_image'] : null;
                    $item->new_qty = isset($data['new_qty']) ? $data['new_qty'] : 0;
                    $item->new_row_total = isset($data['new_row_total']) ? $data['new_row_total'] : 0;
                    $item->qty_exchange = isset($data['qty_exchange']) ? $data['qty_exchange'] : 0;
                }
                $item->qty_return = isset($data['qty_return']) ? $data['qty_return'] : 0;
                $item->save();
                $surveyItems = isset($data['survey_items']) ? $data['survey_items'] : null;
                $this->saveSurveyResponse($request, $item, $surveyItems);
            }
           
            $request->save();
        }
    }

    protected function saveSurveyResponse($request, $item, $surveyItems){
        if($request && $item && $surveyItems){
            $data = []; $surveyId = null;
            foreach($surveyItems as $surveyItem){
                $data[] = $surveyItem['type'] == 'textarea' ? $surveyItem['value'] : $surveyItem['label'];
                $surveyId = $surveyItem['survey_id'] == 0 ? 1 : $surveyItem['survey_id'];
            }
            $surveyResponse = new SurveyResponse();
            $surveyResponse->survey_id = isset($surveyId) ? $surveyId : 1;
            $surveyResponse->request_id = $request->id;
            $surveyResponse->return_item_id = $item->id;
            $surveyResponse->survey_item_id = isset($surveyItem['id']) ? $surveyItem['id'] : null;
            $surveyResponse->step_one = isset($data[0]) ? $data[0] : null;
            $surveyResponse->step_two = isset($data[1]) ? $data[1] : null;
            $surveyResponse->step_three = isset($data[2]) ? $data[2] : null;
            $surveyResponse->save();
        }

    }

    protected function getRequestTags($data, $request){
        $tags = [];
        $isAdmin = (isset($data['is_admin']) && $data['is_admin'] == true) ? true : false;
        $created_by_tag = $isAdmin ? Utils::CREATED_BY_AGENT : Utils::CREATED_BY_USER;
        $tags[] = $created_by_tag;
        if($request->bonus_discount > 0){
            $discount_by_tag = $isAdmin  ? Utils::DISCOUNT_BY_AGENT : Utils::DISCOUNT_BY_USER;
            $tags[] = $discount_by_tag;
        }
        if($request->giftcard_amount > 0){
            $giftcard_by_tag = $isAdmin  ? Utils::GIFTCARD_BY_AGENT : Utils::GIFTCARD_BY_USER;
            $tags[] = $giftcard_by_tag;
        }
        return implode(',', $tags);
    }

    protected function updateAdditionalDiscount($request){
        $settings = OrderSetting::where('order_id', $request->order_id)->first();
        if($settings){
            $settings->additional_exchange_discount = 0;
            $settings->save();
        }
    }


    protected function saveRequestAddress($request, $data, $type = 'shipping'){
        if($request->transaction_type == 'return'){ return; }
        $address = RequestShippingAddress::where('account_id', $request->account_id)->where('request_id', $request->id)->where('address_type', $type)->first();
        if(!$address){
            $address = new RequestShippingAddress();
        }
        $address->account_id = $request->account_id;
        $address->request_id = $request->id;
        $address->order_id = $request->order_id;
        $address->address_type = $type;
        $address->zip = isset($data['shipping_address']) ? $data['shipping_address']['zip'] : null;
        $address->city = isset($data['shipping_address']) ? $data['shipping_address']['city'] : null;
        $address->name = isset($data['shipping_address']) ? $data['shipping_address']['name'] : null;
        $address->phone = isset($data['shipping_address']) ? $data['shipping_address']['phone'] : null;
        $address->company = isset($data['shipping_address']) ? $data['shipping_address']['company'] : null;
        $address->country = isset($data['shipping_address']) ? $data['shipping_address']['country'] : null;
        $address->address1 = isset($data['shipping_address']) ? $data['shipping_address']['address1'] : null;
        $address->address2 = isset($data['shipping_address']) ? $data['shipping_address']['address2'] : null;
        $address->province = isset($data['shipping_address']) ? $data['shipping_address']['province'] : null;
        $address->last_name = isset($data['shipping_address']) ? $data['shipping_address']['last_name'] : null;
        $address->first_name = isset($data['shipping_address']) ? $data['shipping_address']['first_name'] : null;
        $address->country_code = isset($data['shipping_address']) ? $data['shipping_address']['country_code'] : null;
        $address->province_code = isset($data['shipping_address']) ? $data['shipping_address']['province_code'] : null;
        $address->save();
        return $address;
    }

    protected function saveRequestTransaction($request, $order){
        $totalReturnOriginal = $request->total_return_original;
        foreach($order->transactions as $_transaction){
            if($_transaction->kind == 'sale' || $_transaction->kind == 'capture'){
                $transaction = new RequestTransaction();
                $transaction->account_id = $order->account_id;
                $transaction->request_id = $request->id;
                $transaction->order_number = $order->name;
                $transaction->order_id = $order->order_id;
                $transaction->transaction_id = $_transaction->transaction_id;
                $transaction->gateway = $_transaction->gateway;
                $transaction->amount = $_transaction->amount;
                $transaction->balance = $_transaction->amount;
                $transaction->source_name = $_transaction->source_name;
                $transaction->status = 'not_executed';
                $transaction->amount_to_refund = $totalReturnOriginal;
                $transaction->save();
                $totalReturnOriginal = 0;
            }
        }
    }
}