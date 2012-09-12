<?php

class Commands {

  private $version = 2;

  private $fogbugz;
  
  private $path;
  
  private $recent_file;
  
  private $user;
  
  private $version_info = "";
  
  private $command_map = array();

  public function __construct($user, $pass, $host, $path) {
  
    $this->command_map = array(
      'start'    => 'startWork',
      'stop'     => 'stopWork',
      'note'     => 'leaveNote',
      'view'     => 'showInfo',
      'estimate' => 'setEstimate',
      'current'  => 'showCurrent',
      '-help'    => 'showHelp',
      'help'     => 'showHelp',
      'recent'   => 'showRecent',
      'filters'  => 'listFilters',
      'setfilter'=> 'setFilter',
      'cases'    => 'curFilterCases'
    );
  
    $this->version_info = realpath(__DIR__ . '/../help/version.txt');
  
    $this->fogbugz = new FogBugz($user, $pass, $host);
    try {
      $this->fogbugz->logon();
    }
    catch(FogBugzLogonError $e) {
      echo($e->getMessage() . "\n");
      exit(1);
    }
    $this->user         = $user;
    $this->user_version = $path . '/' . md5($user + "fbcli") . '.ver';
    $this->recent_file  = $path . '/' . md5($user + "fbcli" + $this->version) . '.csv';

    // check to see if the client has been updated since we saw them last
    $last_version = 0;
    if (is_readable($this->user_version)) {
      $last_version = trim(file_get_contents($this->user_version));
      if ($this->version > $last_version && is_readable($this->version_info)) {
        include $this->version_info;
        echo "\n";
      }
    }
    file_put_contents($this->user_version, $this->version);
  }

  public function dispatch($arguments) {
    
    // accept three arguments, in this order
    $task  = (!empty($arguments[1])) ? $arguments[1] : null;
    $case  = (!empty($arguments[2])) ? $arguments[2] : null;
    $value = (!empty($arguments[3])) ? $arguments[3] : null;

    // Find matching method for the command and call it with the
    // coorect number of parameters by using reflection.
    // Yes, this could be more effecient, but this is more fun.
    if (array_key_exists($task, $this->command_map)
        && ($method = $this->command_map[$task])
        && method_exists($this, $method)
    ) {
      $reflector = new ReflectionClass(__class__);
      $param_count = count($reflector->getMethod($method)->getParameters());
      switch($param_count) {
        case 0: return $this->$method(); break;
        case 1: return $this->$method($case); break;
        case 2: return $this->$method($case, $value); break;
      }
    }
    else {
      $this->showHelp();
    }
    return;
  }
  
  /**
   * Start work on a case. If you do not supply a case, you will be prompted
   * with a list of your five most recent cases, and be allowed to enter a
   * new number.
   *
   * If a case does not have an estimate, you are required by FogBugz to
   * supply one.
   */
  public function startWork($case = null) {
    $recent_cases = $this->getRecent();
    if ($case == null) {
      echo "What case are you working on?\n";
      $strlen = 4;
      if (!empty($recent_cases)) {
        foreach ($recent_cases as $recent_case) {
          printf(
              "  [%s] %s\n",
              $recent_case[0],
              substr($recent_case[1], 0, 75)
          );
          // this is just for display purposes below
          $strlen = strlen($recent_case[0]);
        }
      }
      while ($case == null) {
        echo "  [", str_repeat('#', $strlen), "] Or type any other case number to start work\n";
        $case = IO::getOrQuit("Case number:", "number");
      }
    }
    
    try {
      $this->fogbugz->startWork(array('ixBug' => $case));
      $bug = $this->fogbugz->search(array(
          'q'    => (int) $case,
          'cols' => 'sTitle,sStatus,sLatestTextSummary'
      ));
      $title          = (string) $bug->cases->case->sTitle;
      $recent_cases[] = array($case, $title);
      $this->setRecent($recent_cases);
      printf("Now working on [%d]\n  %s\n", $case, $title);
    }
    catch (Exception $e) {
      if($e->getCode() == '7') {
        if ($e->getMessage() == 'Case ' . $case  . ' has no estimate') {
          printf(
              "Case %s has no estimate.\n",
              $case
          );
          return $this->setEstimate($case);
        }
        elseif ($e->getMessage() == 'Closed') {
          printf("Sorry, Case %s is closed and may not have a time interval added to it.\n", $case);
        }
        else {
          printf("%s\n", $e->getMessage());
        }
      }
      else {
        printf("%s\n", $e->getMessage());
      }
      exit(1);
    }
  }
  
