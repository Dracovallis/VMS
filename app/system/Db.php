<?php

namespace app\system;

use Exception;
use PDO;

class Db
{
    private static $instance = NULL;

    public static function getInstance($host = NULL, $dbname = NULL, $username = NULL, $password = NULL)
    {
        if (!isset(self::$instance)) {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            try {
                self::$instance = new pdo(
                    'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8',
                    $username,
                    $password,
                    $pdo_options
                );
            } catch (Exception $ex) {
                die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
            }
        }
        return self::$instance;
    }
}
