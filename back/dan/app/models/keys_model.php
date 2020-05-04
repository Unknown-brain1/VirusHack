<?php


namespace dan\models;


use evgeny\database;

class keys_model extends database
{

    /**
     * @param $client_endpoint
     * @return bool
     */
    public function client_key_store($client_endpoint){
        $this->prepare($client_endpoint);
        $sql = "insert into devices (client_endpoint) value('{$client_endpoint}')";
        $this->execute_sql($sql);
        return true;
    }

}