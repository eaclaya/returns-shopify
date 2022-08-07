<?php
namespace App\Libraries;

use App\Models\AccountSetting;
use App\Models\Session;

class Utils {

	public const CREATED_BY_USER = 'created_by_user';
	public const DISCOUNT_BY_USER = 'discount_by_user';
	public const GIFTCARD_BY_USER = 'giftcard_by_user';
	public const CREATED_BY_AGENT = 'created_by_agent';
	public const DISCOUNT_BY_AGENT = 'discount_by_agent';
	public const GIFTCARD_BY_AGENT = 'giftcard_by_agent';
	
	public const REFUND_SELECTED = 'refund_selected';
	public const EXCHANGE_SELECTED = 'exchange_selected';
	public const DISCOUNT_SELECTED = 'discount_selected';
	public const ORIGINAL_PAYMENT_SELECTED = 'original_payment_selected';
	public const GIFTCARD_SELECTED = 'giftcard_selected';
	
	public static function prefixes(){
		$accounts = AccountSetting::groupBy('code')->get()->keyBy('code')->toArray();
		return array_keys($accounts);
	}

	public static function getAccountSession($code){
		$sessionStorage = new SessionStorageManager();
		$accountSettings = cache('accountSettings');
		$accountSetting = isset($accountSettings[$code]) ? $accountSettings[$code] : [];
		$shop = $accountSetting && isset($accountSetting['account']) ? $accountSetting['account']['shop'] : null;
        $_session = Session::where('shop', $shop)->firstOrFail();
        $session = $sessionStorage->loadSession($_session->session_id);
		return $session;
	}

	public static function getAccountId($code){
		$accountSettings = cache('accountSettings'); 
		if(!$accountSettings){
			abort(500, 'The merchant configuration is missing');
		}
		$accountSetting = isset($accountSettings[$code]) ? $accountSettings[$code] : [];
		$accountId = isset($accountSetting['account_id']) ? $accountSetting['account_id'] : null;
		if(!$accountId){
			abort(500, 'Something went wrong, please reload the page and try again');
		}
		return $accountId;
	}

}
