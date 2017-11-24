<?php
namespace Core;

class Request
{
    /**
    * リクエストされたurlを返す
    */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
    * ホストの情報を返す
    */
    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        return $_SERVER['SERVER_NAME'];
    }

    /**
    * ベースURLを返す
    */
    public function getBaseUrl()
    {
        $script_name = $_SERVER['SCRIPT_NAME'];
        $request_uri = $this->getRequestUri();

        if (0 === strpos($request_uri, $script_name)) {
            return $script_name;
        } else if (0 === strpos($request_uri, dirname($script_name))) {
            return rtrim(dirname($script_name), '/');
        }

        return '';
    }

    /**
    * path infoを返す
    */
    public function getPathInfo()
    {
        $base_url = $this->getBaseUrl();
        $request_uri = $this->getRequestUri();

        if (false !== ($pos = strpos($request_uri, '?'))) {
            $request_uri = substr($request_uri, 0, $pos);
        }

        $path_info = (string)substr($request_uri, strlen($base_url));
        return $path_info;
    }

    /**
    * リクエストのメソッドを返す
    * クエリにmethodキーが存在し、正しい値であればその値を返す
    */
    public function getRequestMethod()
    {
        if (array_key_exists('method', $_REQUEST)) {
            $method = $_REQUEST['method'];
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        $method = mb_strtoupper($method);
        $requests = array("GET", "POST", "PUT", "PATCH", "DELETE");
        if (in_array($method, $requests)) {
            return $method;
        } else {
            throw new Exception('不正なHTTPメソッドです');
        }
    }

    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    public function getParams($key)
    {
        return $_REQUEST[$key];
    }
}
