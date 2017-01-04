<?php

namespace doq\Compose\Configuration;

use Exception;

class File
{
    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Return the name of the current configuration
     *
     * @return string
     */
    public function getName($keepExtension = false)
    {
        return basename($this->filePath, $keepExtension ? null : '.yml');
    }

    /**
     * Return the full path of the configuration file
     *
     * @return string
     */
    public function getFilePath($getRealPath = false)
    {
        if ($getRealPath) {
            return realpath($this->filePath);
        } // else
        return $this->filePath;
    }

    public function exists()
    {
        return ($this->filePath && file_exists($this->filePath));
    }

    public function getContents()
    {
        return file_get_contents($this->filePath);
    }

    public function copyTo($destFilePath)
    {
        if (!copy($this->filePath, $destFilePath)) {
            throw new Exception(sprintf('Could not copy configuration file to "%s".', $destFilePath));
        }
    }
}
