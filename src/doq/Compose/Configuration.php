<?php

namespace doq\Compose;

use doq\Compose\Template;
use Exception;
use doq\Exception\ConfigNotFoundException;
use doq\Exception\ConfigExistsException;

class Configuration
{
    const COMPOSE_FOLDER = '.docker-compose';

    protected $configName;
    protected $configFile;

    /**
     * Constructor
     *
     * @param string $configName the configuration name to use
     */
    public function __construct($configName)
    {
        $this->configName = $configName;
        $this->configFile = $this->getConfigFilePath();
    }

    public function createFromTemplate(Template $template)
    {
        // in order to create a new config, it must not already exist
        $this->assertFileDoesNotExist();

        $fileContents = $template->fetchFromSource();
        $configFilePath = $this->getConfigFilePath();
        if (!file_put_contents($configFilePath, $fileContents)) {
            throw new Exception(sprintf("Could not write template contents to '%s'", $configFilePath));
        }
    }

    /**
     * Takes a configuration name and returns the local path to the file.
     *
     * @return string
     */
    protected function getConfigFilePath()
    {
        return sprintf('%s/%s.yml', self::COMPOSE_FOLDER, $this->configName);
    }

    /**
     * Return the name of the current configuration
     *
     * @return string
     */
    public function getName()
    {
        return $this->configName;
    }

    /**
     * Return the path of the current configuration file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->configFile;
    }

    /**
     * Copy source compose config file to temporary file in current directory.
     *
     * @return string the temporary file name
     */
    public function copyTempFile()
    {
        $this->assertFileExists();

        $tmpConfigFile = tempnam(getcwd(), 'compose-');
        if (!copy($this->getFile(), $tmpConfigFile)) {
            throw new Exception('Could not create temporary compose file in current directory.');
        }
        return $tmpConfigFile;
    }

    /**
     * Test if the file for the current configuration exists, throw exception if it does not.
     *
     * @throws doq\Exception\ConfigNotFoundException
     */
    public function assertFileExists()
    {
        if (!file_exists($this->configFile)) {
            throw new ConfigNotFoundException($this->configFile);
        }
    }

    /**
     * Test if the file for the current configuration exists, throw exception if it does.
     *
     * @param $configName
     *
     * @throws doq\Exception\ConfigExistsException
     */
    public function assertFileDoesNotExist()
    {
        if (file_exists($this->configFile)) {
            throw new ConfigExistsException($this->configFile);
        }
    }
}
