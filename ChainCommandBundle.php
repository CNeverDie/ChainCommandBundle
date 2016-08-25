<?php

namespace Borovets\ChainCommandBundle;

use Borovets\ChainCommandBundle\DependencyInjection\Compiler\ChainCommandsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChainCommandBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ChainCommandsCompilerPass());
    }
}