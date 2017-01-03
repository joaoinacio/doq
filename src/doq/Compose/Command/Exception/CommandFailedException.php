<?php

namespace doq\Compose\Command\Exception;

use Exception;

class CommandFailedException extends Exception
{
    public function __construct()
    {
        parent::__construct('docker-compose command did not finish successfully.');
    }
}
