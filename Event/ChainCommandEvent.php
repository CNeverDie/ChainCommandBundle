<?php

namespace Borovets\ChainCommandBundle\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ChainCommandEvent
 * @package Borovets\ChainCommandBundle\Event
 *
 * This event class contain main command in chain and running command
 */
class ChainCommandEvent extends ConsoleCommandEvent
{
    /**
     * @var Command
     */
    private $currentCommand;

    /**
     * ChainCommandEvent constructor.
     * @param Command $mainCommand
     * @param Command|null $currentCommand
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(Command $mainCommand, Command $currentCommand = null, InputInterface $input, OutputInterface $output)
    {
        parent::__construct($mainCommand, $input, $output);
        $this->currentCommand = $currentCommand;
    }

    /**
     * Return command which is the execution in the chain
     *
     * @return Command|null
     */
    public function getCurrentCommand()
    {
        return $this->currentCommand;
    }
}