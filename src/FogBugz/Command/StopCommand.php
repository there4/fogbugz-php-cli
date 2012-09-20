<?php
namespace FogBugz\Command;

// http://symfony.com/doc/current/components/console.html

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('stop')
            ->setDescription('Stop your current working case.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}

/* End of file StopCommand.php */
