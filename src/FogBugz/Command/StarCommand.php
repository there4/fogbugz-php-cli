<?php
namespace FogBugz\Command;

use There4\FogBugz\ApiError;
use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class StarCommand extends AuthCommand
{
    public $action = 'star';
    public $message = 'Case %d has been starred.';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('star')
            ->setDescription('Star a case')
            ->addArgument(
                'case',
                InputArgument::OPTIONAL,
                'Case number, defaults to current active case.'
            )
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $this->action;
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
            $this->app->fogbugz->$action(
                array(
                    'sType' => 'Bug',
                    'ixItem' => $case
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
            sprintf($this->message, $case),
            $this->app->outputFormat
        );
    }
}

/* End of file StarCommand.php */
