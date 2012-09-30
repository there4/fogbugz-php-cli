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
            ->requireAuth(true)
            ->setHelp(
<<<EOF
The <info>%command.name%</info> command displays the filters available for the
current user. This is saved at FogBugz, and any subsequent 'cases' command will
return the list of cases within the filter. 

This filter command only lists the available filters. If you'd like to see a
list of filters and then select one, use `setfilter` without a parameter. It
will display a list and allow you to select one.

EOF
            );
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
                "id"   => (int) $filter['sFilter']
            );
        }

        $template = $this->app->twig->loadTemplate("filters.twig");
        $view = $template->render($data);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file FiltersCommand.php */
