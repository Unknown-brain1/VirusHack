<?php

namespace evgeny;

use mysqli;

class database
{
    protected $mysql_link;

    public function __construct()
    {
        $this->mysql_link = new mysqli(
            getenv('DB_HOST'),
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            getenv('DB_NAME'),
            null,
            getenv('SOCKET')
        );
        if ($this->mysql_link->connect_error) {
            die('Error db connect: ' . $this->mysql_link->connect_errno);
        }
    }

    /**
     * Выполняет произвольный sql запрос, без обработки ошибок
     * @param $sql
     * @return bool|\mysqli_result
     */
    protected function execute_sql($sql)
    {
        return $this->mysql_link->query($sql);
    }

    /**
     * Подготавливает объект(все его свойства) или строку к вставке в sql запрос
     * @param $object
     */
    protected function prepare(&$object)
    {
        if (is_object($object) or is_array($object))
            foreach ($object as &$value)
                $this->prepare($value);
        else
            $object = $this->mysql_link->escape_string($object);
    }

    /**
     * Выполняет select запрос и возвращает stdClass[] или одно поле, если $is_solo = true
     * @param $sql
     * @param bool $is_solo
     * @return array|mixed
     */
    protected function select_sql($sql, $is_solo = false)
    {
        $sql_datas = $this->mysql_link->query($sql);
        if (!$sql_datas)
            return false;
        if ($is_solo)
            return $sql_datas->fetch_array()[0] ?? false;
        $rows = array();
        while ($row = $sql_datas->fetch_object()) {
            $rows[] = $row;
        }

        return $rows;
    }

}