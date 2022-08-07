<?php
namespace App\Libraries;
use Str;
use Log;

class EmailParser {
	
    public static function handle($str, $data) {
        preg_match_all('/{{(.*?)}}/', $str, $matches);
        foreach($matches[1] as $key => $match){
            $match = trim($match);
            $value = isset($data[$match]) ? $data[$match] : '';
            if(!$value){
                $match = str_replace('_', '', $match);
                $value = isset($data[$match]) ? $data[$match] : '';
            }
            $matches[1][$key] = $value;
        }
        foreach($matches[0] as $index => $match){
            $key = $match;
            $value = $matches[1][$index];
            $str = str_replace($key, $value, $str);
        }
        return $str;
    }
}