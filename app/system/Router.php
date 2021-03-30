<?php

namespace app\system;

class Router
{
    private $_whitelistUrls;
    private $_currentUrl;

    public function __construct()
    { 
        $this->_whitelistUrls[] = $this->get(['controller' => 'auth', 'action' => 'login']);
        $this->_whitelistUrls[] = $this->get(['controller' => 'auth', 'action' => 'register']);
        $this->_whitelistUrls[] = $this->get(['controller' => 'auth', 'action' => 'logout']);

        $this->_currentUrl = "$_SERVER[REQUEST_URI]";
    }

    public function get($params)
    {
        $urlArr = [];
        $getParams = '';

        if (isset($params['controller'])) {
            $urlArr[] = $params['controller'];
        }
        if (isset($params['action'])) {
            $urlArr[] = $params['action'];
        }

        if (isset($params['getParams'])) {
            $getParams = '?';
            $paramArray = [];
            foreach ($params['getParams'] as $paramName => $paramValue) {
                $paramArray[] = $paramName . '=' . $paramValue;
            }
            $getParams = '?' . implode('&',  $paramArray);
        }
       

        $url = '/' . implode('/', $urlArr) . $getParams;

        return $url;
    }

    public function goTo($params)
    {
        $url = $this->get($params);
        header("Location: $url", true, 301);
        exit();
    }

    public function getCurrentUrl() {
        return $this->_currentUrl;
    }

    public function hasAccessTo($url) {
        $url = explode('?', $url)[0];
        return in_array($url, $this->_whitelistUrls);
    }
}
