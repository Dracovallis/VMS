<?php

namespace app\system;

class Router
{
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
}
