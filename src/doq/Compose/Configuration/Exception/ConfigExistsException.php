<?php

namespace doq\Compose\Configuration\Exception;

use Exception;

class ConfigExistsException extends Exception
{
    /**
     * Constructor
     *
     * @param string $configFileName - configuration file.
     */
    public function __construct($configFileName)
    {
        parent::__construct(sprintf("Configuration file '%s' already exists.", $configFileName));
    }
}
