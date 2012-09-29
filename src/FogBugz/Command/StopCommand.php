<?php
namespace FogBugz\Command;

use There4\FogBugz\ApiError;
use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('stop')
            ->setDescription('Stop your current working case.')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        try {
            $this->app->fogbugz->stopWork();
        } catch (ApiError $e) {
            $output->writeln(
                sprintf("<error>%s</error>", $e->getMessage()),
                $this->app->outputFormat
            );
            exit(1);
        }
        $output->writeln(
            "Work has stopped.",
            $this->app->outputFormat
        );
    }
}

/* End of file StopCommand.php */
