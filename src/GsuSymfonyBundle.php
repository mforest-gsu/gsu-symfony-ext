<?php

declare(strict_types=1);

namespace Gsu\Symfony;

use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class GsuSymfonyBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $this->addCompilerPassIfExists(
            $container,
            AddConsoleCommandPass::class,
            PassConfig::TYPE_BEFORE_REMOVING
        );
    }


    /**
     * @param ContainerBuilder $container
     * @param string $class
     * @param string $type
     * @param int $priority
     * @return void
     */
    private function addCompilerPassIfExists(
        ContainerBuilder $container,
        string $class,
        string $type = PassConfig::TYPE_BEFORE_OPTIMIZATION,
        int $priority = 0
    ): void {
        $container->addResource(new ClassExistenceResource($class));

        if (!class_exists($class)) {
            throw new \RuntimeException();
        }

        $ref = new \ReflectionClass($class);

        if (!$ref->implementsInterface(CompilerPassInterface::class)) {
            throw new \RuntimeException();
        }

        if (!$ref->isInstantiable()) {
            throw new \RuntimeException();
        }

        /** @var \ReflectionClass<CompilerPassInterface> $ref */
        $container->addCompilerPass($ref->newInstance(), $type, $priority);
    }
}
