<?php

namespace Borovets\ChainCommandBundle\Service;

use Symfony\Component\Console\Command\Command;

class ChainCollection
{
    private $chains = [];

    /**
     * Register chained command
     *
     * @param string $masterCommandName
     * @param int $priority
     * @param Command $command
     */
    public function addCommand($masterCommandName, $priority = 0, Command $command)
    {
        $this->chains[$masterCommandName][] = [
            'commandName' => $command->getName(),
            'priority' => $priority,
            'command' => $command,
        ];
    }

    /**
     * Checks if a chain is present
     *
     * @param string $commandName
     * @return bool
     */
    public function hasChain($commandName)
    {
        if (array_key_exists($commandName, $this->chains)) {
            return true;
        }

        return false;
    }

    /**
     * Check if command used in chains
     *
     * @param string $commandName
     * @return bool
     */
    public function isChainedCommand($commandName)
    {
        foreach ($this->chains as $chain) {
            foreach ($chain as $chainItem) {
                if ($chainItem['commandName'] === $commandName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return master command name by chained Command name
     *
     * @param string $commandName
     * @return string
     */
    public function getMainChainName($commandName)
    {
        foreach ($this->chains as $key => $chain) {
            foreach ($chain as $chainItem) {
                if ($commandName === $chainItem['commandName']) {
                    return $key;
                }
            }
        }

        return false;
    }

    /**
     * Return chain by master command name
     *
     * @param string $chainName
     * @return array
     */
    public function getChainSubCommands($chainName)
    {
        $chain = $this->chains[$chainName];
        uasort($chain, [$this, 'sortChainByPriority']);

        return $chain;
    }

    /**
     * Sort command on their priority
     */
    private function sortChainByPriority($a, $b)
    {
        if ($a['priority'] == $b['priority']) {
            return 0;
        }

        return ($a['priority'] > $b['priority']) ? -1 : 1;
    }
}