<?php

/**
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 */

namespace PHPFormatter\Compiler;

use Phar;
use DateTime;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Class Compiler
 */
class Compiler
{
    /**
     * @var string
     *
     * version
     */
    protected $version;

    /**
     * @var DateTime
     *
     * versionDate
     */
    protected $versionDate;

    /**
     * Compiles composer into a single phar file
     *
     * @throws RuntimeException
     *
     * @param string $pharFile The full path to the file to create
     */
    public function compile($pharFile = 'php-formatter.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        /**
         * Loading versions
         */
        $process = new Process('git log --pretty="%H" -n1 HEAD', __DIR__);
        if ($process->run() != 0) {
            throw new \RuntimeException('Can\'t run git log. You must ensure to run compile from php-formatter git repository clone and that git binary is available.');
        }
        $this->version = trim($process->getOutput());

        $process = new Process('git log -n1 --pretty=%ci HEAD', __DIR__);
        if ($process->run() != 0) {
            throw new \RuntimeException('Can\'t run git log. You must ensure to run compile from php-formatter git repository clone and that git binary is available.');
        }
        $date = new \DateTime(trim($process->getOutput()));
        $date->setTimezone(new \DateTimeZone('UTC'));
        $this->versionDate = $date->format('Y-m-d H:i:s');

        $process = new Process('git describe --tags HEAD');
        if ($process->run() == 0) {
            $this->version = trim($process->getOutput());
        }

        /**
         * Creating phar object
         */
        $phar = new Phar($pharFile, 0, 'php-formatter.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        /**
         * All *.php files
         */
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->notName('ClassLoader.php')
            ->in(realpath(__DIR__ . '/../../../src'));

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        /**
         * All vendors (ignoring tests)
         */
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->in(realpath(__DIR__ . '/../../../vendor/symfony/'));

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        /**
         * Adding composer vendor files
         */
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/composer/autoload_psr4.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/composer/autoload_classmap.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/composer/autoload_real.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/composer/ClassLoader.php'));

        if (file_exists(__DIR__ . '/../../../vendor/composer/include_paths.php')) {
            $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../vendor/composer/include_paths.php'));
        }

        /**
         * Adding bin
         */
        $this->addBin($phar);

        /**
         * Adding stubs
         */
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        /**
         * Adding LICENSE
         */
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../../../LICENSE'), false);

        unset($phar);
    }

    /**
     * Add a file into the phar package
     *
     * @param Phar   $phar  Phar object
     * @param string $file  File to add
     * @param bool   $strip strip
     */
    protected function addFile(Phar $phar, $file, $strip = true)
    {
        $path = strtr(str_replace(dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR, '', $file->getRealPath()), '\\', '/');
        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n" . $content . "\n";
        }

        if ($path === 'src/Composer/Composer.php') {
            $content = str_replace('@package_version@', $this->version, $content);
            $content = str_replace('@release_date@', $this->versionDate, $content);
        }

        $phar->addFromString($path, $content);
    }

    /**
     * Add bin into Phar
     *
     * @param Phar $phar Phar
     */
    protected function addBin(Phar $phar)
    {
        $content = file_get_contents(__DIR__ . '/../../../bin/php-formatter');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/php-formatter', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the whitespace removed
     */
    protected function stripWhitespace($source)
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

    protected function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php

/**
 * This file is part of the php-formatter package
 *
 * Copyright (c) 2014 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

Phar::mapPhar('php-formatter.phar');

require 'phar://php-formatter.phar/bin/php-formatter';

__HALT_COMPILER();
EOF;
    }
}
