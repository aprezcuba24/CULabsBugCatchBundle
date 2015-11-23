<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\BugCatchBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExceptionCommand extends Command
{
    protected function configure()
    {
        $this->setName('bugCatch:testException');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception();
    }
}