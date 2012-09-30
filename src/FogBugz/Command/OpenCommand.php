<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class OpenCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('open')
            ->setDescription('Open a case in your local browser')
            ->addArgument(
                'case',
                InputArgument::OPTIONAL,
                'Case number, defaults to current active case.'
            )
            ->requireAuth(true)
            ->setHelp(
<<<EOF
The <info>%command.name%</info> will open the case in your current web browser.
This uses 'open' on OSX, 'xdg-open' on Linux, and 'start' on Windows.

EOF
            );
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

        $url = trim($this->app->fogbugz->url, "/") . "/default.asp?" . $case;

        switch (PHP_OS) {
            case 'Darwin':
                exec("open $url");
                break;
            case 'WIN32':
            case 'WINNT':
                exec("cmd /c \"start $url\"");
                break;
            case 'Linux':
            case 'Unix':
            case 'NetBSD':
            case 'OpenBSD':
                exec("xdg-open $url");
                break;
            default:
                $output->writeln(
                    "<error>Your operating system (" . PHP_OS .") isn't supported for the open command.</error>",
                    $this->app->outputFormat
                );
        }
    }
}

/* End of file OpenCommand.php */
