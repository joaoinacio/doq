<?php

namespace doq\Compose;

use Exception;

class Template
{
    const TEMPLATE_FOLDER = '~/.docker-compose/';

    const CONF_SOURCE_NONE = 0;
    const CONF_SOURCE_NAME = 1;
    const CONF_SOURCE_FILE = 2;
    const CONF_SOURCE_URL = 3;

    const NAME_REGEX_PATTERN = '/^[a-zA-Z0-9_\-]*/';
    const URL_REGEX_PATTERN = '/^(https?)://[^\s/$.?#].[^\s]*$/iS';

    protected $source;

    /**
     * Constructor
     */
    public function __construct($source)
    {
        $this->source = $source;
        $this->checkTemplatesDirectory();
    }

    public function fetchContents()
    {
        switch ($this->detectConfigSource()) {
            case self::CONF_SOURCE_NONE:
                return $this->newEmptyFile();
            case self::CONF_SOURCE_NAME:
                return file_get_contents($this->getConfigFilePath($this->source));
            case self::CONF_SOURCE_FILE:
                return file_get_contents($this->source);
            case self::CONF_SOURCE_URL:
                return file_get_contents($this->source);
        }
        // TODO: throw exception?
    }

    public function detectConfigSource()
    {
        if ($this->isName($this->source)) {
            return self::CONF_SOURCE_NAME;
        }
        if ($this->isUrl($this->source)) {
            return self::CONF_SOURCE_URL;
        }
        if ($this->isValidFilePath($this->source)) {
            return self::CONF_SOURCE_FILE;
        }

        // non existing file?
        // TODO: throw exception
    }

    protected function checkTemplatesDirectory()
    {
        list($base, $dir) = explode('/', self::TEMPLATE_FOLDER, 2);
        if ($base == '~') {
            $base = getenv('HOME');
        }
        $realPath = realpath($base) . DIRECTORY_SEPARATOR . $dir;

        if (realpath($realPath) !== false) {
            if (is_dir($realPath)) {
                return;
            }
        }

        if (mkdir($realPath)) {
            return;
        }

        throw new Exception(sprintf("Could not create directory '%s'", self::TEMPLATE_FOLDER));
    }

    /**
     * Takes a configuration name and returns the local path to the file.
     *
     * @return string
     */
    public function getConfigFilePath($name)
    {
        return sprintf('%s/%s.yml', self::TEMPLATE_FOLDER, $name);
    }

    public function newEmptyFile()
    {
        // TODO
        return <<<EOF
version: '2.1'

services:
EOF;
    }

    private function isName($source)
    {
        return preg_match(self::NAME_REGEX_PATTERN, $source);
    }

    /**
     * Detect if provided source is an url.
     *
     * @param string $source
     *
     * @return bool  returns true if source is an url, false otherwise.
     */
    private function isUrl($source)
    {
        return preg_match(self::URL_REGEX_PATTERN, $source);
    }


    /**
     * Detect if provided source is a valid/existing file path.
     *
     * @param string $source
     *
     * @return bool  returns true if source is a file, false otherwise.
     */
    private function isValidFilePath($source)
    {
        if (strlen($source) > 0 && $source != '.') {
            // realpath() returns FALSE if the file does not exist.
            if (($filePath = realpath($source)) === false) {
                return false;
            }
            if (is_file($filePath)) {
                return true;
            }
        }

        return false;
    }
}
