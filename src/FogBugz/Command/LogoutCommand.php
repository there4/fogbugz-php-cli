<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogoutCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('logout')
            ->setDescription('End the session with FogBugz')
            ->requireAuth(false)
            ->setHelp(
<<<EOF
The <info>%command.name%</info> command ends a session at FogBugz and removes
the authentication token from the local config file.

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        if (property_exists($this->app, "fogbugz")) {
            $this->app->fogbugz->logoff();
        }
        $this->app->config['AuthToken'] = '';
        $this->app->saveConfig();

        $output->writeln("You've logged out.", $this->app->outputFormat);
    }
}

/* End of file LogoutCommand.php */
