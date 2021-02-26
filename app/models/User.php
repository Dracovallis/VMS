<?php

use app\system\Model;

class User extends Model
{
    protected $_tableName = 'vms_user';
    protected $_primary_key = 'id';

    private $id;
    private $username;
    private $password;
    private $email;
}
