<?php

declare(strict_types=1);

namespace Gsu\Symfony\DependencyInjection;

use Gsu\D2l\Oauth\Command\AbstractCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class GsuSymfonyExtension extends Extension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(\dirname(__DIR__) . '/Resources/config')
        );

        $loader->load('services.php');

        // $configuration = $this->getConfiguration([], $container) ?? throw new \RuntimeException();
        // $config = $this->processConfiguration($configuration, $configs);

        // $container->registerForAutoconfiguration(AbstractCommand::class)
        //     ->addTag('console.command');
    }
}
