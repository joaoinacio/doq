<?php

namespace doq\Compose\Configuration;

use doq\Compose\Configuration;
use Symfony\Component\Yaml\Yaml;
use Exception;

class Services extends Configuration
{
    protected $servicesDefinition;

    /**
     * Constructor
     *
     * @param string $configName configuration to use.
     */
    public function __construct($configName)
    {
        parent::__construct($configName);
        $this->assertFileExists();
        $this->parseConfig();
    }

    /**
     *  Parse docker-compose configuration file
     */
    protected function parseConfig()
    {
        $data = Yaml::parse($this->getContents());

        if (!isset($data['services'])) {
            throw new Exception('the configuration file does not define a "services" section');
        }

        foreach ($data['services'] as $name => $definition) {
            $def = [
                'image' => isset($definition['image']) ? $definition['image'] : '',
                'ports' =>'',
                'links' => '',
                'mounts' => isset($definition['mounts']) ? $definition['mounts'] : '',
            ];

            if (isset($definition['ports'])) {
                if (is_array($definition['ports'])) {
                    $definition['ports'] = implode(',', $definition['ports']);
                }
                $def['ports'] = $definition['ports'];
            }
            if (isset($definition['links'])) {
                if (is_array($definition['links'])) {
                    $definition['links'] = implode(',', $definition['links']);
                }
                $def['links'] = $definition['links'];
            }

            $this->servicesDefinition[$name] = $def;
        }
    }

    /**
     * Return service definition for parsed configuration file
     *
     * @return array
     */
    public function getServicesDefinition()
    {
        return $this->servicesDefinition;
    }
}
