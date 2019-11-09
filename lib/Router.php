<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/URI.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/Request.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/Response.php';

class Router{
        
    private static $routes = [];
    private static $uri = null;

    public static function add($path, $callback){
        self::$routes[$path] = $callback;
    
    }
    public static function run(){
        self::$uri = new URI();
        self::scanRoute();
    }

    
    private static function scanRoute(){
        $path = self::$uri -> getPathString();
        
        
        foreach (self::$routes as $pattern => $callback) {
            
            $arrPattern = explode("/",trim($pattern,"/"));
            
            $regexp = self::convertToRegex($pattern,$arrPattern);
            
            $request = new Request();
            $response = new Response();
            if(strpos($pattern,":") !== false){
                
                if(preg_match('~'.$regexp.'~',$path, $matches)){
                    $params = array_splice($matches,1);
                    self::$uri -> setParams(self::getArrayKeyParams($arrPattern, $params));
                    
                    $callback($request,$response,...$params);
                    break;
                    
                }
            }
                    
            else{
                
                if(preg_match('~'.$regexp.'~',$path)){
                    $callback($request, $response);
                    break;
                }
            }
                    
        }
    }
    private static function getArrayKeyParams($arrPattern, $params){
        
        $keysParams = [];
        
        for($i = 0; $i < count($arrPattern); $i++){
            if(strpos($arrPattern[$i],":")!==false || strpos($arrPattern[$i],":?")!==false){
                $keysParams[] = str_replace([":","?"],"",$arrPattern[$i]);
            }
        }
        $def = count($keysParams) - count($params);
        
        return $def > 0 ? 
            array_combine(array_splice($keysParams,0, count($keysParams) - $def), $params) :
            array_combine($keysParams, $params);
        
        } 
        
        
                    
                    
    public static function convertToRegex($pattern, $arrPattern){
        
        $replacment = "([a-zA-Z0-9-]+)";
        
        if(strpos($pattern,":") !== false){
            for($i = 0; $i < count($arrPattern); $i++){
                if(substr_count($arrPattern[$i],":") == 1 && substr_count($arrPattern[$i],"?") == 1){
                    $arrPattern[$i] = str_replace($arrPattern[$i],$replacment."?", $arrPattern[$i]);
                }
                else if(substr_count($arrPattern[$i],":") == 1){
                    $arrPattern[$i] = str_replace($arrPattern[$i],$replacment, $arrPattern[$i]);
 
                }
                
            }
        }
        
        return '^\/?'.implode("\/?",$arrPattern)."\/?$";
    }
}
?>