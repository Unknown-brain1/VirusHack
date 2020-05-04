<?php


namespace dan\models;


use evgeny\database;

class user extends database
{

    /**
     * @param $login
     * @param $password
     * @return bool|string
     */
    public function store($login, $password)
    {
        $hash = hash('sha256', $password);
        $token = hash('sha256', strval(time()) . getenv('HASH'));
        $this->prepare($login);
        if ($this->exists($login)) {
            return false;
        }
        $sql = "insert into users (login, password_hash, token) value('{$login}','{$hash}','{$token}')";
        $result = $this->execute_sql($sql);
        if ($result) {
            return $token;
        } else {
            return false;
        }
    }

    public function oauth_store($platform_user_id)
    {
        $login = hash('sha256', strval(time()) . $platform_user_id);
        $token = hash('sha256', strval(time()) . getenv('HASH'));
        $hash = hash('sha256', $token . $login);
        $sql = "insert into users (login, password_hash, token) value('{$login}','{$hash}','{$token}')";
        $result = $this->execute_sql($sql);
        if ($result) {
            return $token;
        } else {
            return false;
        }
    }


    /**
     * @param $login
     * @param $password
     * @return array|bool|mixed
     */
    public function get($login, $password)
    {
        $hash = hash('sha256', $password);
        $this->prepare($login);
        $sql = "select token from users where login = '{$login}' and password_hash = '{$hash}'";
        $token = $this->select_sql($sql, true);
        if ($token) return $token;
        return false;
    }


    /**
     * @param $login
     * @return bool
     */
    public function exists($login)
    {
        $this->prepare($login);
        $sql = "select count(*) from users where login = '{$login}'";
        return (bool)$this->select_sql($sql, true);
    }


    /**
     * @param $token
     * @return array|bool|mixed
     */
    public function get_id_by_token($token)
    {
        $this->prepare($token);
        $sql = "select id from users where token = '{$token}'";
        $id = $this->select_sql($sql, true);
        if ($id) return $id;
        return false;
    }


    /**
     * @param $id
     * @return array|bool
     */
    public function get_token_by_id($id)
    {
        $this->prepare($id);
        $sql = "select token from users where id = '{$id}'";
        $token = $this->select_sql($sql, true);
        if ($token) return $token;
        return false;
    }


    /*  public function resetpass($login, $password, $newpassword)
      {
          $hash_old = hash('sha256', $password);
          $hash_new = hash('sha256', $newpassword);
          $this->prepare($login);
          $exist = $this->exists($login);
          if($exist){

          }
      } */

}