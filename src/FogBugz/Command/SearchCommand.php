<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('search')
            ->setDescription('Search by keyword')
            ->addArgument('term', InputArgument::OPTIONAL, 'Search term')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog = new DialogHelper();

        $keyword = $input->getArgument('term');

        if (null === $keyword) {
            $keyword = $dialog->ask($output, 'Enter a search string: ');
        }

        $xml = $this->app->fogbugz->search(
            array(
                'q'    => $keyword,
                'cols' => 'ixBug,sStatus,sTitle,hrsCurrEst,sPersonAssignedTo'
            )
        );

        $tplData = array(
            'filterTitle' => "Search resultes for '$keyword'",
            'cases'       => array()
        );

        foreach ($xml->cases->children() as $case) {
            // Colorize the search term in the title of the case
            $title = (string) $case->sTitle;
            $title = str_ireplace(
                $keyword,
                sprintf('<fire>%s</fire>', strtoupper($keyword)),
                $title
            );

            $tplData['cases'][] = array(
                'id'           => (int) $case->ixBug,
                'status'       => (string) $case->sStatus,
                'statusFormat' => $this->app->statusStyle((string) $case->sStatus),
                'title'        => $title,
                'estimate'     => (string) $case->hrsCurrEst,
                'assigned'     => (string) $case->sPersonAssignedTo
            );
        }

        $template = $this->app->twig->loadTemplate('cases.twig');
        $view = $template->render($tplData);
        $output->write($view, true, $this->app->outputFormat);
    }
}

/* End of file SearchCommand.php */
