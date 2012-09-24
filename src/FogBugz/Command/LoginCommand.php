<?php
namespace FogBugz\Command;

use FogBugz\Cli\AuthCommand;
use There4\FogBugz;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoginCommand extends AuthCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('login')
            ->setDescription('Establish a session with FogBugz')
            ->requireAuth(false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $dialog = new DialogHelper();
      
        // TODO: check for a token in the token store, if it's there, try to use it.
        //       if it fails, then login with the code below.
        //       once we've logged in, set the new token into the token store
      
        $output->writeln("\n<comment>Please Login to FogBugz</comment>");
        
        $host = $dialog->ask(
            $output,
            " * Url: "
        );
        
        $user = $dialog->ask(
            $output,
            " * Email address: ",
            getenv("GIT_AUTHOR_EMAIL")
        );
        
        $password = $dialog->ask(
            $output,
            " * Password: "
        );
        
        $this->app->fogbugz = new FogBugz\Api(
            $user,
            $password,
            $host
        );
        
        try {
            $this->app->fogbugz->logon();
            $token = $this->app->fogbugz->token;
            $output->writeln("<info>$token</info>");
        }
        catch(FogBugz\ApiLogonError $e) {
            $output->writeln("\n<error>" . $e->getMessage() . "</error>\n");
            exit(1);
        }

        // Write the config and the token out to the config file

    }
}

/* End of file LoginCommand.php */
