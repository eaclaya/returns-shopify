<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    public function steps(){
        return $this->hasMany('App\Models\SurveyStep');
    }

    public function items(){
        return $this->hasMany('App\Models\SurveyItem');
    }
}
