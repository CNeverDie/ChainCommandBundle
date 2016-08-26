<?php

namespace Borovets\ChainCommandBundle\EventListener;


use Borovets\ChainCommandBundle\Component\Console\ReadableBufferedOutput;
use Borovets\ChainCommandBundle\Event\ChainCommandEvent;
use Borovets\ChainCommandBundle\Event\ChainEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandChainSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ChainEvents::CHAIN_INIT => 'onInit',
            ChainEvents::CHAIN_COMMAND_REGISTERED => 'onRegisterSubCommand',
            ChainEvents::BEFORE_MAIN_COMMAND => 'onBeforeMainCommand',
            ChainEvents::AFTER_MAIN_COMMAND => 'onAfterMainCommand',
            ChainEvents::BEFORE_CHAIN_STARTED => 'onBeforeChainStart',
            ChainEvents::BEFORE_SUB_COMMAND => 'onBeforeSubCommand',
            ChainEvents::AFTER_SUB_COMMAND => 'onAfterSubCommand',
            ChainEvents::CHAIN_FINISHED => 'onChainFinished'
        ];
    }

    /**
     * CommandChainSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Listen event chain_command.init
     *
     * @param ChainCommandEvent $event
     */
    public function onInit(ChainCommandEvent $event)
    {
        $this->logger->info(
            sprintf('%s is a master command of a command chain that has registered member commands',
                $event->getCommand()->getName()
            )
        );
    }

    /**
     * Listen event chain_command.chain_registered
     *
     * @param ChainCommandEvent $event
     */
    public function onRegisterSubCommand(ChainCommandEvent $event)
    {
        $this->logger->info(
            sprintf('%s registered as a member of %s command chain',
                $event->getCurrentCommand()->getName(),
                $event->getCommand()->getName()
            )
        );
    }

    /**
     * Listen event chain_command.before_main_command
     *
     * @param ChainCommandEvent $event
     */
    public function onBeforeMainCommand(ChainCommandEvent $event)
    {
        $this->logger->info(
            sprintf('Executing %s command itself first:',
                $event->getCommand()->getName()
            )
        );
    }

    /**
     * Listen event chain_command.after_main_command
     *
     * @param ChainCommandEvent $event
     */
    public function onAfterMainCommand(ChainCommandEvent $event)
    {
        /** @var ReadableBufferedOutput $bufferOutput */
        $bufferOutput = $event->getOutput();
        $this->logger->info($bufferOutput->get());
    }

    /**
     * Listen event chain_command.before_chain_started
     *
     * @param ChainCommandEvent $event
     */
    public function onBeforeChainStart(ChainCommandEvent $event)
    {
        $this->logger->info(
            sprintf('Executing %s command chain members:',
                $event->getCommand()->getName()
            )
        );
    }

    /**
     * Listen event chain_command.before_sub_command
     *
     * @param ChainCommandEvent $event
     */
    public function onBeforeSubCommand(ChainCommandEvent $event)
    {
        $this->logger->info(
            sprintf('Executing %s chained command:',
                $event->getCurrentCommand()->getName()
            )
        );
    }

    /**
     * Listen event chain_command.after_sub_command
     *
     * @param ChainCommandEvent $event
     */
    public function onAfterSubCommand(ChainCommandEvent $event)
    {
        /** @var ReadableBufferedOutput $bufferOutput */
        $bufferOutput = $event->getOutput();

        $this->logger->info($bufferOutput->get());
    }

    /**
     * Listen event chain_command.finish
     *
     * @param ChainCommandEvent $event
     */
    public function onChainFinished(ChainCommandEvent $event)
    {
        $this->logger->info(
            sprintf('Execution of %s chain completed.',
                $event->getCommand()->getName()
            )
        );
    }
}