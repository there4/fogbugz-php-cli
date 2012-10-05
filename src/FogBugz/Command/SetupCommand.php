<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class SetupCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Configure this FogBugz client')
            ->requireAuth(false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog    = new DialogHelper();

        // TODO: this should be config dir
        if (file_exists($this->app->configFile)) {
            $this->config = Yaml::parse($this->app->configFile);
        } else {
            $this->config = $this->app->getDefaultConfig();
        }
        $output->writeln(
            sprintf(
                "%s\n<info>%s</info>\n%s\n Config Path: %s\n",
                str_repeat("—", 80),
                str_pad("FogBugz Client Setup", 80, " ", STR_PAD_BOTH),
                str_repeat("—", 80),
                $this->app->configFile
            ),
            $this->app->outputFormat
        );

        // Prompt the values in the config file
        $question = "Enable color output (";
        $question .= !empty($this->config['UseColor']) && $this->config['UseColor']  ? "yes" : "no";
        $question .= "): ";
        $useColor = $dialog->ask($output, $question, $this->config['UseColor']);
        $this->config['UseColor'] = (strtolower($useColor[0]) == 'y');

        // TODO: use validation here for host prompt
        $question = "FogBugz host url (";
        $question
            .= !empty($this->config['Host']) && $this->config['Host']
            ? $this->config['Host']
            : "include https://";
        $question .= "): ";
        $this->config['Host'] = $dialog->ask($output, $question, $this->config['Host']);

        // We can use this config to know if we need to make changes in setup
        $this->config['ConfigVersion'] = $this->app->project->version;

        $this->app->config = $this->config;
        $this->app->saveConfig();

        // Display the alias to use in bash config.
    }
}

/* End of file SetupCommand.php */
