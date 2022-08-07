<?php namespace App\Services;

use App\Libraries\Utils;
use Shopify\Rest\Admin2021_10\Customer;
use Shopify\Exception\RestResourceRequestException;
class AccountCustomerService {

    public function fetchOne($customer_id, $code = null){
        if(!$customer_id){
            return 'Customer id is not valid';
        }
        $session = Utils::getAccountSession($code);
        try{
            $customer = Customer::find($session,$customer_id);
            return $customer->toArray();
        }catch(RestResourceRequestException $e){
            return 'Customer not found';
        }
           
    }


}