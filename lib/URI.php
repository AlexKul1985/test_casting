<?php
class URI{
    private $path = "";
    private $pathArray = [];
    private $params = [];
    private $queryParams = [];
    private $uri = "";
    
    public function __construct(){
        $this -> uri = $_SERVER['REQUEST_URI'];
        $this -> scanURI();
    }
    
    public function scanURI(){
        if(strpos($this -> uri,"?") !== false){
            $chunksURI = explode("?", $this -> uri);
            $this -> queryParams = $this -> parseQueryParams($chunksURI[1]);
            $this -> path = $chunksURI[0];
        }
           
        else{
            $this -> path = $this -> uri;
            // echo $this -> uri;
        }
        $path = trim($this -> path, "/");
        $this -> pathArray = strpos($path,"/") !== false ?
         explode("/",$path) : [$path];
    }
    private function parseQueryParams($query){
        
        $arrQuery = [];
        
        if(!empty(trim($query,"")) && preg_match('~^([a-z0-9\_-]+ *?= *?[a-z0-9]+?&?)+?$~',$query)){
                
                $arrQuery = strpos(rtrim($query,"&"),"&") !== false  ? 
                     array_reduce(array_map(function($el){
                             $els = explode("=",$el);
                             return [$els[0] => $els[1]];
                             },explode("&",rtrim($query,"&"))),function($arrPrev,$arrNext){
                             $arrPrev = $arrPrev ?? [];
                             return array_merge($arrPrev, $arrNext);
                            }) : 
                    [explode("=",rtrim($query,"&"))[0] => explode("=",rtrim($query,"&"))[1]];
                }
                
                return $arrQuery;
    }
            
    public function getPathString(){
        return $this -> path;
    }
    public function getPathArray(){
        return $this -> pathArray;
    }
    public function getParams(){
        return $this -> params;
    }
    public function setParams($params){
        $this -> params = $params;
    }
    public function getQueryParams(){
        return $this -> queryParams;
    }
}