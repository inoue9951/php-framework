<?php
namespace Core;

class Router
{
    protected $routes;

    /**
    * コンストラクタ
    */
    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    /**
    * ルーティングに含まれる動的パラメータを正規表現でキャプチャできる状態へ変換
    * :で始まる文字列を正規表現の形式に変換する
    * 変換後のurlを返す
    * /sample/:action => /sample/(?P<action>)
    */
    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }
        return $routes;
    }

    /**
    * routesに格納された値とリクエストがマッチするか判別する
    */
    public function resolve($path_info, $method)
    {
        //path_infoの左側に'/'を追加する
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = '/' . $path_info;
        }

        //渡されたpath_infoがroutesの値とマッチするか調べる
        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches) && mb_strtoupper($params['method']) == $method) {
                $params = array_merge($params, $matches);
                return $params;
            }
        }
        return false;
    }
}
