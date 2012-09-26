<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('search')
            ->setDescription('Search by keyword')
            ->requireAuth(true);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        // TODO: Add search command
    }
}

/* End of file SearchCommand.php */
