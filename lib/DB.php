<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/db_cfg.php";
    
    class DB {

        private static $instance = null;
        private static $db_conn = null;
        
        private function __construct(){}

        public static function getInstance(){
            if(is_null(self::$instance)){
                self::initConnection();
                self::$instance = new DB();
               
            }
            
            return self::$instance;
        }

        private static function initConnection(){
             $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8";
             $opt = [ PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
             self::$db_conn = new PDO($dsn, DB_USER, DB_PASSWORD,$opt);
        }

        private  function getQuery(string $table_name, array $fields_search = ["*"], array $where_fields = []){
            $query = $query ?? $this -> selectQuery($table_name,$fields_search);
            if(!empty($where_fields)){
                $query .= $this -> addWhere($where_fields);
                
            }
            echo $query."<br/>";
            return $query;
                
        }

        private function selectQuery(string $table_name, array $fields_search){
            return "SELECT ".join(',',$fields_search)." FROM ".$table_name;
        }
                    
                
        private  function updateQuery(string $table_name, array $fields, array $where_fields = []){
            
            $query = "UPDATE ".$table_name." SET ".join("= ?,",$fields)." = ?";
                
            $query .= $this -> addWhere($where_fields);
                
            return $query;
            
        }

        private function joinQuery(string $table_name,array $fields_search,array $join_arr,array $on_arr,array $where_fields){
            $query = $query ?? $this -> selectQuery($table_name,$fields_search);
            if(count($join_arr) == count($on_arr)){
                for($i = 0; $i < count($join_arr); $i++){
                    $query .= " JOIN ".$join_arr[$i]." ON ".$on_arr[$i];
                }
                $query .= $this -> addWhere($where_fields);
            }
          
            return $query;
            
            

        }

        private  function addWhere(array $where_fields){
            $query = " WHERE ";
            for($i = 0; $i < count($where_fields); $i++){
                $query .= $where_fields[$i]." = ?";
                if($i + 1 !== count($where_fields)){
                    $query .= " AND ";
                }
            }
            return $query;
        }

        public function getData(string $table_name, array $fields_search = [], array $where_fields = [], array $where_values = []){
            $stmt = self::$db_conn->prepare($this -> getQuery($table_name,$fields_search,$where_fields));
            $stmt->execute($where_values);
            return $stmt->fetchAll();
        }
        public function getDataJoin(string $table_name,array $fields_search,array $join_arr,array $on_arr,array $where_fields, array $where_values){
            $stmt = self::$db_conn->prepare($this -> joinQuery($table_name, $fields_search, $join_arr, $on_arr, $where_fields));
            $stmt->execute($where_values);
            return $stmt->fetchAll();
        }

        public  function updateData(string $table_name, array $fields, array $newValues,array $where_fields = [], array $where_values = []){
            $stmt = self::$db_conn->prepare($this -> updateQuery($table_name,$fields,$where_fields));
            // echo $this -> updateQuery($table_name,$field,$where_fields);
            array_unshift($where_values,...$newValues);
            $stmt->execute($where_values);
            return $stmt->fetchAll();
        }
        public function isExistsValue(string $table_name, $field, $value){
            $stmt = self::$db_conn->prepare('SELECT 1 FROM '.$table_name.' WHERE '.$field.' = ?');
            $stmt->execute([$value]);
            return $stmt->fetch(PDO::FETCH_NUM);
        }
    }

?>