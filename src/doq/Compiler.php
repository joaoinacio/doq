<?php

namespace doq;

use Phar;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Phar Compiler
 */
class Compiler
{
    const PHAR_NAME = 'doq.phar';

    /**
     * @var string - root directory
     */
    protected $rootDir;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rootDir = realpath( __DIR__ . '/../../' );
    }

    /**
     * Compiles app into a single phar file
     *
     * @param string $pharFile - The full path to the file to create
     * @throws \RuntimeException
     */
    public function compile($pharFile = self::PHAR_NAME)
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new Phar($pharFile, 0, self::PHAR_NAME);
        $phar->setSignatureAlgorithm(Phar::SHA1);
        $phar->startBuffering();

        // Add sources
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*')
            ->in($this->rootDir . '/src');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addDependencies(
            $phar,
            [
                'vendor/symfony',
                'vendor/psr',
            ]
        );

        // add composer autoload
        $this->addFile($phar, new SplFileInfo($this->rootDir . '/vendor/autoload.php'));
        $this->addDependencies($phar, ['vendor/composer'] );

        // add binary
        $this->addBin($phar);

        // Stub
        $phar->setStub($this->getStub());

        // Save
        $phar->stopBuffering();
    }

    /**
     * Add dependencies to phar archive
     *
     * @param Phar $phar   archive
     * @param array $paths paths to add
     */
    protected function addDependencies($phar, $paths = [])
    {
        foreach ($paths as &$path) {
            $path = $this->rootDir . '/' . $path;
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('LICENSE')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in($paths);

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }
    }

    /**
     * Add file contents to phar archive
     *
     * @param Phar        $phar archive
     * @param SplFileInfo $file file
     * @param bool        $strip wether to strip whitespace, default true.
     */
    private function addFile($phar, SplFileInfo $file, $strip = true)
    {
        $path = strtr(str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath()), '\\', '/');
        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }
        $phar->addFromString($path, $content);
    }

    /**
     * Add binary entry-point to phar archive
     *
     * @param Phar $phar archive
     */
    private function addBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/doq');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/doq', $content);
    }

    /**
     * Return phar stub contents as string.
     *
     * @return string
     */
    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
Phar::mapPhar('doq.phar');
require 'phar://doq.phar/bin/doq';
__HALT_COMPILER();
EOF;
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        return $output;
    }

}
