<?php

use Firebase\JWT\JWT;

class ControllerBase
{
    protected $_view;
    protected $_jsPaths = [];
    protected $_cssPaths = [];
    protected $_user;
    protected $_userMenu = [
        'guest' => [
            [
                'title' => 'Home',
                'url' => [
                    'controller' => 'home'
                ]
            ],
            [
                'title' => 'Login',
                'url' => [
                    'controller' => 'auth',
                    'action' => 'login'
                ]
            ],
            [
                'title' => 'Register',
                'url' => [
                    'controller' => 'auth',
                    'action' => 'register'
                ]
            ],
        ],
        'logged' => [
            [
                'title' => 'Home',
                'url' => [
                    'controller' => 'home'
                ]
            ],
            [
                'title' => 'User',
                'children' => [
                    [
                        'title' => 'Profile',
                        'url' => [
                            'controller' => 'user',
                            'action' => 'profile'
                        ]
                    ],
                    [
                        'title' => 'Dashboard',
                        'url' => [
                            'controller' => 'user',
                            'action' => 'dashboard'
                        ]
                    ]
                ]

            ],
            [
                'title' => 'Logout',
                'url' => [
                    'controller' => 'auth',
                    'action' => 'logout'
                ]
            ],
        ]
    ];

    public function __construct($config)
    {
        $this->_config = $config;

        include('../app/system/Model.php');
        include('../app/system/Form.php');
        include('system/Router.php');
        $this->_router = new app\system\Router();


        session_start();
        $this->authenticate();

        include('system/Db.php');
        $this->_db = app\system\Db::getInstance(
            $this->_config['database']['host'],
            $this->_config['database']['dbname'],
            $this->_config['database']['username'],
            $this->_config['database']['password']
        );

        $this->addJs('node_modules/jquery/dist/jquery.min.js');
        $this->addJs('node_modules/bootstrap/dist/js/bootstrap.min.js');
        $this->addJs('js/public.js');

        $this->addCss('node_modules/bootstrap/dist/css/bootstrap.min.css');
        $this->addCss('css/style.css');
    }

    public function render($viewPath = null)
    {

        $controllerName = debug_backtrace()[1]['class'];
        $controllerNameParsed =  strtolower(preg_replace('/Controller$/', '', $controllerName));
        $this->_controllerName =  $controllerNameParsed;

        $functionName = debug_backtrace()[1]['function'];
        $action = preg_replace('/Action$/', '', $functionName);

        $this->_actionName = $action;

        if ($viewPath) {
            $this->_view = $this->_config['app']['viewsDir'] . $viewPath . '.phtml';
        } else {
            $this->_view = $this->_config['app']['viewsDir'] . $controllerNameParsed . '/' . $action . '.phtml';
        }

        // loading default js and css if present
        $this->addJs('js/' . $controllerNameParsed . '/' . $action . '.js');
        $this->addCss('css/' . $controllerNameParsed . '/' . $action . '.css');

        require($this->_config['app']['viewsDir'] . 'index.phtml');
    }

    public function getContent()
    {
        if (is_file($this->_view)) {
            require_once($this->_view);
        }
    }

    public function addJs($path)
    {
        $fullPath = $this->_config['app']['publicResourcesDir'] . $path;
        if (is_file($fullPath)) {
            $this->_jsPaths[] = $fullPath;
        }
    }

    public function addCss($path)
    {
        $fullPath =  $this->_config['app']['publicResourcesDir'] . $path;
        if (is_file($fullPath)) {
            $this->_cssPaths[] = $fullPath;
        }
    }

    public function getHeader()
    {
        require_once('../app/views/shared/header.phtml');
    }

    public function getFooter()
    {
        require_once('../app/views/shared/footer.phtml');
    }

    public function getCss()
    {
        require_once($this->_config['app']['viewsDir'] . 'shared/templates/css-render.phtml');
    }

    public function getJs()
    {
        require_once($this->_config['app']['viewsDir'] . 'shared/templates/js-render.phtml');
    }

    public function setVars($vars)
    {
        foreach ($vars as $key => $value) {
            $this->{$key} = $value;
        }
    }

    protected function setToken($username, $id)
    {

        $now = new \DateTime();
        $future = new \DateTime("+8 hours");
        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            'username' => $username,
            'user_id' => $id
        ];

        $token = JWT::encode($payload, $this->_config['app']['secret'], "HS256");

        $_SESSION['jwt'] = $token;
    }

    protected function authenticate()
    {
        $token = $this->getToken();

        if (!empty($token) && $token->exp > strtotime("now")) {
            $this->user['username'] = $token->username;
            $this->user['id'] = $token->user_id;
        } else {
            if (!$this->_router->hasAccessTo($this->_router->getCurrentUrl())) {
                $this->_router->goTo(['controller' => 'auth', 'action' => 'login']);
            }
        }
    }

    protected  function unsetToken()
    {
        unset($_SESSION['jwt']);
        $this->_router->goTo(['controller' => 'auth', 'action' => 'login']);
    }

    protected function getToken()
    {
        $token = false;
        if (!empty($_SESSION) && !empty($_SESSION['jwt'])) {
            $jwt = $_SESSION['jwt'];

            try {
                $token = JWT::decode($jwt, $this->_config['app']['secret'], ['HS256']);
            } catch (Exception $e) {
                unset($_SESSION['jwt']);
            }
        }

        return $token;
    }
}
