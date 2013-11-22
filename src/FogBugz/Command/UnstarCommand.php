<?php
namespace FogBugz\Command;

use Symfony\Component\Console\Input\InputArgument;

class UnstarCommand extends StarCommand
{
    public $action = 'unstar';
    public $message = 'Case %d has been unstarred.';

    protected function configure()
    {
        $this
            ->setName('unstar')
            ->setDescription('Remove the star from a case')
            ->addArgument(
                'case',
                InputArgument::OPTIONAL,
                'Case number, defaults to current active case.'
            )
            ->requireAuth(true);
    }
}

/* End of file UnstarCommand.php */
