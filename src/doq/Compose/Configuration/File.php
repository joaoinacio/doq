<?php

namespace doq\Compose\Configuration;

use Exception;

class File
{
    protected $filePath;

    /**
     * Constructor
     *
     * @param string $filePath the path to the file.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Return the name of the current configuration.
     *
     * @param bool $keepExtension wether to keep extension (default false).
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
     * @param bool $getRealPath wether to return realpath (default false).
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

    /**
     * Check if file exists.
     *
     * @return bool - true if the file exists, false if not.
     */
    public function exists()
    {
        return ($this->filePath && file_exists($this->filePath));
    }

    /**
     * Get the contents of the file.
     *
     * @return string file contents.
     */
    public function getContents()
    {
        return file_get_contents($this->filePath);
    }

    /**
     * Copy the file to destination path.
     *
     * @param string $destFilePath destination
     */
    public function copyTo($destFilePath)
    {
        if (!copy($this->filePath, $destFilePath)) {
            throw new Exception(sprintf('Could not copy configuration file to "%s".', $destFilePath));
        }
    }
}
