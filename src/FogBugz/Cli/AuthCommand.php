<?php
namespace FogBugz\Cli;

use Symfony\Component\Console\Command\Command;

class AuthCommand extends Command
{
  public $requireAuth = false;

  public function requireAuth($bool)
  {
    $this->requireAuth = $bool;
  }

}

/* End of file AuthCommand.php */
