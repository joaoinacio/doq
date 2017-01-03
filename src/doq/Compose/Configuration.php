<?php

namespace doq\Compose;

use doq\Compose\Configuration\File;
use doq\Compose\Configuration\Template;
use doq\Compose\Configuration\Exception\ConfigNotFoundException;
use doq\Compose\Configuration\Exception\ConfigExistsException;
use Exception;

class Configuration extends File
{
    const COMPOSE_FOLDER = '.docker-compose';

    /**
     * Constructor
     *
     * @param string $configName the configuration name to use
     */
    public function __construct($configName)
    {
        parent::__construct($this->getConfigFilePath($configName));
    }

    public function createFromTemplate(Template $template)
    {
        // in order to create a new config, it must not already exist
        $this->assertFileDoesNotExist();

        $fileContents = $template->fetchFromSource();
        $configFilePath = $this->getFilePath();
        if (!file_put_contents($configFilePath, $fileContents)) {
            throw new Exception(sprintf("Could not write template contents to '%s'", $configFilePath));
        }
    }

    /**
     * Takes a configuration name and returns the local path to the file.
     *
     * @return string
     */
    protected function getConfigFilePath($configName)
    {
        return sprintf('%s/%s.yml', self::COMPOSE_FOLDER, $configName);
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
        if (!copy($this->getFilePath(), $tmpConfigFile)) {
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
        if (!$this->exists()) {
            throw new ConfigNotFoundException($this->getFilePath(false));
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
        if ($this->exists()) {
            throw new ConfigExistsException($this->getFilePath(false));
        }
    }
}
