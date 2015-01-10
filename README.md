# FogBugz Command Line Client [![Build Status](https://secure.travis-ci.org/there4/fogbugz-php-cli.png)](http://travis-ci.org/there4/fogbugz-php-cli)
> Manage FogBugz cases from the command line

A simple command line client application that can be used to manage your FogBugz
account, working on status, read cases, and leave short notes. This is built
using the [Symfony Console][sc].

## Quick Start

Run the `fb` phar file from the command line. It will prompt you for host, user,
and password. It will store an API token in a .fogbugz.yml file.

## Help

    FogBugz Command Line Client version 1.2.5 by Craig Davis

    Usage:
      [options] command [arguments]

    Options:
      --help           -h Display this help message.
      --quiet          -q Do not output any message.
      --verbose        -v|vv|vvv Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
      --version        -V Display this application version.
      --ansi              Force ANSI output.
      --no-ansi           Disable ANSI output.
      --no-interaction -n Do not ask any interactive question.

    Available commands:
      assign       Assign a case to a different user
      cases        Show cases for the current filter
      close        Close a case
      current      Display the current working case
      estimate     Set the working time estimate for a case
      filters      List filters for the current user
      help         Displays help for a command
      list         Lists commands
      login        Establish a session with FogBugz
      logout       End the session with FogBugz
      note         Leave a note on a case
      open         Open a case in your local browser
      parent       View the parent of a case
      ps1          Display the current working case
      reactivate   Reactivate a case. (The opposite of resolving a case)
      recent       Show cases you have recently worked on.
      reopen       Reopen a Case
      resolve      Resolve a case
      search       Search by keyword
      selfupdate   Updates fb.phar to the latest version.
      setfilter    Set a FogBugz search filter
      setup        Configure this FogBugz client
      star         Star a case
      start        Start working on a case
      stop         Stop your current working case.
      unstar       Remove the star from a case
      version      Show version information
      view         View a case by case number

## Setup

You don't have to clone this repo. You can [download the phar file][dlfb] and save it
to your computer as `fb` somewhere in your `PATH`.

This app stores a config file with host, user, an api token and other
settings. If you'd like to change this path, set a env var for
`FOGBUGZ_CONFIG` for a value such as `~/.fogbugz.yml` to store the
config file in you home directory.

    `env FOGBUGZ_CONFIG='~/.fogbugz.yml'`

You can either locate the `fb` phar file in a bin
path, or add an alias to your bash config for the file

    `alias fb='~/Projects/fogbugz-php-cli/fb'`

## Dependencies

This app requires PHP 5.3. If you need to run on PHP 5.2, you can use the [older
cli branch](https://github.com/there4/fogbugz-php-cli/tree/php-5.2) that has
fewer depencies, but similar basic functionality.

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

## Troubleshooting

* `date_default_timezone_get())` : Some users are getting messages about system
  timezones. This is a problem with your php config. Please see the
  [PHP Docs][date] for information about fixing this message.


[date]: http://us3.php.net/date_default_timezone_get
[dlfb]: https://github.com/there4/fogbugz-php-cli/raw/master/fb
[sc]: http://symfony.com/doc/current/components/console.html
[composer]: http://getcomposer.org/
[pake]: https://github.com/indeyets/pake/wiki
