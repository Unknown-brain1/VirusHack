<?php


namespace dan\models;


use evgeny\database;

class keys_model extends database
{

    /**
     * @param $client_key
     * @return bool
     */
    public function client_key_store($client_key){
        $this->prepare($client_key);
        $sql = "insert into devices (client_key) value('{$client_key}')";
        $this->execute_sql($sql);
        return true;
    }

}