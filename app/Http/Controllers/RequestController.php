<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Libraries\Utils;
use App\Repositories\AccountProductRepository;
use App\Repositories\RequestRepository;
use App\Repositories\DestinationRepository;
use App\Repositories\SettingRepository;
use App\Repositories\AccountOrderRepository;
use App\Repositories\RefundRepository;
use App\Repositories\SurveyRepository;
use App\Services\AccountOrderService;
use App\Events\RequestCreated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class RequestController extends Controller
{
    public function __construct(AccountProductRepository $accountProductRepo, RequestRepository $requestRepo, DestinationRepository $destinationRepo, AccountOrderService $accountOrderService, SettingRepository $settingRepo, AccountOrderRepository $accountOrderRepo, RefundRepository $refundRepo, SurveyRepository $surveyRepo){
		$this->accountProductRepo = $accountProductRepo;
		$this->requestRepo = $requestRepo;
		$this->destinationRepo = $destinationRepo;
		$this->accountOrderService = $accountOrderService;
		$this->settingRepo = $settingRepo;
		$this->accountOrderRepo = $accountOrderRepo;
		$this->refundRepo = $refundRepo;
		$this->surveyRepo = $surveyRepo;
	}

    	public function showRequestDetails(){
		return inertia('Requests/Details');
	}

	public function showRequestSuccess(){
		return inertia('Requests/Success');
	}

	public function showRequestReview(Request $_request, $code){
		$accountId = Utils::getAccountId($code);
		if($_request->order_number && $accountId){
			$order_number = $_request->order_number;
			$orderSetting = $this->settingRepo->getOrderSetting($accountId, $order_number);
			$order = $this->accountOrderRepo->getByOrderNumber($accountId, $order_number);
			$customer = $order && $order->customer ? $order->customer : null;
			$accountSetting = $this->settingRepo->getAccountSetting($accountId);
			$refundTypes = $this->refundRepo->getAvailableRefundTypes($accountId);
			$requests = $this->requestRepo->getByOrder($accountId, $order_number);
			$request = $requests->first();
			$shippingSetting = $this->settingRepo->getShippingSetting($accountId, $order);
			$exchangeOrder = $this->accountOrderRepo->getDraftOrderById($accountId, $order_number);
			$order_number = $exchangeOrder ? $exchangeOrder->source_order_number : $order_number;
			$order_number = str_replace('#', '', $order_number);
			$requestBalance = $this->requestRepo->getBalanceByOrder($accountId, $order_number);
			return inertia('Requests/Review', compact('orderSetting', 'accountSetting', 'shippingSetting', 'requestBalance', 'refundTypes', 'customer'));
		}
		return abort(402);
	}
    
    public function showRequestStep($code, $id){		
		$accountId = Utils::getAccountId($code);
		switch($id){
			case $id == 2:
				$productTypes = $this->accountProductRepo->getProductTypes($accountId);
				$collections = $this->accountProductRepo->getActiveCollections($accountId);
				return inertia('Requests/StepTwo', compact('productTypes', 'collections'));
			case $id == 3:
				return inertia('Requests/StepThree', ['isAdmin' => false]);
			case $id == 4:
				return inertia('Requests/StepFour');
			case $id == 5:
				return inertia('Requests/StepFive');
			case $id == 6:
				return inertia('Requests/StepSix');
			case $id == 7:
				return inertia('Requests/StepSeven');
			default:
				return inertia('Requests/StepOne');
		}
	}

	public function saveRequest(Request $_request, $code = null){
		$accountId = Utils::getAccountId($code);
		$request = $this->requestRepo->findByOrder($_request->order_number, $accountId);
		$hasRequestToken = Session::pull('REQUEST_TOKEN', null);
		if($request && !$hasRequestToken){
			return $request;
		} 
		
		$isAdmin = $hasRequestToken ? true : false;
		
		$data = $_request->all();
		$data['account_id'] = $accountId;
		$data['is_admin'] = $isAdmin;
		$response = $this->requestRepo->save($data);
		if(!$isAdmin && env('APP_ENV') == 'production'){
			RequestCreated::dispatch($response->toArray());
		}
		return $response;
	}

}
