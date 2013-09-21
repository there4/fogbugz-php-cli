[![Build Status](https://secure.travis-ci.org/there4/fogbugz-php-cli.png)](http://travis-ci.org/there4/fogbugz-php-cli)

# EXPERIMENTAL SYMFONY BRANCH

This branch is a complete rewrite of the script using the [Symfony Console][sc].

# FogBugz Command Line Client

A simple command line client application that can be used to manage your FogBugz
account, working on status, read cases, and leave short notes.

## Quick Start

Run the `fb` phar file from the command line. It will prompt you for host, user,
and password. It will store an API token in a .fogbugz.yml file.

## Help

    Console Tool by Craig Davis
    
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
      ps1         Display the current working case
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

This app stores a config file with host, user, an api token and other
settings. If you'd like to change this path, set a env var for
`FOGBUGZ_CONFIG` for a value such as `~/.fogbugz.yml` to store the
config file in you home directory. 

    env FOGBUGZ_CONFIG='~/.fogbugz.yml'

You can either locate the `fb` phar file in a bin
path, add an alias to your bash config for the file

    alias fb='~/Projects/fogbugz-php-cli/fb'

## Development TODO

* Resolve: select the destination user from a list
* Login: Test auth token and re-auth on failure
* Setup: Validate the host url with an api touch
* New [Self Update][update] command.
* Consider Kiln [branch commands][kiln]

## Developing New Commands

The project depends on [Composer][composer] to load dependencies. Once you've
got that, run `composer install` to load the the required libraries. From this
point, you should be able to develop by running `php working.php`.

If you'd like to add new commands to the repo, see the `src/FogBugz/Command`
directory. After creating a new command file, add it to the 
`src/FogBugz/Cli/Working.php` file around line 50 where we init the commands.

## Building the Phar

If you'd like to rebuild the phar file, you'll need [Pake][pake]. Once you've
got that, you'll need to run `pake build`. It will clean the files, run some
linters, and then finally build the phar file.

[sc]: http://symfony.com/doc/current/components/console.html
[composer]: http://getcomposer.org/
[pake]: https://github.com/indeyets/pake/wiki
[kiln]: https://developers.fogbugz.com/default.asp?W166

[update]: https://github.com/composer/composer/blob/master/src/Composer/Command/SelfUpdateCommand.php
