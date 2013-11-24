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
            ->setDescription('Show cases you have recently worked on.')
            ->requireAuth(true)
            ->setHelp(
<<<EOF
The <info>%command.name%</info> will display the most recent cases that you've worked on with
the 'start' command. The data is stored locally in the config file, and so
if you've used the web, those cases won't show up here.

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app   = $this->getApplication();
        $recentCases = array(
            'cases'   => $this->app->getRecent(),
            'current' => $this->app->getCurrent()
        );

        $template = $this->app->twig->loadTemplate('recent.twig');
        $view = $template->render($recentCases);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file RecentCommand.php */
