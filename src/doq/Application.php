<?php
namespace doq;

use Symfony\Component\Console\Application as BaseApp;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use doq\Command;

/**
 * Implement Symfony Console application
 */
class Application extends BaseApp
{
    const NAME    = 'doq - docker-compose service configuration manager';
    const VERSION = '1.0';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->container = new ContainerBuilder();

        $this->loadServices();
        $this->addTaggedCommands();
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Load services configured for dependency injection
     */
    protected function loadServices()
    {
        $serviceConfigDir = new FileLocator(__DIR__ . DIRECTORY_SEPARATOR . 'config');

        $loader = new YamlFileLoader($this->container, $serviceConfigDir);
        $loader->load('services.yml');
    }

    /**
     * Add services tagged as doq.command to the Application
     */
    protected function addTaggedCommands()
    {
        $taggedServices = $this->container->findTaggedServiceIds('doq.command');
        foreach (array_keys($taggedServices) as $id) {
            $this->add($this->container->get($id));
        }
    }

}
