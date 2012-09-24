<?php
namespace FogBugz\Command;

use There4\FogBugz;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoginCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('login')
            ->setDescription('Establish a session with FogBugz');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        
        $output->writeln("<info>Login</info>");
        
        // TODO: Prompt for username and password, and then login
        //       store the api token into a dotfile in the config
        //       file location
        
        $this->app->fogbugz = new FogBugz\Api(
            $this->app->config['User'],
            $this->app->config['Password'],
            $this->app->config['Host']
        );
        
        try {
            $this->app->fogbugz->logon();
        }
        catch(FogBugz\ApiLogonError $e) {
            echo($e->getMessage() . "\n");
            exit(1);
        }
    }
}

/* End of file LoginCommand.php */
