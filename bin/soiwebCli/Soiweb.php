<?php

require 'Parser.php';
require 'CreateFile.php';

class Soiweb
{
    protected $parser;
    protected $action;
    protected $options = array();
    protected $expectedActions = array('create', 'migrate', 'migration', 'rollback', 'seed', 'test');

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function run()
    {
        $this->parse();
        if (in_array($this->action, $this->expectedActions)) {
            $function = $this->action;
            $this->$function($this->options);
            return;
        } else {
            echo '不正なアクションです' . PHP_EOL;
            return;
        }
    }

    private function parse()
    {
        $this->parser->parse();
        $result = $this->parser->getResult();
        $this->action = $result->args['action'];
        $this->options = $result->args['options'];
    }

    private function create($params = array())
    {
        echo __FUNCTION__ . ' running' . PHP_EOL;
        $type = $params[0];
        $filenames = array_slice($params, 1, count($params) -1);
        $generater = new CreateFile($type, $filenames);
        $generater->run();
    }

    private function test($params = array())
    {
        $options = !empty($params) ? ' ' . implode(' ', $params) : '';
        $cmd = '/vendor/bin/phpunit' . $options . ' -c ' . dirname(__FILE__, 3) . '/config/phpunit.xml';
        $this->cmdExec(__FUNCTION__, $cmd);
    }

    // 以下phinxのラッパー関数
    private function migration($params = array())
    {
        $str = !empty($params) ? ' ' . implode(' ', $params) : '';
        $cmd = '/vendor/bin/phinx create' . $str . ' -c ' . dirname(__FILE__, 3) . '/config/configurations.php';
        $this->cmdExec(__FUNCTION__, $cmd);
    }

    private function migrate($params = array())
    {
        $str = !empty($params) ? ' ' . implode(' ', $params) : '';
        $cmd = '/vendor/bin/phinx migrate' . $str . ' -c ' . dirname(__FILE__, 3) . '/config/configurations.php';
        $this->cmdExec(__FUNCTION__, $cmd);
    }

    private function rollback($params = array())
    {
        $str = !empty($params) ? ' ' . implode(' ', $params) : '';
        $cmd = '/vendor/bin/phinx rollback' . $str . ' -c ' . dirname(__FILE__, 3) . '/config/configurations.php';
        $this->cmdExec(__FUNCTION__, $cmd);
    }

    private function seed($params = array()) {
        $seedCmd = $params[0];
        $params = array_slice($params, 1, count($params) -1);
        $str = !empty($params) ? ' ' . implode(' ', $params) : '';
        $cmd = '/vendor/bin/phinx seed:' . $seedCmd . $str . ' -c ' .dirname(__FILE__, 3) . '/config/configurations.php';
        $this->cmdExec(__FUNCTION__, $cmd);
    }

    private function cmdExec($name, $cmd)
    {
        echo $name . ' running' . PHP_EOL;
        exec(dirname(__FILE__, 3) . $cmd, $output);
        $this->printResult($output);
    }

    private function printResult($output) {
        foreach ($output as $result) {
            echo $result . PHP_EOL;
        }
    }
}
