<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/lib/Router.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/lib/DB.php';

 class App{

    public function __construct(){
        
        
        header('Access-Control-Allow-Headers: *');
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:*");
        
        $this -> initRoutes();
        
        
  }

  private function initRoutes(){
    
    $this -> initRouterPUT();
    $this -> initRouterGET();
    Router::run();
  
}
     
  private function initRouterGET(){
    Router::add('/users/:user_id/services/:service_id/tarifs',function(Request $request,Response $response, $user_id, $service_id){
    
        $db = DB::getInstance();
        $data = $db -> getDataJoin('services',["*"],['tarifs'],['services.tarif_id=tarifs.tarif_group_id'],['services.ID','services.user_id'], [$service_id,$user_id]);
        $data = empty($data) ? ["result" => "error"] : [
          "result" => "ok",
          "tarifs" => [
              'title' => $data[0]['title'],
              'link' => $data[0]['link'],
              'speed' => $data[0]['speed'],
              'tarifs' => [
                  $this -> generateTarifsData($data)
              ]
          ]
      ];
      
      $response -> sendJson($data);
    
      });
  }

  private function initRouterPUT(){

    Router::add('/users/:user_id/services/:service_id/tarif', function(Request $request,Response $response, $user_id, $service_id){
        $db = DB::getInstance();
        if($db -> isExistsValue('tarifs', 'tarif_group_id', $request -> tarif_id)){
            $db -> updateData('services',['tarif_id','payday'], [$request -> tarif_id, date('o-m-d')], ['user_id','ID'], [$user_id,$service_id]);

            $data = ["result" => "ok"] ;
        }
        else{
            $data = ["result" => "error"] ;
        }
        // $data = $db -> getDataJoin('services',["*"],['tarifs'],['services.tarif_id=tarifs.tarif_group_id'],['services.ID','services.user_id','services.tarif_id'], [$service_id, $user_id, $request -> tarif_id]);
      
        $response -> sendJson($data);
      });
  }

  private function generateTarifsData($data){
    
        for($i = 0; $i < count($data); $i++){
            $data[$i] = array_filter($data[$i],function($v,$k){
                return ($k !== 'link' && $k !== 'tarif_group_id') && $v;
            },ARRAY_FILTER_USE_BOTH);
            $data[$i]['new_payday'] = (int)$data[$i]['pay_period']*24*3600 + time() + 3*3600;
        }   
        return $data;
  }
}