<?php namespace App\Repositories;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class RequestTokenReposiory {
    public function getByToken($token = ''){
        return DB::table('request_tokens')->where('token', $token)->where('is_active', 1)->where('expiration_date', '>', Date::now())->first();
    }

    public function disableToken($token = ''){
        return DB::table('request_tokens')->where('token', $token)->update(['is_active' => 0]);
    }
}