<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FiltersCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('filters')
            ->setDescription('List filters for the current user')
            ->requireAuth(true);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        
        $xml = $this->app->fogbugz->listFilters();
        $data = array("filters" => array());
        
        foreach ($xml->filters->children() as $filter) {
          $data["filters"][] = array(
              "name" => (string) $filter,
              "type" => (string) $filter['type'],
              "id"   => (int)    $filter['sFilter']
          );
        }
        
        $template = $this->app->twig->loadTemplate("filters.twig");
        $view = $template->render($data);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file FiltersCommand.php */
