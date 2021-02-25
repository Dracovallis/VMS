<?php

namespace app\system;

use Exception;

class Loader
{
    const CONFIG_DIR = 'config/';
    const CONFIG_NAME = 'config.ini';
    
    function getConfig()
    {
        if (is_dir(self::CONFIG_DIR)) {
            return parse_ini_file(self::CONFIG_DIR . self::CONFIG_NAME, true);
        } else {
            throw new Exception('No config file found.');
        }
    }
}
