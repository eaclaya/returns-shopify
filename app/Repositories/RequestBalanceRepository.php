<?php

namespace App\Repositories;

use App\Models\AccountOrder;
use App\Models\AccountSetting;
use App\Models\RequestItemBalance;
use App\Models\RequestBalance;

class RequestBalanceRepository
{


    public function all()
    {
        return RequestBalance::where('deleted_at', null)->get();
    }

    public function save($data)
    {
        $accountId = isset($data['account_id']) ? $data['account_id'] : 1;
        $accountSetting = AccountSetting::where('account_id', $accountId)->first();
        $orderNumber = str_replace('#', '', $data['name']);
        if($accountSetting && $accountSetting->order_prefixes){
            $orderPrefixes = explode(',', strtoupper($accountSetting->order_prefixes));
            $orderNumber = str_replace($orderPrefixes, '', strtoupper($orderNumber));
        }
        $request = RequestBalance::where('account_id', $accountId)->where('original_order_number', $orderNumber)->first();
        if (!$request) {
            $request = new RequestBalance();
        }
        $order = AccountOrder::with('items')->where('account_id', $accountId)->where('order_number', $orderNumber)->first();
        $remains_qty = 0;
        if($order && $order->items){
            foreach ($order->items as $item) {
                $remains_qty += $item->quantity;
            }
        }
        $request->account_id = $accountId;
        $request->shipping_description = isset($data['shipping_description']) ? $data['shipping_description'] : null;
        $request->original_order_id = isset($data['id']) ? $data['id'] : null;
        $request->original_order_number = $orderNumber;
        $request->original_grand_total = isset($data['total_price']) ? $data['total_price'] : 0;
        $request->original_shipping_amount = isset($data['total_shipping_price_set']) ? $data['total_shipping_price_set']['presentment_money']['amount'] : 0;
        $request->original_shipping_tax_amount = isset($data['shipping_tax_amount']) ? $data['shipping_tax_amount'] : 0;
        $request->original_tax_amount = isset($data['tax_amount']) ? $data['tax_amount'] : 0;
        $request->original_coupon_code = isset($data['coupon_code']) ? $data['coupon_code'] : null;
        $request->original_gift_credit_amount = isset($data['gift_credit_amount']) ? $data['gift_credit_amount'] : 0;
        $request->original_discount_amount = isset($data['total_discounts']) ? $data['total_discounts'] : 0;
        $request->original_items_qty = $remains_qty;
        $request->gift_credit_balance = isset($data['gift_credit_amount']) ? $data['gift_credit_amount'] : 0;
        $request->shipping_amount_balance = isset($data['total_shipping_price_set']) ? $data['total_shipping_price_set']['presentment_money']['amount'] : 0;
        $request->tax_amount_balance = isset($data['tax_amount']) ? $data['tax_amount'] : 0;
        $request->total_refunded = isset($data['total_refunded']) ? $data['total_refunded'] : 0;
        $request->grand_total_balance = isset($data['total_price']) ? $data['total_price'] : 0;
        $request->shipping_max = isset($data['shipping_max']) ? $data['shipping_max'] : 0;
        $request->remains_qty = $remains_qty;
        $request->cron_executed = isset($data['cron_executed']) ? $data['cron_executed'] : 0;
        $request->additional_data = isset($data['additional_data']) ? $data['additional_data'] : null;
        $request->save();
        $itemsCount = 0;
        if (isset($data['line_items'])) {
            $items = $data['line_items'];
            foreach ($items as $item) {
                $requestItem = RequestItemBalance::where('account_id', $accountId)->where('original_order_id', $request->original_order_id)->where('item_id', $item['id'])->first();
                if (!$requestItem) {
                    $requestItem = new RequestItemBalance();
                }
                $requestItem->account_id = $accountId;
                $requestItem->original_order_id = isset($data['id'])  ? $data['id'] : null;
                $requestItem->original_order_number = $request->original_order_number;
                $requestItem->item_id = isset($item['id'])  ? $item['id'] : null;
                $requestItem->product_id = isset($item['product_id'])  ? $item['product_id'] : 0;
                $requestItem->variant_id = isset($item['variant_id'])  ? $item['variant_id'] : 0;
                $requestItem->is_active = 1;
                $requestItem->product_type = isset($item['product_type'])  ? $item['product_type'] : null;
                $requestItem->sku = isset($item['sku'])  ? $item['sku'] : null;
                $requestItem->sku_variant = isset($item['variant_sku'])  ? $item['variant_sku'] : null;
                $requestItem->name = isset($item['name'])  ? $item['name'] : null;
                $requestItem->qty_ordered = isset($item['quantity'])  ? $item['quantity'] : 0;
                $requestItem->qty_balance = isset($item['quantity'])  ? $item['quantity'] : 0;
                $requestItem->price = isset($item['price'])  ? $item['price'] : 0;
                $requestItem->row_total = $requestItem->qty_ordered * $requestItem->price;
                $requestItem->total_refunded = isset($item['total_refunded'])  ? $item['total_refunded'] : 0;
                $requestItem->discount_amount = isset($item['total_discount'])  ? $item['total_discount'] : 0;
                $requestItem->tax_amount = isset($item['tax_amount'])  ? $item['tax_amount'] : 0;
                $requestItem->discount_balance = isset($item['total_discount'])  ? $item['total_discount'] : 0;
                $requestItem->tax_balance = isset($item['tax_amount'])  ? $item['tax_amount'] : 0;
                $requestItem->save();
                $itemsCount += intval($requestItem->qty_ordered);
            }
            $request->original_items_qty = $itemsCount;
            $request->remains_qty = $itemsCount;
            $request->save();
        }
        return $request;
    }

