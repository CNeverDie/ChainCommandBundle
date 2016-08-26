<?php

namespace Borovets\ChainCommandBundle\Service;

use Borovets\ChainCommandBundle\Event\ChainCommandEvent;
use Borovets\ChainCommandBundle\Event\ChainEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChainManager
{
    /** @var ChainCollection */
    private $chainCollection;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * ChainManager constructor.
     * @param ChainCollection $chainCollection
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ChainCollection $chainCollection, EventDispatcherInterface $eventDispatcher)
    {
        $this->chainCollection = $chainCollection;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return ChainCollection
     */
    public function getChain()
    {
        return $this->chainCollection;
    }

    /**
     * Execute chain
     *
     * @param Command $command
     * @param $input
     * @param $output
     */
    public function runChain(Command $command, $input, $output)
    {
        $event = new ChainCommandEvent($command, null, $input, $output);
        $this->eventDispatcher->dispatch(ChainEvents::CHAIN_INIT, $event);

        $chainMembers = $this->getChainedCommands($command, $input, $output);

        $this->runMainCommand($command, $input, $output);

        $event = new ChainCommandEvent($command, null, $input, $output);
        $this->eventDispatcher->dispatch(ChainEvents::BEFORE_CHAIN_STARTED, $event);

        $this->runChainCommands($command, $chainMembers, $input, $output);

        $event = new ChainCommandEvent($command, null, $input, $output);
        $this->eventDispatcher->dispatch(ChainEvents::CHAIN_FINISHED, $event);
    }

    /**
     * Get chained command by main command name
     *
     * @param string|Command $mainCommand
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    private function getChainedCommands(Command $mainCommand, $input, $output)
    {
        $chainMembers = $this->chainCollection->getChainSubCommands($mainCommand->getName());

        foreach ($chainMembers as $chainMember) {

            $event = new ChainCommandEvent($mainCommand, $chainMember['command'], $input, $output);
            $this->eventDispatcher->dispatch(ChainEvents::CHAIN_COMMAND_REGISTERED, $event);
        }

        return $chainMembers;
    }

    /**
     * Perform main command on chain
     *
     * @param Command $command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function runMainCommand(Command $command, $input, $output)
    {
        $event = new ChainCommandEvent($command, $command, $input, $output);
        $this->eventDispatcher->dispatch(ChainEvents::BEFORE_MAIN_COMMAND, $event);

        $bufferOutput = new BufferedOutput();
        $command->run($input, $bufferOutput);

        $message = $bufferOutput->fetch();
        $output->write($message);

        $buffer = new BufferedOutput();
        $buffer->write($message);

        $event = new ChainCommandEvent($command, $command, $input, $buffer);
        $this->eventDispatcher->dispatch(ChainEvents::AFTER_MAIN_COMMAND, $event);
    }

    /**
     * Run chain members
     *
     * @param Command $mainCommand
     * @param array $chainCommands
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @TODO: Input params for chained command as service tags
     */
    private function runChainCommands(Command $mainCommand, array $chainCommands, $input, $output)
    {
        foreach ($chainCommands as $chainItem) {
            /** @var Command $chainCommand */
            $chainCommand = $chainItem['command'];

            $event = new ChainCommandEvent($mainCommand, $chainCommand, $input, $output);
            $this->eventDispatcher->dispatch(ChainEvents::BEFORE_SUB_COMMAND, $event);

            $bufferOutput = new BufferedOutput();
            $chainCommand->run(new ArrayInput([]), $bufferOutput);

            $message = $bufferOutput->fetch();
            $output->write($message);


            $buffer = new BufferedOutput();
            $buffer->write($message);
            $event = new ChainCommandEvent($mainCommand, $chainCommand, $input, $buffer);
            $this->eventDispatcher->dispatch(ChainEvents::AFTER_SUB_COMMAND, $event);
        }
    }
}