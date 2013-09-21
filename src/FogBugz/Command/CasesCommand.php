<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CasesCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cases')
            ->setDescription('Show the cases for the current filter')
            ->requireAuth(true)
            ->setHelp(
<<<EOF
The <info>%command.name%</info> command lists all cases in the current filter.
You should use the setfilter command to change your filter.

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();

        $filterTitle = 'No filter selected';
        $xml = $this->app->fogbugz->listFilters();
        foreach ($xml->filters->children() as $filter) {
            if ((string) $filter['status'] === 'current') {
                $filterTitle = (string) $filter;
                break;
            }
        }

        $xml = $this->app->fogbugz->search(
            array(
                'cols' => 'ixBug,sStatus,sTitle,hrsCurrEst,sPersonAssignedTo'
            )
        );

        $data = array(
            "filterTitle" => $filterTitle,
            "cases" => array()
        );

        foreach ($xml->cases->children() as $case) {
            $data["cases"][] = array(
                "id"           => (int) $case->ixBug,
                "status"       => (string) $case->sStatus,
                "statusFormat" => $this->app->statusStyle((string) $case->sStatus),
                "title"        => (string) $case->sTitle,
                "estimate"     => (string) $case->hrsCurrEst,
                "assigned"     => (string) $case->sPersonAssignedTo
            );
        }

        $template = $this->app->twig->loadTemplate("cases.twig");
        $view = $template->render($data);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file CasesCommand.php */
