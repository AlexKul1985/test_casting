<?php

class Response {
    public function setHeaders(array $headers){
        foreach ($headers as $key => $value) {
            header($key.":".$value);
        }
    }
    public function sendJson(array $data){
        echo json_encode($data);
    }
}

?>