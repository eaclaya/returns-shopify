<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AccountSetting;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class CustomerTokenTest extends TestCase
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

    public function test_customer_token_get_url(){
        $code = $this->getAccountCode();
        $response = $this->get("/{$code}/customer/token");
        $response->assertStatus(200);
    }

    public function test_customer_token_post_url(){
        $code = $this->getAccountCode();
        $response = $this->post("/{$code}/customer/token", [
            'email' => 'test@gmail.com'
        ]);
        $response->assertStatus(200);
    }

}
    