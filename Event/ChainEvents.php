<?php


namespace Borovets\ChainCommandBundle\Event;

final class ChainEvents
{
    /**
     * The start command is a main in chain
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const CHAIN_INIT = 'chain_command.init';

    /**
     * The command registered as chain member
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const CHAIN_COMMAND_REGISTERED = 'chain_command.chain_registered';

    /**
     * Before executing main command of chain command
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const BEFORE_MAIN_COMMAND = 'chain_command.before_main_command';

    /**
     * After executing main command of chain command
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const AFTER_MAIN_COMMAND = 'chain_command.after_main_command';

    /**
     * The main command before start executing members of chain
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const BEFORE_CHAIN_STARTED = 'chain_command.before_chain_started';

    /**
     * Before executing members of chain command
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const BEFORE_SUB_COMMAND = 'chain_command.before_sub_command';


    /**
     * After executing members of chain command
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const AFTER_SUB_COMMAND = 'chain_command.after_sub_command';

    /**
     * All member in chain has been executed
     *
     * @Event("Borovets\ChainCommandBundle\Event\ChainCommandEvent")
     * @var string
     */
    const CHAIN_FINISHED = 'chain_command.finish';
}