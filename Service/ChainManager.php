<?php

namespace Borovets\ChainCommandBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ChainManager
{
    /** @var ChainCollection */
    private $chainCollection;
    private $logger;

    /**
     * ChainManager constructor.
     * @param ChainCollection $chainCollection
     * @param LoggerInterface $logger
     */
    public function __construct(ChainCollection $chainCollection, LoggerInterface $logger)
    {
        $this->chainCollection = $chainCollection;
        $this->logger = $logger;
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
        $this->logger->info(
            sprintf('%s is a master command of a command chain that has registered member commands',
                $command->getName()
            )
        );

        $chainMembers = $this->getChainedCommands($command->getName());

        $this->runMainCommand($command, $input, $output);

        $this->logger->info(
            sprintf('Executing %s command chain members:',
                $command->getName()
            )
        );

        $this->runChainCommands($chainMembers, $output);

        $this->logger->info(
            sprintf('Execution of %s chain completed.',
                $command->getName()
            )
        );
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
        $this->logger->info(
            sprintf('Executing %s command itself first:',
                $command->getName()
            )
        );

        $buffer = new BufferedOutput();
        $command->run($input, $buffer);

        $outputMessage = $buffer->fetch();

        $this->logger->info($outputMessage);
        $output->writeln($outputMessage);
    }


    /**
     * @param $chainCommands
     * @param OutputInterface $output
     *
     * @TODO: Input params for chained command as service tags
     */
    private function runChainCommands($chainCommands, $output)
    {
        foreach ($chainCommands as $chainItem) {
            /** @var Command $chainCommand */
            $chainCommand = $chainItem['command'];

            $buffer = new BufferedOutput();
            $chainCommand->run(new ArrayInput([]), $buffer);

            $outputMessage = $buffer->fetch();

            $this->logger->info($outputMessage);
            $output->writeln($outputMessage);
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
        $chainMembers = $this->chainCollection->getChainSubCommands($mainCommand);

        foreach ($chainMembers as $chainMember) {
            $this->logger->info(
                sprintf('%s registered as a member of %s command chain',
                    $chainMember['commandName'],
                    $mainCommand
                )
            );
        }

        return $chainMembers;
    }
}