<?php


namespace evgeny\models;


use evgeny\database;

class oauth extends database
{
    protected $platform;


    public function __construct($platform)
    {
        parent::__construct();
        $this->platform = $platform;
    }

    public function store($user_id, $platform_user_id)
    {
        $this->prepare($user_id);
        $this->prepare($platform_user_id);

        $sql = "insert into oauth (user_id, platform, platform_user_id) values ('$user_id','{$this->platform}','$platform_user_id') on duplicate key update platform_user_id = '$platform_user_id'";

        return $this->execute_sql($sql);
    }

    public function get($id)
    {
        $sql = "select * from oauth where id = '$id' limit 1";
        return $this->select_sql($sql, false)[0];
    }

    public function remove($id)
    {
        $sql = "delete from oauth where id = '$id' ";
        return $this->execute_sql($sql);
    }

    public function get_user_id_by_platform_user_id($platform_user_id)
    {
        $this->prepare($platform_user_id);
        $sql = "select user_id from oauth where platform_user_id = '{$platform_user_id}' and platform = '{$this->platform}'";
        return $this->select_sql($sql, true);
    }


}