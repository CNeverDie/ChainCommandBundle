<?php

namespace Borovets\ChainCommandBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ChainCommandsCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $commandChain = $container->findDefinition('chain_command_bundle.chain');
        $taggedServices = $container->findTaggedServiceIds('chain.command');

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $attr) {
                if (!isset($attr['master_command'])) {
                    throw new \InvalidArgumentException('You must specify a master command');
                }

                if (!isset($attr['priority'])) {
                    throw new \InvalidArgumentException('You must specify a priority for chain command');
                }

                $commandChain->addMethodCall('addCommand', [
                    $attr['master_command'], $attr['priority'], new Reference($serviceId)
                ]);
            }
        }
    }
}
