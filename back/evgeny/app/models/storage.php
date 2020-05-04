<?php


namespace evgeny\models;


use evgeny\database;

class storage extends database
{
    private $token;

    public function __construct($token)
    {
        parent::__construct();
        $this->token = $token;
        $this->prepare($this->token);
    }

    public function store($data)
    {
        $this->prepare($data);

        $sql = "insert into storage (token, crypt_data) values ('{$this->token}','$data') on duplicate key update crypt_data = '$data'";
        return $this->execute_sql($sql);
    }

    public function load()
    {
        $sql = "select `crypt_data` from storage where token = '{$this->token}'";
        return $this->select_sql($sql, true);
    }

    public function is_isset()
    {
        $sql = "select count(*) from storage where token = '{$this->token}'";
        return (bool)$this->select_sql($sql, true);
    }

    public function remove()
    {
        $sql = "delete from storage where token = '{$this->token}'";
        return $this->execute_sql($sql);
    }
}