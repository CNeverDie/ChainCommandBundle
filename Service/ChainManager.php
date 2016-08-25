<?php

namespace Borovets\ChainCommandBundle\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChainManager
{
    /** @var ChainCollection */
    private $chainCollection;

    /**
     * ChainManager constructor.
     * @param ChainCollection $chainCollection
     */
    public function __construct(ChainCollection $chainCollection)
    {
        $this->chainCollection = $chainCollection;
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
        $this->runMainCommand($command, $input, $output);

        $this->runChainCommand($command->getName(), $output);
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
        $command->run($input, $output);
    }


    /**
     * @param string $chainName
     * @param $output
     *
     * @TODO: Input params for chained command as service tags
     */
    private function runChainCommand($chainName, $output)
    {
        $chain = $this->getChainedCommands($chainName);

        foreach ($chain as $chainItem) {
            /** @var Command $chainCommand */
            $chainCommand = $chainItem['command'];
            $chainCommand->run(new ArrayInput([]), $output);
        }
    }

    /**
     * Get chained command by main command name
     *
     * @param string $mainCommand
     * @return array
     */
    private function getChainedCommands($mainCommand)
    {
        return $this->chainCollection->getChainSubCommands($mainCommand);
    }
}