  /**
   * Stop working on the currently active case
   */
  public function stopWork() {
    try {
      $this->fogbugz->stopWork();
      echo "Work has stopped\n";
    }
    catch (Exception $e) {
      printf("%s\n", $e->getMessage());
      exit(1);
    }
  }
  
  /**
   * Leave a message on a case. If you do not specify a case, your
   * currently active case will be used. If you do not have an
   * active case, then you will be prompted for one.
   */
  public function leaveNote($case, $note = null) {
    // fb note "string message" and so we swap case and note
    if (!is_numeric($case)) {
      $note = $case;
      list($case, $title) = $this->getCurrent();
      if (empty($case)) {
        $case = IO::getOrQuit("Enter a case number:", "number");
      }
    }
    
    if (empty($note)) {
      $note = IO::getOrQuit("Please supply a note:", "string");
    }
    
    try {
      $this->fogbugz->edit(array(
          'ixBug'  => $case,
          'sEvent' => $note
      ));
      printf(
          "Left a note on case %s\n",
          $case
      );
    }
    catch (Exception $e) {
      printf("%s\n", $e->getMessage());
      exit(1);
    }
  }
  
  /**
   * Set the estimate for a given case.
   * Both case and estimate are optional
   */
  public function setEstimate($case = null, $estimate = null) {
    if ($case == null) {
      $case = IO::getOrQuit(
          "Please provide a case:",
          "number"
      );
    }
    if ($estimate == null) {
      $estimate = IO::getOrQuit(
          "Please enter an estimate for this case in hours:",
          "number"
      );
    }

    try {
      $this->fogbugz->edit(array('ixBug' => $case, 'hrsCurrEst' => $estimate));
    }
    catch (Exception $e) {
      printf("%s\n", $e->getMessage());
      exit(1);
    }
    $this->startWork($case);
    printf(
        "Starting work on case %s with an estimate of %s hours.\n",
        $case, $estimate
    );
  }
  
  /**
   * Display a short summary of a case. If you call this without a case number
   * it will default to your current active case. If you do not have an active
   * case, this will prompt you for a case.
   */
  public function showInfo($case = null) {
    if (null == $case) {
      list($case, $title) = $this->getCurrent();
      if ($case == null) {
        $case = IO::getOrQuit("Enter a case number:", "number");
      }
    }

    try {
      $bug = $this->fogbugz->search(array(
          'q'    => (int) $case,
          'cols' => 'ixBug,sTitle,sStatus,sLatestTextSummary,sProject,sArea,'
                    . 'sPersonAssignedTo,sStatus,sPriority,sCategory,'
                    . 'dtOpened,dtResolved,dtClosed,dtLastUpdated,'
                    . 'sFixFor'
      ));
    }
    catch (Exception $e) {
      printf("%s\n", $e->getMessage());
      exit(1);
    }
    
    if (0 == $bug->cases['count']) {
      printf("Unable to retrieve [%d]\n", $case);
      exit(0);
    }
    
    // extract the case to local vars and then include the template
    $info = $bug->cases->case;
    foreach(get_object_vars($info) as$property => $value) {
      $$property = (string) $value;
    }
    $host = $this->config['host'];
    include realpath(__DIR__ . "/../templates/info.php");
    echo "\n";
  }
  
  /**
   * This prints the current case to stdout without any whitespace
   * or padding. This is intended for use in other batch files. If
   * there is no current case, print "-"
   */
  public function showCurrent($format = NULL) {
    list($case, $title) = $this->getCurrent();
    
    if ($format == NULL) {
      $format = "[%d] %s\n";
    }
    
    if ($case) {
      printf(
          $format,
          $case, $title
      ); 
    }
    else {
      echo "-";
      exit(1);
    }
  }

