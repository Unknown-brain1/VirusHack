<?php


namespace dan\models;


use evgeny\database;

class locations extends database
{

    /**
     * @return array|mixed
     */

    public function locations_provide()
    {
        $sql = "select name, location from locations where state = 'Bad'";
        $result = $this->select_sql($sql);
        return $result;
    }
}