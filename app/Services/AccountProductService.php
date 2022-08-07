<?php namespace App\Services;

use App\Libraries\Utils;
use Shopify\Rest\Admin2021_10\Product;
use Shopify\Rest\Admin2021_10\Transaction;
use Shopify\Exception\RestResourceRequestException;

class AccountProductService {

    public function fetchOne($productId, $code = null){
        if(!$productId){
            return 'Product id is not valid';
        }
        $session = Utils::getAccountSession($code);
        try{
            $product = Product::find($session,$productId);
            return $product->toArray();
        }catch(RestResourceRequestException $e){
            return 'Product not found';
        }
    }


    public function fetchAll($ids, $code = null){
        if(!$ids){
            return 'Product ids are not valid';
        }
        $session = Utils::getAccountSession($code);
        try{
            $products = Product::all($session,[], ['ids' => $ids, 'fields' => 'id,handle,title,product_type,status,tags,variants,options,image',]);
            return $products;
        }catch(RestResourceRequestException $e){
            return 'Product not found';
        }
    }



    
}