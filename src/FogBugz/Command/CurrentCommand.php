<?php
namespace FogBugz\Command;

// http://symfony.com/doc/current/components/console.html

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrentCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('current')
            ->setDescription('Display the current working case')
            ->addArgument('format', InputArgument::OPTIONAL, 'Output format, in sprintf format.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        
        $format = $input->getArgument('format');
        $case   = null;
        $title  = null;
        $xml    = $this->app->fogbugz->viewPerson(array('sEmail' => $this->app->config['User']));
        $bug_id = $xml->people->person->ixBugWorkingOn;
        
        if (!empty($bug_id) && (0 != $bug_id)) {
          $bug = $this->app->fogbugz->search(array(
              'q'    => (int) $bug_id,
              'cols' => 'sTitle,sStatus'
          ));
          
          $case  = (int) $bug_id;
          $title = (string) $bug->cases->case->sTitle;
        }

        if ($format == NULL) {
            $format = "[%d] %s";
        }

        if ($case) {
            $output->writeln(sprintf(
                $format,
                $case, $title
            ));
        }
        else {
            $output->writeln("-");
        }
    }
}

/* End of file CurrentCommand.php */
