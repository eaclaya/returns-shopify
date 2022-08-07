<?php namespace App\Repositories;
use Auth;
use DB;
use App\Libraries\Utils;
use App\Models\AccountCustomer;

class AccountCustomerRepository {


    public function all(){
        return AccountCustomer::where('deleted_at', null)->get();
    }


    public function get($filters = []){
        $query = DB::table('account_customers');
        if(isset($filters['account_id'])){
            $query->where('account_id', $filters['account_id']);
        }
        if(isset($filters['customer_id'])){
            $query->where('customer_id', $filters['customer_id']);
        }
        if(isset($filters['email']) || isset($filters['first_name']) || isset($filters['last_name'])){
            $query->where(function($query) use($filters) {
                $query->where('email', 'LIKE', "%".$filters['email']."%")
                    ->orWhere('first_name', 'LIKE', "%".$filters['first_name']."%")
                    ->orWhere('last_name', 'LIKE', "%".$filters['last_name']."%");
            });
        }
        return $query->get();
    }

    public function save($data){
        $accountId = isset($data['account_id']) ? $data['account_id'] : 1;
        $customer = AccountCustomer::where('account_id', $accountId)->where('customer_id', $data['id'])->first();
        if(!$customer){
            $customer = new AccountCustomer();
        }
        $customer->account_id = $accountId;
        $customer->email = isset($data['email']) ? $data['email'] : null;
        $customer->first_name = isset($data['first_name']) ? $data['first_name'] : null;
        $customer->customer_id = isset($data['id']) ? $data['id'] : null;
        $customer->last_name = isset($data['last_name']) ? $data['last_name'] : null;
        $customer->last_order_id = isset($data['last_order_id']) ? $data['last_order_id'] : null;
        $customer->last_order_name = isset($data['last_order_name']) ? $data['last_order_name'] : null;
        $customer->orders_count = isset($data['orders_count']) ? $data['orders_count'] : null;
        $customer->phone = isset($data['phone']) ? $data['phone'] : null;
        $customer->state = isset($data['state']) ? $data['state'] : null;
        $customer->tags = isset($data['tags']) ? json_encode($data['tags']) : null;
        $customer->total_spent = isset($data['total_spent']) ? $data['total_spent'] : null;
        $customer->default_address = isset($data['default_address']) ? json_encode($data['default_address']) : null;
        $customer->addresses = isset($data['addresses']) ? json_encode($data['addresses']) : null;
        $customer->save();
        return $customer;
    }
}