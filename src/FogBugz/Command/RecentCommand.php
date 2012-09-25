<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecentCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('recent')
            ->setDescription("Show cases you've recently worked on.")
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app    = $this->getApplication();
        $recentCases = $this->app->getRecent();

        $template = $this->app->twig->loadTemplate("recent.twig");
        $view = $template->render($recentCases);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file RecentCommand.php */
