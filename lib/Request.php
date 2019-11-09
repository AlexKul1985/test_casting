<?php

class Request{
    private $data = [];
    private $headers = [];
    
    public function __construct(){
        if($this -> getMethod() == "POST" || $this -> getMethod() == "PUT"){
            $this -> parseData();
        }
        $this -> parseHeaders();
        
    }
    public function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }
    private function parseHeaders(){
        
        
        foreach ($_SERVER as $key => $value) {
            if(strpos($key,"HTTP_") !== false){
                $key = substr($key,5);
                $key = substr_count($key,"_") > 0 ? implode("-",array_map(function($el){
                    return ucfirst(strtolower($el));
                },explode("_",$key))) : ucfirst(strtolower($key));
                
                $this -> headers[$key] = $value;
            }
        }
        
    }    
    public function getHeaders(){
        return $this -> headers;
    }
    private function parseData(){
        $this -> data = (array) json_decode(file_get_contents('php://input'));
    }
    public function __get($key){
        if(array_key_exists($key,$this -> data)){
            return $this -> data[$key];
        }
    }
    
}
?>