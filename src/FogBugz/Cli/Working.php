<?php
namespace FogBugz\Cli;

use FogBugz\Cli;
use FogBugz\Command;
use There4\FogBugz;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Yaml\Yaml;

class Working extends Application
{

  public function __construct($baseDir)
  {

    // Add the composer information for use in version info and such.
    $this->project = json_decode(file_get_contents($baseDir . '/composer.json'));

    // Load our application config information
    $this->config = Yaml::parse($baseDir . '/.config.yml');

    // We do this now because we've loaded the project info from the composer file
    parent::__construct($this->project->description, $this->project->version);

    // Load our commands into the application
    foreach (glob($baseDir ."/src/FogBugz/Command/*.php") as $filename) {
      $classname = "Fogbugz\Command\\" . basename($filename, ".php");
      require $filename;
      $this->add(new $classname);
    }
    
    // TODO: If the config file is empty, run the setup script here:
    
  }
  
  public function run(InputInterface $input = null, OutputInterface $output = null)
  {
    if (null === $input) {
      $input = new ArgvInput();
    }

    if (null === $output) {
      $output = new ConsoleOutput();
    }

    

    // Does the command require authentication?
    $name = $this->getCommandName($input);
    $command = $this->find($name);
    if ($command->requireAuth) {
      $login = $this->find('login');
      $returnCode = $login->run($input, $output);
    }
    
    return parent::run($input, $output);
  }

}

/* End of file Working.php */