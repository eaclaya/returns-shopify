<?php namespace App\Repositories;

use App\Models\Survey;

class SurveyRepository {

    public function getByActiveStatus($accountId = 1){
       return Survey::with('items', 'steps')->where('account_id', $accountId)->where('is_active', 1)->get();
    }
}