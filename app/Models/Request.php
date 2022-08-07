<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    public function items(){
        return $this->hasMany('App\Models\RequestItem');
    }

    public function survey_responses(){
        return $this->hasMany('App\Models\SurveyResponse');
    }

    public function request_comments(){
        return $this->hasMany('App\Models\RequestComment');
    }

    public function account_drafts(){
        return $this->hasMany(AccountDraftOrder::class);
    }
}
