<?php namespace App\Repositories;
use App\Libraries\Utils;
use App\Models\Destination;

class DestinationRepository {
    public function all($accountId = 1){
        $destinationList = Destination::where('account_id', $accountId)->get();
        $destinations = [];
		if($destinationList->first()){
			foreach($destinationList as $destination){
				if(isset($destinations[$destination->country_code]) == false){
					$destinations[$destination->country_code] = ['country_code' => $destination->country_code, 'country_id' => $destination->country_id];
				}
				if($destination->province_code){
					if(isset($destinations[$destination->country_code]['provinces']) == false){
						$destinations[$destination->country_code]['provinces'] = [];
					}
					$destinations[$destination->country_code]['provinces'][$destination->province_code] = ['province_code' => $destination->province_code, 'province_id' => $destination->province_id]; 
				}
			}
		}
        return $destinations;
    }
}