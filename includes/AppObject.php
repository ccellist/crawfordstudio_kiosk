<?php
abstract class AppObject {    
    abstract public function __construct();
    abstract public function __get($name);
    abstract public function __set($name, $value);
//    
//    public function unsetValue(array $array, $value, $strict = TRUE){ //TODO: bug in this method.
//        if (($key = array_search($value, $array, $strict) !== false)){
//            unset($array[$key]);
//        }
//        return $array;
//    }
//    
//    public function binarySearch($value, array $array, callable $cmp){
//        
//    }
    
    public static function filterValue(Array $array, $my_value){
        $filtered_array = array_filter($array, function ($element) use ($my_value) { return ($element != $my_value); } ); 
        return $filtered_array;
    }
}

