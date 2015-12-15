<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
            ->setDescription('Show cases for the current filter')
            ->addArgument('tree', InputArgument::OPTIONAL, 'Output as a hierarchy.')
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
        $tree  = $input->getArgument('tree') == 'tree';

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
                'cols' => 'ixBug,ixBugParent,sStatus,sTitle,hrsCurrEst,sPersonAssignedTo'
            )
        );

        $data = array(
            'filterTitle' => $filterTitle,
            'cases'       => array()
        );

        foreach ($xml->cases->children() as $case) {
            $data['cases'][] = array(
                'id'           => (int) $case->ixBug,
                'parent'       => (int) $case->ixBugParent,
                'status'       => (string) $case->sStatus,
                'statusFormat' => $this->app->statusStyle((string) $case->sStatus),
                'title'        => (string) $case->sTitle,
                'estimate'     => (string) $case->hrsCurrEst,
                'assigned'     => (string) $case->sPersonAssignedTo,
                'children'     => array()
            );
        }

        if ($tree) {
            $data['cases'] = $this->parseTree($data['cases']);
        }

        $template = $this->app->twig->loadTemplate('cases.twig');
        $view = $template->render($data);
        $output->write($view, false, $this->app->outputFormat);
    }

    // http://stackoverflow.com/a/2916150/14651
    protected function parseTree($array) { // Faster - but no spacing info
        $temp = array();
        $tree = array();

        foreach ($array as $item) {
            $temp[$item['id']] = $item;
        }

        foreach ($array as $item) {
            $id     = $item['id'];
            $parent = $item['parent'];

            if ($parent == 0) {
                $tree[$id] = &$temp[$id];
            } else {
                $temp[$parent]['children'][$id] = &$temp[$id];
            }
        }

        return $tree;
    }
}

/* End of file CasesCommand.php */
