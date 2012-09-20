<?php
namespace FogBugz\Authentication;

class Session
{
    public var $token;

    public function __construct()
    {
    }

    public function login()
    {
        // TODO: refactor this to be a pre-filter for the commands that require it.
        // For instance, help or a setup command shouldn't need this!
        $console->fogbugz = new FogBugz\Api(
            $console->config['User'],
            $console->config['Password'],
            $console->config['Host']
        );
        
        try {
            $console->fogbugz->logon();
        }
        catch(FogBugz\ApiLogonError $e) {
            echo($e->getMessage() . "\n");
            exit(1);
        }
    }

    public function logout()
    {
    }
}

/* End of file Session.php */
