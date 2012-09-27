# EXPERIMENTAL SYMFONY BRANCH

This branch is a complete rewrite of the script using the [Symfony Console][sc].

## TODO

* In recent command, asterisks next to the current case
* Status color codes
* Relocate config and recent files
* http://empir.sourceforge.net/ build a phar for distribution
  https://github.com/fabpot/PHP-CS-Fixer/blob/master/Symfony/CS/Util/Compiler.php
  https://github.com/fabpot/PHP-CS-Fixer/blob/master/Symfony/CS/Console/Command/SelfUpdateCommand.php
  https://github.com/koto/phar-util



# FogBugz Command Line Client

A simple command line client application that can be used to manage your FogBugz
account, working on status, read cases, and leave short notes.

## Help
     fb <command> <value> <value>
    
    Information:
     help (command)                :: More information about a task
     recent                        :: Get the five most recent cases you've worked on
     current                       :: Get the number for your current case
     view (#case#)                 :: Get info about the current or a particular case
     cases                         :: Get a list of cases in your current active filter
     filters                       :: Get a list of available filters
    
    Editing:
     setfilter (#filter#)          :: Set the current active filter
     estimate (#case#) (#hours#)   :: Set the estimate for a case
     note (#case#) ("note string") :: Set a note for a particular case
     start (#case#)                :: Start working on a case
     stop                          :: Stop all work
     
## Setup

I use this with a bash alias:

    alias fb='php ~/Projects/fogbugz-php-cli/working.php'
    
    
[sc]: http://symfony.com/doc/current/components/console.html