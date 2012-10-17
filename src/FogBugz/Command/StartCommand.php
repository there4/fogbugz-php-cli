<?php
namespace FogBugz\Command;

use There4\FogBugz\ApiError;
use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Start working on a case')
            ->addArgument('case', InputArgument::OPTIONAL, 'Case number, will prompt if omitted..')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app   = $this->getApplication();
        $dialog      = new DialogHelper();
        $case        = $input->getArgument('case');
        $recentCases = $this->app->getRecent();

        if ($case == null) {
            $strlen = 4;
            if (!empty($recentCases)) {
                $output->writeln("What case are you working on?", $this->app->outputFormat);
                foreach ($recentCases as $recent_case) {
                    $output->writeln(
                        sprintf(
                            "  <info>[%s]</info> %s",
                            $recent_case['id'],
                            substr($recent_case['title'], 0, 75)
                        ),
                        $this->app->outputFormat
                    );
                    // this is just for display purposes below
                    $strlen = strlen($recent_case['id']);
                }
                $output->writeln(
                    "  <info>[" . str_repeat('#', $strlen) . "]</info> Or type any other case number to start work",
                    $this->app->outputFormat
                );
            }
            while ($case == null) {
                $case = $dialog->ask($output, "Case number: ");
            }
        }

        try {
            // We'll go ahead and look it up, and if we find it, we'll
            // save it to recent. Then, we'll issue the command and catch
            // any problems with it and deal with it then.
            $bug = $this->app->fogbugz->search(
                array(
                    'q'    => (int) $case,
                    'cols' => 'sTitle,sStatus,sLatestTextSummary'
                )
            );
            $title = (string) $bug->cases->case->sTitle;
            $this->app->pushRecent($case, $title);

            $this->app->fogbugz->startWork(array('ixBug' => $case));

            $output->writeln(
                sprintf("Now working on [%d]\n  %s\n", $case, $title),
                $this->app->outputFormat
            );
        } catch (ApiError $e) {
            if ($e->getCode() == '7') {
                if ($e->getMessage() == 'Case ' . $case  . ' has no estimate') {
                    $output->writeln(
                        sprintf(
                            "<alert>Case %s has no estimate.</alert>",
                            $case
                        ),
                        $this->app->outputFormat
                    );

                    // Delegate to the set estimate
                    $command = $this->getApplication()->find('estimate');
                    $arguments = array(
                        'command' => 'estimate',
                        'case' => $case
                    );
                    $input = new ArrayInput($arguments);
                    $command->run($input, $output);

                    // Now come back to start the case.
                    // TODO: move this to call, so we aren't working the catch.
                    $title = (string) $bug->cases->case->sTitle;
                    $this->app->fogbugz->startWork(array('ixBug' => $case));
                    $output->writeln(
                        sprintf("Now working on [%d]\n  %s\n", $case, $title),
                        $this->app->outputFormat
                    );

                    return;
                } elseif ($e->getMessage() == 'Closed') {
                    $output->writeln(
                        sprintf(
                            "<fire>Sorry, Case %s is closed and may not "
                            . "have a time interval added to it.</fire>",
                            $case
                        ),
                        $this->app->outputFormat
                    );
                } else {
                    $output->writeln(
                        sprintf("<error>%s</error>", $e->getMessage()),
                        $this->app->outputFormat
                    );
                }
            } else {
                $output->writeln(
                    sprintf("<error>%s</error>", $e->getMessage()),
                    $this->app->outputFormat
                );
            }
            exit(1);
        }
    }
}

/* End of file StartCommand.php */
