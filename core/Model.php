<?php
namespace Core;

/**
* Modelを継承したオブジェクトはTableへのアクセス権を持つ
* getTableメソッドを使用することでdowncaseのクラス名と
* 等しい名前のテーブルオブジェクトを取得できる
*/
class Model
{

    protected $table;

    public function __construct() {
        $this->setting();
        $this->setTable();
    }

    public function readPhinxSetting() {
        $filename = dirname(__FILE__, 2) . '/config/configurations.php';
        $setting = require($filename);
        return $setting;
    }

    public function setting() {
        //DB接続
        $phinx_setting = $this->readPhinxSetting();
        $params = $phinx_setting['environments'];
        if (getenv('exec_environment')) {
            $key = $params[getenv('exec_environment')];
        } else {
            $key = $params['default_database'];
        }
        \ORM::configure('mysql:host=' . $params[$key]['host'] . ';dbname=' . $params[$key]['name']);
        \ORM::configure('username', $params[$key]['user']);
        \ORM::configure('password', $params[$key]['pass']);
    }

    protected function setTable() {
        $this->table = \ORM::for_table(strtolower(get_class($this)));
    }

    public function getTable() {
        return $this->table;
    }
}
