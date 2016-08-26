<?php

namespace Borovets\ChainCommandBundle\EventListener;

use Borovets\ChainCommandBundle\Service\ChainManager;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandSubscriber implements EventSubscriberInterface
{

    private $manager;

    public function __construct(ChainManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand',
        ];
    }

    /**
     * Subscribe on all console command.
     *
     * If the command is run, which is the main in the chain, control is passed ChainManager
     * If the command is run member the chain, command execution will be stopped
     *
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        if ($event->isPropagationStopped()) {
            return;
        }

        $command = $event->getCommand();

        if ($this->manager->getChain()->isChainedCommand($command->getName())) {
            $event->disableCommand();
            $event->stopPropagation();

            $event->getOutput()->writeln(
                sprintf('Error: %s command is a member of %s command chain and cannot be executed on its own.',
                    $command->getName(),
                    $this->manager->getChain()->getMainChainName($command->getName())
                )
            );
        }

        if ($this->manager->getChain()->hasChain($command->getName())) {
            $event->disableCommand();

            $this->manager->runChain($command, $event->getInput(), $event->getOutput());
        }
    }
}