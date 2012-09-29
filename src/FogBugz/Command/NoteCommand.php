<?php
namespace FogBugz\Command;

use There4\FogBugz\ApiError;
use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NoteCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('note')
            ->setDescription('Leave a note on a case')
            ->addArgument('case', InputArgument::OPTIONAL, 'Case number, will use current active if omitted.')
            ->addArgument('note', InputArgument::OPTIONAL, 'Message to leave on the case.')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog    = new DialogHelper();
        $case      = $input->getArgument('case');
        $note      = $input->getArgument('note');

        // fb note "string message" and so we swap case and note
        if (!is_numeric($case)) {
            $note = $case;
            $case = $this->app->getCurrent();
            if (empty($case)) {
                $case = $dialog->ask($output, "Enter a case number:");
            }
        }

        if (empty($note)) {
            $note = $dialog->ask(
                $output,
                sprintf("Please supply a note for Case %d:\n", $case)
            );
        }

        try {
            $this->app->fogbugz->edit(
                array(
                    'ixBug'  => $case,
                    'sEvent' => $note
                )
            );
            $output->writeln(
                sprintf("Left a note on case %s", $case),
                $this->app->outputFormat
            );
        } catch (ApiError $e) {
            $output->writeln(
                sprintf("<error>%s</error>", $e->getMessage()),
                $this->app->outputFormat
            );
            exit(1);
        }
    }
}

/* End of file NoteCommand.php */
