<?php

require dirname(__FILE__, 3) . '/vendor/autoload.php';

class Parser
{
    public $parser;
    protected $result = null;

    public function __construct()
    {
        $this->parser = new Console_CommandLine();
        $this->setting();
    }

    private function setting()
    {
        $this->parser->addArgument('action');
        $this->parser->addArgument('options', array('multiple' => true, 'optional' => true));
    }

    public function parse()
    {
        try {
            $result = $this->parser->parse();
            $this->result = $result;
        } catch (Exception $exc) {
            $this->parser->displayError($exc->getMessage());
        }
    }

    public function getResult()
    {
        return $this->result;
    }
}
