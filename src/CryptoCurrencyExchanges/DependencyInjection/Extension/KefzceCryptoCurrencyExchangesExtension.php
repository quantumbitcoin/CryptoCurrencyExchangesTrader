<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\DependencyInjection\Extension;

use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class KefzceCryptoCurrencyExchangesExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources'));
        $loader->load('config/packages/kefzce_crypto_currency_exchanges.yaml');
        $loader->load('services.yaml');

        foreach ($configs as $key => $value) {
            $container->setParameter($key, $value);
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        $rc = new \ReflectionClass(Configuration::class);
        $container->addResource(new FileResource($rc->getFileName()));

        return new Configuration(
            $container->hasParameter('kernel.debug') ?
                $container->getParameter('kernel.debug') : false
        );
    }
}
