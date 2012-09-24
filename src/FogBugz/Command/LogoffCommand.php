<?php
namespace FogBugz\Command;

// http://symfony.com/doc/current/components/console.html

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogoffCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('logoff')
            ->setDescription('End the session with FogBugz');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
    }
}

/* End of file LogoffCommand.php */
