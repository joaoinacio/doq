<?php

namespace doq\Compose;

use doq\Compose\Configuration;
use doq\Compose\Command\Exception\CommandFailedException;
use Exception;

class Command
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * Result status of last compose shell execution
     * @var int
     */
    protected $lastResult;

    /**
     * Result output of last compose shell execution
     * @var string
     */
    protected $lastOutput;

    /**
     * Set the configuration object for the command to be executed.
     *
     * @param Configuration $config - the configuration instance
     */
    public function setConfiguration(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Execute a command in docker-compose, using a configuration file and name.
     *
     * @param string $command the command to execute with docker-compose
     * @param array  $options optional options array
     * @param array  $args    optional arguments array
     *
     * @throws doq\Exception\ConfigNotFoundException
     *
     */
    public function execute($command, $options = [], $args = [])
    {
        if (!$this->config) {
            throw new Exception('docker-compose configuration was not provided');
        }

        $tempConfigFile = $this->config->createTempFile();

        // merge default options for file and project name with provided $options
        $options = array_merge(
            [
                '--project-name ' . $this->getProjectName($this->config->getName()),
                '--file ' . $tempConfigFile,
            ],
            $options
        );

        $this->lastResult = 0;
        $this->exec($command, $options, $args);

        unlink($tempConfigFile);

        if ($this->getResult() !== 0) {
            throw new CommandFailedException();
        }
    }

    /**
     * Returns the output string of the last command executed.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->lastOutput;
    }

    /**
     * Returns the result status code of the last command executed.
     *
     * @return int
     */
    public function getResult()
    {
        return $this->lastResult;
    }

    /**
     * Get the prwoject name based on current path and config name.
     *
     * @param string $configName the name of the configuration to use.
     *
     * @return string
     */
    protected function getProjectName($configName)
    {
        $currentDirName = dirname(getcwd());
        return sprintf('%s.%s', $currentDirName, $configName);
    }

    /**
     * Execute shell command and store result/output.
     *
     * @param string $command the command to execute in docker-compose
     * @param array  $options options array
     * @param array  $args    arguments array
     */
    protected function exec($command, $options, $args)
    {
        $options = implode(' ', $options);
        $args = implode(' ', $args);

        // escape and execute shell command
        $command = escapeshellcmd("docker-compose $options $command $args");

        exec($command . ' 2>&1', $out, $result);

        $this->lastOutput = implode(PHP_EOL, $out);
        $this->lastResult = (int)$result;
    }
}
