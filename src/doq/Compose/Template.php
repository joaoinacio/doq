<?php

namespace doq\Compose;

use Exception;

class Template
{
    const CONF_SOURCE_NAME = 1;
    const CONF_SOURCE_FILE = 2;
    const CONF_SOURCE_URL = 3;

    const URL_REGEX_PATTERN = '@^(https?)://[^\s/$.?#].[^\s]*$@iS';

    public function detectConfigSource($source)
    {
        if ($this->isUrl($source)) {
            return self::CONF_SOURCE_URL;
        }
        if ($this->isFilePath($source)) {
            return self::CONF_SOURCE_FILE;
        }

        // name?
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
     * Detect if provided source is a file path.
     *
     * @param string $source
     *
     * @return bool  returns true if source is a file, false otherwise.
     */
    private function isFilePath($source)
    {
        $filePath = getcwd() . DIRECTORY_SEPARATOR . $source;
        return is_file($filePath);
    }
}
