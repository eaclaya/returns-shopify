<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AccountSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class SearchOrderTest extends TestCase
{
    use RefreshDatabase;
    

    protected function getAccountCode(){
        $account = Account::create([
            'name' => config('services.shopify.host'),
            'email' => 'test@gmail.com',
            'is_active' => 1,
            'shop' => config('services.shopify.host'),
            'domain' => config('services.shopify.host'),
        ]);
        $accountSetting = AccountSetting::create([
            'name' => config('services.shopify.host'),
            'account_id' => $account->id,
            'code' => config('services.shopify.code'),
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
}
