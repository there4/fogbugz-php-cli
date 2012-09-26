<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Configure this FogBugz client')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        // TODO: Add setup command

        // Prompt the values in the config file
        // Display the alias to use in bash config.
    }
}

/* End of file SetupCommand.php */
