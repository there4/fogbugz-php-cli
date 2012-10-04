<?php

$app = require __DIR__ . '/../vendor/autoload.php';
$app->add('FogBugz', __DIR__ . '/../src');

use FogBugz\Cli\Working;
use There4\FogBugz\Api;

class WorkingTest extends \PHPUnit_Framework_TestCase
{

    public $console;

    public function setUp()
    {
        $root = realpath(__DIR__ . '/../');
        $project = json_decode(file_get_contents(__DIR__ . '/../composer.json'));
        $this->console = new Working($root, $project);
        
        $this->console->fogbugz = $this->getMock('Api');
        $this->console->fogbugz->token = 'foo';
    }
}

/* End file bootstrap.php */