<?php

namespace app\system;

use Exception;

class Bootstrap
{
    private $_config;
    private $_controllersDir;
    private $_actionSuffix;
    private $_controllerSuffix;
    private $_defaultControllerAction;
    private $_defaultController;
    private $_notFoundController;

    public function __construct($config)
    {
        date_default_timezone_set('Europe/Sofia');

        $this->_config = $config;
        $this->_actionSuffix = $this->_config['appDefaults']['actionSuffix'];
        $this->_controllerSuffix = $this->_config['appDefaults']['controllerSuffix'];
        $this->_defaultController = $this->_config['appDefaults']['defaultController'];
        $this->_notFoundController = $this->_config['appDefaults']['notFoundController'];
        $this->_controllersDir = $this->_config['app']['controllersDir'];
        $this->_modelsDir = $this->_config['app']['modelsDir'];
        $this->_formsDir = $this->_config['app']['formsDir'];
        $this->_systemDir = $this->_config['app']['systemDir'];
        $this->_defaultControllerAction = $this->_config['appDefaults']['defaultAction'] . $this->_config['appDefaults']['actionSuffix'];

        spl_autoload_register(function ($className) {
            if (file_exists($this->_controllersDir . $className . '.php')) {
                require_once $this->_controllersDir . $className . '.php';
            }

            if (file_exists($this->_modelsDir . $className . '.php')) {
                require_once $this->_modelsDir . $className . '.php';
            }

            if (file_exists($this->_formsDir . $className . '.php')) {
                require_once $this->_formsDir . $className . '.php';
            }

            if (file_exists( $this->_systemDir . $className  . '.php')) {
                include $this->_systemDir . $className  . '.php';
            }

            if (file_exists('../app/vendor/autoload.php')) {
                require_once '../app/vendor/autoload.php';
            }

            ini_set('xdebug.max_nesting_level', 512);

        });
    }

    public function exec()
    {
        $flag = false;
        // router
        if (isset($_GET['path']) && !empty($_GET['path'])) {
            $tokens = explode('/', trim($_GET['path'], '/'));
            unset($_GET['path']);
         
            // dispatcher
            $controllerName = ucfirst(array_shift($tokens)) . $this->_controllerSuffix;
            if (file_exists($this->_controllersDir . $controllerName . '.php')) {
                $controller = new $controllerName($this->_config);
                // run the action
                if (!empty($tokens)) {
                    $tokens = array_merge($tokens , $_GET);

                    $actionName = array_shift($tokens) . $this->_actionSuffix;
                    if (method_exists($controller, $actionName)) {
                        $controller->{$actionName}(@$tokens);
                    } else {
                        // 404
                        $flag = true;
                    }
                } else if (method_exists($controller, $this->_defaultControllerAction)) {
                    // default action
                    $controller->{$this->_defaultControllerAction}();
                } else {
                    throw new Exception('The controller does not contain the default action');
                }
            } else {
                // no controller found
                $flag = true;
            }
        } else {
            // no controlller entered - go to default controller
            $controllerName = $this->_defaultController . $this->_controllerSuffix;
            if (file_exists($this->_controllersDir . $controllerName . '.php')) {
                $controller = new $controllerName($this->_config);
                if (method_exists($controller, $this->_defaultControllerAction)) {
                    $controller->{$this->_defaultControllerAction}();
                } else {
                    throw new Exception('Default controller does not containt a default action');
                }
            } else {
                throw new Exception('No default controller specified');
            }
        }

        // 404 page
        if ($flag) {
            $controllerName = $this->_notFoundController . $this->_controllerSuffix;
            if (file_exists($this->_controllersDir . $controllerName . '.php')) {
                $controller = new $controllerName($this->_config);
                if (method_exists($controller, $this->_defaultControllerAction)) {
                    $controller->{$this->_defaultControllerAction}();
                } else {
                    throw new Exception('404 controller does not containt a default action');
                }
            } else {
                throw new Exception('No 404 controller specified');
            }
        }
    }
}
