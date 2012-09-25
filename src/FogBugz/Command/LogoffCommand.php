<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogoffCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('logoff')
            ->setDescription('End the session with FogBugz')
            ->requireAuth(false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $this->app->fogbugz->logoff();
        unlink($this->app->config['tokenPath']);
        
        $output->writeln("You've logged out.", $this->app->outputFormat);
    }
}

/* End of file LogoffCommand.php */