  /**
   * Show the 5 most recently active cases (from this tool)
   * Your current task (if any) is marked with an asterisk
   */
  public function showRecent() {
    $recent_cases = $this->getRecent();
    $current_case = $this->getCurrent();
    if (count($recent_cases)) {
      foreach ($recent_cases as $recent_case) {
        $line = "  [%s] %s\n";
        if ($recent_case[0] == $current_case[0]) {
          $line = " *[%s] %s\n";
        }
        printf(
            $line,
            $recent_case[0],
            substr($recent_case[1], 0, 75)
        );
      }
      return;
    }
    echo "No recent cases with this tool\n";
  }

  /**
   * fb <command> <value> <value>
   *
   *Information:
   * help (command)                :: More information about a task
   * recent                        :: Get the five most recent cases you've worked on
   * current                       :: Get the number for your current case
   * view (#case#)                 :: Get info about the current or a particular case
   * cases                         :: Get a list of cases in your current active filter
   * filters                       :: Get a list of available filters
   *
   *Editing:
   * setfilter (#filter#)          :: Set the current active filter
   * estimate (#case#) (#hours#)   :: Set the estimate for a case
   * note (#case#) ("note string") :: Set a note for a particular case
   * start (#case#)                :: Start working on a case
   * stop                          :: Stop all work
   */
  public function showHelp($task = null) {
    if (array_key_exists($task, $this->command_map)) {
      $method = $this->command_map[$task];
    }
    else {
      $method = 'showHelp';
    }
    $rc = new ReflectionClass(__class__);
    $help = $rc->getMethod($method)->getDocComment();
    $help = str_replace(
        array(
            "/**\n",
            '   */',
            '   *',
            '   *'
        ),
        '',
        $help
    );
    if ($task == null) {
      $task = "General use";
    }
    echo "\n", $task, ":\n", $help, "\n";
  }

  private function getRecent() {
    if (is_readable($this->recent_file)) {
      return json_decode(file_get_contents($this->recent_file));
    }
    return array();
  }
  
  private function setRecent($cases) {
    $cases   = array_slice($cases, -5);
    file_put_contents(
        $this->recent_file,
        json_encode($cases)
    );
  }

  private function getCurrent() {
    $case   = null;
    $title  = null;
    $xml    = $this->fogbugz->viewPerson(array('sEmail' => $this->user));
    $bug_id = $xml->people->person->ixBugWorkingOn;
    
    if (!empty($bug_id) && (0 != $bug_id)) {
      $bug = $this->fogbugz->search(array(
          'q'    => (int) $bug_id,
          'cols' => 'sTitle,sStatus'
      ));
      
      $case  = (int) $bug_id;
      $title = (string) $bug->cases->case->sTitle;
    }
    return array($case, $title);
  }

  /**
   * Obtain a list of available filters
   * 
   * No parameters accepted
   * Command: fb filters
   */
  private function listFilters() {
    // fetch the list of filters available on fogbugz
    $xml = $this->fogbugz->listFilters();
    include realpath(__DIR__ . "/../templates/listFilters.php");
  }

  /**
   * Change the current active filter to <value>
   * 
   * Param: Filter ID
   * Command: fb setfilter <value>
   */
  private function setFilter($filter) {
    if (null == $filter) {
      $this->listFilters();
      $filter = IO::getOrQuit("Enter a filter number:", "string");
    }    
    
    $this->fogbugz->setCurrentFilter(array('sFilter' => $filter));

    printf(
        "Set the current active filter to: %s\n",
        $filter
    );
  }

  /**
   * Show all cases in the current active filter
   * 
   * No parameters accepted
   * Command: fb cases
   */
  private function curFilterCases() {
    $xml = $this->fogbugz->search(array(
        //'q' => '',
        'cols' => 'ixBug,sStatus,sTitle,hrsCurrEst,sPersonAssignedTo'
    ));

    include realpath(__DIR__ . "/../templates/caseList.php");
  }
  
}

/* End of file working.php */