    public function update($request)
    {   
        $order_number = ($request->order_id == $request->refund_order_id) ? $request->order_number : $request->final_order_id;
        $exchange_grand_total = 0.00;
        $requestBalance = RequestBalance::where('account_id', $request->account_id)->where('original_order_number', $order_number)->firstOrFail();
            
        $grandTotalBalance = $requestBalance->grand_total_balance;
        $giftCreditBalance = $requestBalance->gift_credit_balance;
        $totalRefunded = $requestBalance->total_refunded;
        $shippingAmountBalance = $requestBalance->shipping_amount_balance;
        $total_return = $request->total_return;
        $remains_qty = $request->remains_qty;
        $newShippingBalance = floatval($shippingAmountBalance) + floatval($request->shipping_amount);
        $newTotalRefunded = $totalRefunded + $total_return;
        $newGiftCreditBalance = 0;
        if ($giftCreditBalance > 0) {
            $newGiftCreditBalance = $giftCreditBalance - $total_return;
            if ($newGiftCreditBalance < 0) {
                $total_return = -$newGiftCreditBalance;
                $newGiftCreditBalance = 0;
            } else {
                $total_return = 0;
            }
        }
        $newGrandTotalBalance = floatval($grandTotalBalance) - floatval($total_return) + floatval($exchange_grand_total) - ($newShippingBalance - floatval($newShippingBalance));

        $requestBalance->grand_total_balance = $newGrandTotalBalance;
        $requestBalance->shipping_amount_balance = $newShippingBalance;
        $requestBalance->remains_qty = $remains_qty;
        $requestBalance->total_refunded = $newTotalRefunded;
        $requestBalance->gift_credit_balance = $newGiftCreditBalance;
        $requestBalance->save();
        $this->updateItems($requestBalance, $request);
    }

    protected function updateItems($requestBalance, $request){
    
        $orderId = ($request->order_id == $request->refund_order_id) ? $request->order_id : $request->final_order_id;
        
        foreach($request->items as $item){
            $_item = RequestItemBalance::where('account_id', $request->account_id)->where('original_order_id', $orderId)->where('sku', $item->original_sku)->firstOrFail();
            $_item->qty_balance = intval($_item->qty_balance) - intval($item->qty_return);
            $_item->is_active = $_item->qty  == 0 ? false : true;
            $_item->save();
            if($item->transaction_type == 'exchange'){
                $newItem = new RequestItemBalance();
                $newItem->account_id = $item->account_id;
                $newItem->original_order_id = $item->original_order_id;
                $newItem->original_order_number = $item->original_order_number;
                $newItem->item_id = $item->order_item_id ? $item->order_item_id : 0;
                $newItem->product_id = $item->new_product_id ? $item->new_product_id : 0;
                $newItem->variant_id = $item->new_product_id ? $item->new_product_id : 0;
                $newItem->is_active = true;
                $newItem->product_type = $item->product_type;
                $newItem->sku = $item->new_sku;
                $newItem->sku_variant = $item->sku_variant;
                $newItem->name = $item->new_name;
                $newItem->qty_ordered = $item->qty_exchange;
                $newItem->qty_balance = $item->qty_exchange;
                $newItem->price = $item->new_price;
                $newItem->row_total = $item->new_row_total;
                $newItem->total_refunded = $item->total_refunded ? $item->total_refunded : 0;
                $newItem->discount_amount = $item->new_discount ? $item->discount_amount : 0;
                $newItem->tax_amount = $item->new_tax_amount ? $item->new_tax_amount : 0;
                $newItem->discount_balance = $item->new_discount ? $item->new_discount : 0;
                $newItem->tax_balance = $item->new_tax_amount ?  $item->new_tax_amount : 0;
                $newItem->save();
            }
            
        }

}
}
