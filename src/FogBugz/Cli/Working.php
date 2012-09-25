<?php
namespace FogBugz\Cli;

use FogBugz\Cli;
use FogBugz\Command;
use There4\FogBugz;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Yaml\Yaml;

class Working extends Application
{

  var $baseDir;
  var $tokenPath;

  public function __construct($baseDir)
  {
  
    $this->baseDir = $baseDir;
    
    $this->tokenPath = $baseDir . "/token.txt";

    // Add the composer information for use in version info and such.
    $this->project = json_decode(file_get_contents($baseDir . '/composer.json'));

    // Load our application config information
    if (!file_exists($baseDir . '/.config.yml')) {
      copy($baseDir . '/.config.dist.yml', $baseDir . '/.config.yml');
    }
    $this->config = Yaml::parse($baseDir . '/.config.yml');

    // We do this now because we've loaded the project info from the composer file
    parent::__construct($this->project->description, $this->project->version);

    // Load our commands into the application
    foreach (glob($baseDir ."/src/FogBugz/Command/*.php") as $filename) {
      $classname = "Fogbugz\Command\\" . basename($filename, ".php");
      require $filename;
      $this->add(new $classname);
    }

    // https://github.com/symfony/Console/blob/master/Output/Output.php
    $this->outputFormat
        = $this->config['UseColor']
        ? OutputInterface::OUTPUT_NORMAL
        : OutputInterface::OUTPUT_PLAIN;

    $loader = new \Twig_Loader_Filesystem($baseDir . '/templates');
    $this->twig = new \Twig_Environment($loader, array(
        "cache" => false,
        "autoescape" => false,
        "strict_variables" => false // SET TO TRUE WHILE DEBUGGING
    ));

    $this->twig->addFilter('pad', new \Twig_Filter_Function("FogBugz\Cli\TwigFormatters::strpad"));
    $this->twig->addFilter('style', new \Twig_Filter_Function("FogBugz\Cli\TwigFormatters::style"));
    $this->twig->addFilter('repeat', new \Twig_Filter_Function("str_repeat"));
    $this->twig->addFilter('wrap', new \Twig_Filter_Function("wordwrap"));
    
    // TODO: If the config file is empty, run the setup script here:

  }
  
  public function registerStyles(&$output) {

    // https://github.com/symfony/Console/blob/master/Formatter/OutputFormatterStyle.php
    // http://symfony.com/doc/2.0/components/console/introduction.html#coloring-the-output
    //
    // * <info></info> green
    // * <comment></comment> yellow
    // * <question></question> black text on a cyan background
    // * <alert></alert> yellow
    // * <error></error> white text on a red background
    // * <fire></fire> red text on a yellow background
    // * <notice></notice> blue
    
    $style = new OutputFormatterStyle('red', 'yellow', array('bold'));
    $output->getFormatter()->setStyle('fire', $style);
    
    $style = new OutputFormatterStyle('blue', 'black', array());
    $output->getFormatter()->setStyle('notice', $style);
    
    $style = new OutputFormatterStyle('red', 'black', array());
    $output->getFormatter()->setStyle('alert', $style);

    return $output;
  }

  public function run(InputInterface $input = null, OutputInterface $output = null)
  {
    if (null === $input) {
      $input = new ArgvInput();
    }

    if (null === $output) {
      $output = new ConsoleOutput();
    }
    
    $this->registerStyles($output);

    // Does the command require authentication?
    $name = $this->getCommandName($input);
    if (!empty($name) && $command = $this->find($name)) {
      if ($command->requireAuth) {
        $simple_input = new ArgvInput(array(
          $_SERVER['argv'][0], $_SERVER['argv'][1]
        ));
        $login = $this->find('login');
        $returnCode = $login->run($simple_input, $output);
      }
    }

    return parent::run($input, $output);
  }

}

/* End of file Working.php */
