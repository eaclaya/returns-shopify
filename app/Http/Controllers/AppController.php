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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;


class AppController extends Controller
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

	public function start(){
		return inertia('Index');
	}

	public function showOrderSearch(){
		Session::forget('CUSTOMER_TOKEN');
		Session::forget('REQUEST_TOKEN');
		return inertia('App');
	}
	
	public function showOrderReview($code = null){
		$accountId = Utils::getAccountId($code);
		$accountSetting = $this->settingRepo->getAccountSetting($accountId);
		$refundTypes = $this->refundRepo->getAvailableRefundTypes($accountId);
		$accountSettings = cache('accountSettings') ? cache('accountSettings') : [];  
		$accountSettings[$code] = $accountSetting;
		cache(['accountSettings' => $accountSettings]);
		$destinations = $this->destinationRepo->all($accountId);
		return inertia('Order', ['isAdmin' => false, 'destinations' => $destinations, 'refundTypes' => $refundTypes, 'accountSetting' => $accountSetting]);
	}

	public function getOrder(Request $request, $code = null){
		$validator = Validator::make($request->all(), [
			'order_id' => 'required',
			'skus' => 'required',
		]);	
		if($validator->fails()){
			return ['error' => true, 'msg' => 'Order data not found.' ];
		}
		$accountId = Utils::getAccountId($code);
		$order_number  = $request->order_number;
		$requests = $this->requestRepo->getByOrder($accountId, $order_number);
		$surveys = $this->surveyRepo->getByActiveStatus();
		$products = $this->accountProductRepo->getProductsByParent($request->ids, true, $accountId);
		return ['requests' => $requests, 'products' => $products, 'surveys' => $surveys];

	}

	public function getOrderByToken($code, $token){
		$requestToken = $this->requestTokenRepo->getByToken($token);
		if(!$requestToken){
			abort(403);
		}
		$order = $this->accountOrderService->getByOrderAndEmail($requestToken->order_number, $requestToken->email, $code);
		if(!$order){ return ['error' => true, 'msg' => 'Order not found. Error: 501']; }

		$order['name'] = $requestToken->order_number;
		$this->requestTokenRepo->disableToken($token);
		$currencies = cache('currencies') ? cache('currencies') : [];
		if(isset($currencies[$order['currency']])){
			$order['currency'] = $currencies[$order['currency']]['symbol'];
		}
		$isAdmin = true;
		Session::put('CUSTOMER_TOKEN', $requestToken->email);
		Session::put('ACCOUNT_ID', $requestToken->account_id);
		Session::put('REQUEST_TOKEN', $token);
		return inertia('Orders/Items', compact('order', 'isAdmin'));
	}

	public function showOrderDetails(){
		return inertia('Orders/Details');
	}

	public function setCustomerToken(Request $request){
		if($request->email){
			Session::put('CUSTOMER_TOKEN', $request->email);
			Session::put('ACCOUNT_ID', 1);
			return response()->json(['success' => true, 'msg' => 'Customer token set.']);
		}
		return response()->json(['error' => 'Customer token not set.']);
	}

	public function removeCustomerToken(Request $request){
		Session::forget('CUSTOMER_TOKEN');
	}

	public function getProducts(Request $request, $code = null){
		$validator = Validator::make($request->all(), [
			'skus' => 'required',
		]);	
		if($validator->fails()){
			return ['error' => true, 'msg' => 'Products not found.' ];
		}
		$accountId = Utils::getAccountId($code);
		$skus = $request->skus;
		$products = $this->accountProductRepo->getProductsByParent($request->ids, true, $accountId);
		return ['success' => true, 'response' => $products];
	}

	public function filterProducts(Request $request, $code = null){
		if(!$request->collection_id){
			return [];
		}
		$accountId = Utils::getAccountId($code);
		$collectionProducts = $this->accountProductRepo->getProductsByCollection($accountId, $request->collection_id);
		$products = [];
		foreach($collectionProducts as $item){
			if($item->product && $item->product->status == 1 && strpos(strtolower($item->product->tags), 'rma_exclude') === false){
				$products[] = $item->product;
			}
		}
		return $products;
	}


	

}
