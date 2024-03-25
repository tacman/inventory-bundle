<?php

namespace PlinioCardoso\InventoryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class InventoryExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('inventory.out_of_stock_notification.from', $config['out_of_stock_notification']['from']);
        $container->setParameter('inventory.out_of_stock_notification.to', $config['out_of_stock_notification']['to']);
        $container->setParameter('inventory.out_of_stock_notification.subject', $config['out_of_stock_notification']['subject']);
    }
}
