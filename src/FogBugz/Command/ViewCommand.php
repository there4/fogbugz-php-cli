<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\FormatterHelper;
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
            ->addArgument('case', InputArgument::OPTIONAL, 'Case number, defaults to current active case.')
            ->requireAuth(true);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog = new DialogHelper();
        
        $case = $input->getArgument('case');
        
        if (null == $case) {
          // TODO: Get with current active case
          //list($case, $title) = $this->getCurrent();
          if ($case == null) {
            $case = $dialog->ask($output, "Enter a case number: ");
          }
        }
    
        try {
          $bug = $this->app->fogbugz->search(array(
              'q'    => (int) $case,
              'cols' => 'ixBug,sTitle,sStatus,sLatestTextSummary,sProject,sArea,'
                        . 'sPersonAssignedTo,sStatus,sPriority,sCategory,'
                        . 'dtOpened,dtResolved,dtClosed,dtLastUpdated,'
                        . 'sFixFor,ixBugParent'
          ));
        }
        catch (Exception $e) {
          printf("%s\n", $e->getMessage());
          exit(1);
        }
        
        if (0 == $bug->cases['count']) {
          printf("Unable to retrieve [%d]\n", $case);
          exit(0);
        }
        
        // extract the case to local vars and then include the template
        $info = $bug->cases->case;
        foreach(get_object_vars($info) as $property => $value) {
          $$property = (string) $value;
        }
        $host = $this->app->fogbugz->url;
        include realpath($this->app->baseDir . "/templates/info.php");
        echo "\n";

    }
}

/* End of file ViewCommand.php */
