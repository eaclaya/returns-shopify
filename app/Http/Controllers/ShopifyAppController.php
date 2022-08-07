<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ShopifyOrderLoaded;
use App\Repositories\AccountProductRepository;
use App\Libraries\Utils;
use App\Models\AccountSetting;
use App\Services\AccountProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Shopify\Clients\Rest;
use Shopify\Exception\RestResourceRequestException;
use Shopify\Rest\Admin2021_10\Order;
use Shopify\Rest\Admin2021_10\Product;
use Shopify\Rest\Admin2021_10\DiscountCode;
use Shopify\Rest\Admin2021_10\PriceRule;

class ShopifyAppController extends Controller
{
	protected $productRepo;

	public function __construct(AccountProductRepository $productRepo, AccountProductService $productService){
		$this->productRepo = $productRepo;
		$this->productService = $productService;
	}
	

	public function getOrder(Request $request, $code){
		$validator = Validator::make($request->all(), [
            		'email' => 'required|email',
            		'order_number' => 'required'
		]);	
		if($validator->fails()){
			return ['error' => true, 'msg' => 'You need to complete all fields.' ];
		}
		
		$session = Utils::getAccountSession($code);
		
		$accountId = Utils::getAccountId($code);
		$accountSetting = AccountSetting::where('account_id', $accountId)->firstOrFail();
		$orderNumber = str_replace('#', '', strtoupper($request->order_number));
		$orderPrefixes = [];
		if($accountSetting){
			$orderPrefixes =  explode(',', strtoupper($accountSetting->order_prefixes));
		}
		$orderNumber = str_replace($orderPrefixes, '', $orderNumber);
		$order = null;
		$response = Order::all($session, [], ["status" => "any", 'name' => $orderNumber, 'fields' => 'line_items,id,order_number,name,email,customer,created_at,financial_status,fulfillment_status,total_price,discount_applications,subtotal_price,processed_at,tags,tax_lines,total_discounts,total_discounts_set,total_price,total_tax,shipping_address,currency,taxes_included']);
		
		if(count($response) > 0){
			foreach($response as $item){
				if(strtolower($item->email) == strtolower($request->email)){
					$order = $item->toArray();
					break;
				}
			}
		}
		if(!$order){ return ['error' => true, 'msg' => 'Order not found. Error: 501']; }
		$ids = [];
		foreach($order['line_items'] as $key => $item){
			if(!$item['requires_shipping']){
				unset($order['line_items'][$key]);
			}
			if(isset($item['product_id']) && $item['product_id']){
				$ids[] = $item['product_id'];
			}
		}
		$ids = implode(',', $ids);
		$products = $this->productService->fetchAll($ids, $code);
		if(is_array($products)){
			foreach($products as $product){
				$product = $product->toArray();
				$product['account_id'] = $accountId;
				$this->productRepo->saveProduct($product);
			}
		}
		
		$dayThreshold = $accountSetting && intval($accountSetting->day_threshold) > 0 ? intval($accountSetting->day_threshold) : 100;
		if($order['fulfillment_status'] != 'fulfilled' ){ return ['error' => true, 'msg' => 'This order is not fulfilled. Error: 503']; }
		if((time() - strtotime($order['processed_at']))/(60 * 60 * 24) > $dayThreshold){ return ['error' => true, 'msg' => 'This is order is not valid for exchange/refund. Error: 505']; }
		
		$currencies = cache('currencies') ? cache('currencies') : [];
		if(isset($currencies[$order['currency']])){
			$order['currency'] = $currencies[$order['currency']]['symbol'];
		}
		$payload = ['order_id' => $order['id'], 'name' => $order['name'], 'order_number' => $order['order_number'], 'customer_email' => $order['email'], 'customer_id' => $order['customer']['id'], 'account_id' => $accountId, 'code' => $code];
		ShopifyOrderLoaded::dispatch($payload);

		return response()->json(['success' => true, 'url' => route('orders.review'), 'response' => $order]);
	}

	public function getDiscountRule(Request $request, $code){
		$request->validate([
			'code' => 'required'
		]);
		$session = Utils::getAccountSession($code);
		try{
			// $response = DiscountCode::lookup( $session, [], ["code" => $request->code] );
			$client = new Rest($session->getShop(), $session->getAccessToken());
			$response = $client->get('discount_codes/lookup.json', [], ['code' => $request->code]);
			$headers = $response->getHeaders();
			if(isset($headers['Location'])){  
				$location = $headers['Location'][0];
				$startIndex = strpos($location, 'price_rules/') + 12;
				$endIndex = strpos($location, '/discount_codes');
				$priceRuleId = substr($location, $startIndex, $endIndex - $startIndex);
				if(is_numeric($priceRuleId)){
					$response = PriceRule::find($session,$priceRuleId,[],[]);
					return $response->toArray();
				}
				
			}
		}catch(RestResourceRequestException $e){
			return [];
		}
		
		return [];
	}

	public function getProducts(Request $request, $code){
		$validator = Validator::make($request->all(), [
            		'ids' => 'required',
		]);	
		if($validator->fails()){
			return ['error' => true, 'msg' => 'Products not found.' ];
		}
		$session = Utils::getAccountSession($code);
		$response = Product::all($session, [], ["ids" => $request->ids]);
		if(count($response) == 0){ return ['error' => true, 'msg' => 'Products not found.'];}
		$products = $response;
		return ['success' => true, 'response' => $products];
	}

	public function getProductsByHandle(Request $request, $code){
		$validator = Validator::make($request->all(), [
            		'handles' => 'required'
		]);	
		if($validator->fails()){
			return ['error' => true, 'msg' => 'Products not found.' ];
		}
		$session = Utils::getAccountSession($code);
		$accountId = Utils::getAccountId($code);
		$response = Product::all($session, [], ["ids" => $request->ids]);
		if(count($response) == 0){ return ['error' => true, 'msg' => 'Products not found.'];}
		$products = $response;
		if(!$products){ return ['error' => true, 'msg' => 'Products not found.']; }
		foreach($products as $parent){
			$product = $parent->toArray();
			$product['account_id'] = $accountId;
			$this->productRepo->saveProduct($product);
		}
		return ['success' => true, 'response' => $products];
	}

}
