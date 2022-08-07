<?php

namespace App\Listeners;

use App\Events\ShopifyOrderLoaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\AccountCustomerService;
use App\Repositories\AccountCustomerRepository;
use App\Repositories\AccountOrderRepository;
use App\Repositories\RequestBalanceRepository;
use App\Services\AccountOrderService;

class FetchCustomerData 
{
    public $delay = 5;
    
    protected $accountCustomerService;
    protected $accountOrderService;
    protected $accountCustomerRepo;
    protected $accountOrderRepo;
    protected $requestBalanceRepo;
    public function __construct(AccountCustomerService $accountCustomerService, AccountCustomerRepository 
    $accountCustomerRepo, AccountOrderRepository $accountOrderRepo, RequestBalanceRepository $requestBalanceRepo, AccountOrderService $accountOrderService){
        $this->accountCustomerService = $accountCustomerService;
        $this->accountCustomerRepo = $accountCustomerRepo;
        $this->accountOrderRepo = $accountOrderRepo;
        $this->requestBalanceRepo = $requestBalanceRepo;
        $this->accountOrderService = $accountOrderService;
    }    


    /**
     * Handle the event.
     *
     * @param  ShopifyOrderLoaded  $event
     * @return void
     */
    public function handle(ShopifyOrderLoaded $event)
    {
        $payload = $event->payload ? $event->payload : null;
        if(!$payload){ return; }
        $customer = $this->accountCustomerService->fetchOne($payload['customer_id'], $payload['code']);
        if(is_string($customer)){
            return ['error' => true, $customer];
        }
        $order = $this->accountOrderService->fetchOne($payload['order_id'], $payload['code']);
        $customer['account_id'] = $payload['account_id'];
        $this->accountCustomerRepo->save($customer);
        if(is_string($order)){
            return ['error' => true, $order];
        }
        $transactions = $this->accountOrderService->fetchTransactions($order['id'], $payload['code']);
        foreach($transactions as &$transaction){
            $transaction = $transaction->toArray();
            if($transaction['status'] == 'success' &&  ($transaction['kind'] == 'capture' || $transaction['kind'] == 'sale')){
                $order['gateway'] = $transaction['gateway'];
                $order['transaction_id'] = $transaction['id'];
                break;
            }
        }
        $order['account_id'] = $payload['account_id'];
        $this->accountOrderRepo->save($order, $transactions);
        $this->requestBalanceRepo->save($order);
    }
}
