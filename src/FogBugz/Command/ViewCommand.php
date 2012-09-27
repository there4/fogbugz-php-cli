<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ViewCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('view')
            ->setDescription('View a Case')
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
                $case = $dialog->ask($output, "Enter a case number: ");
            }
        }

        try {
            $bug = $this->app->fogbugz->search(
                array(
                    'q'    => (int) $case,
                    'cols' => 'ixBug,sTitle,sStatus,sLatestTextSummary,'
                              . 'sProject,sArea,sPersonAssignedTo,sStatus,'
                              . 'sPriority,sCategory,dtOpened,dtResolved,'
                              . 'dtClosed,dtLastUpdated,sFixFor,ixBugParent'
                    )
            );
        } catch (Exception $e) {
            $output->writeln(
                sprintf("<error>%s</error>", $e->getMessage()),
                $this->app->outputFormat
            );
            exit(1);
        }

        if (0 == $bug->cases['count']) {
            $output->writeln(
                sprintf("<error>Unable to retrieve [%d]</error>", $case),
                $this->app->outputFormat
            );
            exit(1);
        }

        // extract the case to local vars and then include the template
        $info = $bug->cases->case;
        $data = array();
        foreach (get_object_vars($info) as $property => $value) {
            $data[$property] = (string) $value;
        }
        $data['host'] = $this->app->fogbugz->url;

        if ($data['ixBugParent'] == 0) {
            $data['ixBugParent'] = '—';
        }

        $data['statusFormat'] = $this->app->statusStyle($data['sStatus']);

        $template = $this->app->twig->loadTemplate("info.twig");
        $view = $template->render($data);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file ViewCommand.php */
