<?php namespace App\Repositories;

use App\Models\AccountCollection;
use App\Models\AccountCollectionProduct;
use App\Models\AccountProduct;
use App\Models\AccountProductOption;
use App\Models\AccountProductMain;

class AccountProductRepository {

    public function getProductsBySku($skus, $skipStatus = false){
        $products = [];
		$_products = AccountProduct::whereIn('sku', $skus);
		$excludeProducts = AccountProductMain::where('exclude', 1)->get()->keyBy('product_id');
		if(!$skipStatus){
			$_products = $_products->where('status', 1);
		}
		$_products = $_products->get();
		
		foreach($_products as $item){
			
			if(isset($excludeProducts[$item->parent_id])){
				continue;
			}
			$items = AccountProduct::with('collections')->where('handle', $item->handle);
			if(!$skipStatus){
				$items = $items->where('status', 1);
			}
			$items = $items->get()->toArray();
			$products = array_merge($products, $items);
		}
		
        return $products;
    }

	public function getProductsByCollection($accountId = 1, $collectionId = null){
		return AccountCollectionProduct::with('product')->where('account_id', $accountId)->where('collection_id', $collectionId)->orderBy('sort_number')->get();
	}

	public function getActiveCollections($accountId = 1){
		return AccountCollection::where('account_id', $accountId)->where('is_active', 1)->orderBy('sort_number')->get();
	}

	public function getProductTypes($accountId = 1){
		return AccountProduct::select('product_type')->where('status', 1)->where('account_id', $accountId)->groupBy('product_type')->get()->keyBy('product_type');
	}

	public function getProductsByParent($ids, $skipStatus = false, $accountId = 1){
		$_products = AccountProduct::with('collections')->where('account_id', $accountId)->whereIn('parent_id', $ids);
		if(!$skipStatus){
			$_products = $_products->where('status', 1);
		}
		$_products = $_products->get();
		if($_products->first()){
			$options = AccountProductOption::where('account_id', $accountId)->where('product_type', $_products->first()->product_type)->get();
			if($options->first()){
				foreach($_products as &$product){
					foreach($options as  $item){
						if($product->option1_name == $item->option_name && $product->option1_value == $item->option_value){
							$product->option1_type = $item->type;
							$product->option1_label = $item->value;
						}
						if($product->option2_name == $item->option_name && $product->option2_value == $item->option_value){
							$product->option2_type = $item->type;
							$product->option2_label = $item->value;
						}
						if($product->option3_name == $item->option_name && $product->option3_value == $item->option_value){
							$product->option3_type = $item->type;
							$product->option3_label = $item->value;
						}
					}
				}
			}
		}
		
        return $_products;
    }

	public function saveProduct($parent){
		if(!$parent['id'] || !$parent['handle']){ return null; }
		$accountId = isset($parent['account_id']) ? $parent['account_id'] : 1;
		AccountProduct::where('parent_id', $parent['id'])->update(['status' => 0]);
		foreach($parent['variants'] as $item){
			$product = AccountProduct::where('account_id', $accountId)->where('product_id', $item['id'])->first();
			if(!$product){
				$options = isset($parent['options']) ? $parent['options'] : [];
				$product = new AccountProduct();
				$product->account_id = $accountId;
				$product->parent_id = isset($parent['id']) ? $parent['id'] : 0;
				$product->product_id = isset($item['id']) ? $item['id'] : 0;
				$product->title = isset($parent['title']) ? $parent['title'] : null;
				$product->option1_name = isset($options[0]) ? $options[0]['name'] : null;
				$product->option1_value = isset($item['option1']) ? $item['option1'] : null;
				$product->option2_name = isset($options[1]) ? $options[1]['name'] : null;
				$product->option2_value = isset($item['option2']) ? $item['option2'] : null;
				$product->option3_name = isset($options[2]) ? $options[2]['name'] : null;
				$product->option3_value = isset($item['option3']) ? $item['option3'] : null;
				$product->sku = isset($item['sku']) ? $item['sku'] : null;
				$product->inventory_item_id = isset($item['inventory_item_id']) ? $item['inventory_item_id'] : 0;
				$product->product_type = $parent['product_type'] ? $parent['product_type'] : null;
				$product->taxable = $item['taxable'] == 'true' ? true : false;
				$product->handle = isset($parent['handle']) ? $parent['handle'] : null;
				$product->tags = isset($parent['tags']) ? $parent['tags'] : null;
				$product->image = isset($parent['image']) ? $parent['image']['src'] : null;
				
			}
			$product->status = $parent['status'] == 'active' ? true : false; 
			$product->price = isset($item['price']) ? $item['price'] : 0;
			if(isset($item['inventory_quantity'])){
				$product->inventory_quantity = isset($item['inventory_quantity']) > 0 ? $item['inventory_quantity'] : 0;
			}elseif(isset($item['old_inventory_quantity'])){
				$product->inventory_quantity = isset($item['old_inventory_quantity']) > 0 ? $item['old_inventory_quantity'] : 0;
			}
			$product->last_update = date('Y-m-d H:i:s');
			$product->save();
		}
	}
}
