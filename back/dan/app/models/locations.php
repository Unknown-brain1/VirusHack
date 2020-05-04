<?php


namespace dan\models;


use evgeny\database;

class locations extends database
{

    /**
     * @return array|mixed
     */

    public function locations_get()
    {
        $sql = "select name, location from locations";
        $result = $this->select_sql($sql);
        return $result;
    }
}