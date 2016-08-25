<?php

namespace Borovets\ChainCommandBundle\Tests\Fixtures\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Hi2Command extends Command
{
    protected function configure()
    {
        $this
            ->setName('bar:hi2');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hi2 from Bar!');
    }
}