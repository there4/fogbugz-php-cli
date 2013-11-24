<?php
namespace FogBugz\Command;

use There4\FogBugz\ApiError;
use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class CloseCommand extends AuthCommand
{

    protected function configure()
    {
        $this
            ->setName('close')
            ->setDescription('Close a case')
            ->addArgument(
                'case',
                InputArgument::OPTIONAL,
                'Case number, defaults to current active case.'
            )
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog = new DialogHelper();

        $case = $input->getArgument('case');

        if (null == $case) {
            $case = $this->app->getCurrent();
            if ($case == null || $case == 0) {
                $case = $dialog->ask($output, 'Enter a case number: ');
            }
        }

        try {
            $this->app->fogbugz->close(
                array(
                    'ixBug' => $case
                )
            );
        } catch (ApiError $e) {
            $output->writeln(
                sprintf('<error>%s</error>', $e->getMessage()),
                $this->app->outputFormat
            );
            exit(1);
        }
        $output->writeln(
            sprintf('Case %d has been closed.', $case),
            $this->app->outputFormat
        );
    }
}

/* End of file CloseCommand.php */
