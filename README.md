# EXPERIMENTAL SYMFONY BRANCH

This branch is a complete rewrite of the script using the [Symfony Console][sc].

## TODO

* Relocate config and recent files
* http://empir.sourceforge.net/ build a phar for distribution
  https://github.com/fabpot/PHP-CS-Fixer/blob/master/Symfony/CS/Util/Compiler.php
  https://github.com/fabpot/PHP-CS-Fixer/blob/master/Symfony/CS/Console/Command/SelfUpdateCommand.php
  https://github.com/koto/phar-util



# FogBugz Command Line Client

A simple command line client application that can be used to manage your FogBugz
account, working on status, read cases, and leave short notes.

## Help

    FogBugz Command Line Client version 1.0.0 by Craig Davis
    
    Usage:
      [options] command [arguments]
    
    Options:
      --help           -h Display this help message.
      --quiet          -q Do not output any message.
      --verbose        -v Increase verbosity of messages.
      --version        -V Display this application version.
      --ansi              Force ANSI output.
      --no-ansi           Disable ANSI output.
      --no-interaction -n Do not ask any interactive question.
    
    Available commands:
      cases       Show the cases for the current filter
      current     Display the current working case
      estimate    Set a the working estimate for a case
      filters     List filters for the current user
      help        Displays help for a command
      list        Lists commands
      login       Establish a session with FogBugz
      logout      End the session with FogBugz
      note        Leave a note on a case
      open        Open a case in your local browser
      parent      View a Parent Case
      recent      Show cases you've recently worked on.
      resolve     Resolve a case
      search      Search by keyword
      setfilter   Set a FogBugz search filter
      setup       Configure this FogBugz client
      start       Start working on a case
      stop        Stop your current working case.
      version     Show version information
      view        View a Case
    
## Setup

I use this with a bash alias:

    alias fb='php ~/Projects/fogbugz-php-cli/working.php'
    
    
[sc]: http://symfony.com/doc/current/components/console.html