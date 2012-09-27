<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrentCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('current')
            ->setDescription('Display the current working case')
            ->addArgument('format', InputArgument::OPTIONAL, 'Output format, in sprintf format.')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();

        $format = $input->getArgument('format');
        $case   = null;
        $title  = null;
        $xml    = $this->app->fogbugz->viewPerson(array('sEmail' => $this->app->fogbugz->user));
        $bug_id = $xml->people->person->ixBugWorkingOn;

        if (!empty($bug_id) && (0 != $bug_id)) {
            $bug = $this->app->fogbugz->search(
                array(
                    'q'    => (int) $bug_id,
                    'cols' => 'sTitle,sStatus'
                )
            );

            $case  = (int) $bug_id;
            $title = (string) $bug->cases->case->sTitle;
        }

        if ($format == null) {
            $format = "[%d] %s";
        }

        if ($case) {
            $output->writeln(
                sprintf($format, $case, $title),
                $this->app->outputFormat
            );
        } else {
            $output->writeln("-", $this->app->outputFormat);
        }
    }
}

/* End of file CurrentCommand.php */
