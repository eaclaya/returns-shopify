<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AccountSetting;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;


class SearchOrderTest extends TestCase
{
    use RefreshDatabase;
    

    protected function getAccountCode(){
        $account = Account::create([
            'name' => config('services.shopify.host'),
            'email' => 'test@gmail.com',
            'is_active' => 1,
            'shop' => config('services.shopify.shop'),
            'domain' => config('services.shopify.shop'),
        ]);
        $accountSetting = AccountSetting::create([
            'name' => config('services.shopify.host'),
            'account_id' => $account->id,
            'code' => config('services.shopify.code'),
        ]);
        $session = Session::create([
            'session_id' => config('services.shopify.shop'),
            'shop' => config('services.shopify.shop'),
            'is_online' => 1,
            'access_token' => config('services.shopify.password'),
            'state' => config('services.shopify.key'),
            'scope' => 'read_products,write_products,read_draft_orders,write_draft_orders,read_checkouts,write_checkouts,read_customers,write_customers,read_discounts,write_discounts,read_gift_cards,write_gift_cards,read_inventory,write_inventory,read_orders,write_orders,read_product_listings'
        ]);
        return $accountSetting->code;
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_if_search_url_is_valid()
    {
        
        $code = $this->getAccountCode();
        $response = $this->get("/{$code}");
        $response->assertStatus(200);
    }


    public function test_valid_order_search_on_shopify(){
        $code = $this->getAccountCode();
        $response = $this->post("/api/{$code}/v1/orders/get", [
            'email' => 'xmaz2023@gmail.com',
            'order_number' => '303311'
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);
        
    }

    public function test_invalid_order_search_on_shopify(){
        $code = $this->getAccountCode();
        $response = $this->post("/api/{$code}/v1/orders/get", [
            'email' => 'xmaz2023@gmail.com',
            'order_number' => 'A303311'
        ]);
        $response->assertStatus(200)->assertJson([
            'error' => true,
        ]);
    }
}
