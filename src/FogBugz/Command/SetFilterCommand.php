<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class SetFilterCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('setfilter')
            ->setDescription('Set a FogBugz search filter')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Filter number, if omitted a list is displayed.')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog    = new DialogHelper();
        $filter    = $input->getArgument('filter');

        if (null === $filter) {
            $command = $this->getApplication()->find('filters');
            $arguments = array(
                'command' => 'filters'
            );
            $input = new ArrayInput($arguments);
            $command->run($input, $output);

            $filter = $dialog->ask($output, "Enter a filter number: ");
        }

        $this->app->fogbugz->setCurrentFilter(array('sFilter' => $filter));

        $output->writeln(
            sprintf(
                  "Set the current active filter to: %s",
                  $filter
            ), $this->app->outputFormat
        );
    }
}

/* End of file SetFilterCommand.php */
