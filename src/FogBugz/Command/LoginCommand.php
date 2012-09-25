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

        if (file_exists($this->app->tokenPath)) {
          $tokenInfo = json_decode(file_get_contents($this->app->tokenPath));
          touch($this->app->tokenPath);
          $this->app->fogbugz = new FogBugz\Api($tokenInfo->user, '', $tokenInfo->host);
          $this->app->fogbugz->token = $tokenInfo->token;
          
          // TODO: Test this token, and re-prompt if it fails
          return;
        }

        $output->writeln("\n<comment>Please Login to FogBugz</comment>");

        $host = $dialog->ask(
            $output,
            " * Url: ",
            "https://"
        );

        $user = $dialog->ask(
            $output,
            " * Email address: ",
            getenv("GIT_AUTHOR_EMAIL")
        );

        // TODO: HIDE THIS FROM USERS, see sample:
        // http://www.sitepoint.com/interactive-cli-password-prompt-in-php/
        // or
        // $pwd = preg_replace('/\r?\n$/', '', `stty -echo; head -n1 ; stty echo`);
        // on Win32, we'll have to continue to use this.
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
            // TODO: convert this to YAML to match the other config
            $tokenFile = json_encode(array(
                'user'  => $user,
                'host'  => $host,
                'token' => $this->app->fogbugz->token
            ));
            file_put_contents(
                $this->app->tokenPath,
                $tokenFile
            );
        }
        catch(FogBugz\ApiLogonError $e) {
            $output->writeln("\n<error>" . $e->getMessage() . "</error>\n");
            exit(1);
        }

        // Write the config and the token out to the config file

    }
}

/* End of file LoginCommand.php */
