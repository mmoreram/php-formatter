<?php

/*
 * This file is part of the php-formatter package
 *
 * Copyright (c) 2014-2016 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\PHPFormatter\Compiler;

use DateTime;
use Phar;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * Class Compiler.
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
     * Compiles composer into a single phar file.
     *
     * @throws RuntimeException
     */
    public function compile()
    {
        $pharFilePath = dirname(__FILE__) . '/../../../build/php-formatter.phar';

        if (file_exists($pharFilePath)) {
            unlink($pharFilePath);
        }

        $this->loadVersion();

        /**
         * Creating phar object.
         */
        $phar = new Phar($pharFilePath, 0, 'php-formatter.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $this
            ->addPHPFiles($phar)
            ->addVendorFiles($phar)
            ->addComposerVendorFiles($phar)
            ->addBin($phar)
            ->addStub($phar)
            ->addLicense($phar);

        $phar->stopBuffering();

        unset($phar);
    }

    /**
     * Add a file into the phar package.
     *
     * @param Phar        $phar  Phar object
     * @param SplFileInfo $file  File to add
     * @param bool        $strip strip
     *
     * @return Compiler self Object
     */
    protected function addFile(
        Phar $phar,
        SplFileInfo $file,
        $strip = true
    ) {
        $path = strtr(str_replace(dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR, '', $file->getRealPath()), '\\', '/');
        $content = $file->getContents();
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === $file->getBasename()) {
            $content = "\n" . $content . "\n";
        }

        if ($path === 'src/Composer/Composer.php') {
            $content = str_replace('@package_version@', $this->version, $content);
            $content = str_replace('@release_date@', $this->versionDate, $content);
        }

        $phar->addFromString($path, $content);

        return $this;
    }

    /**
     * Add bin into Phar.
     *
     * @param Phar $phar Phar
     *
     * @return Compiler self Object
     */
    protected function addBin(Phar $phar)
    {
        $content = file_get_contents(__DIR__ . '/../../../bin/php-formatter');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/php-formatter', $content);

        return $this;
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
            } elseif (in_array($token[0], [T_COMMENT, T_DOC_COMMENT])) {
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

    protected function addStub(Phar $phar)
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php

/*
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
        $phar->setStub($stub);

        return $this;
    }

    /**
     * Add php files.
     *
     * @param Phar $phar Phar instance
     *
     * @return Compiler self Object
     */
    private function addPHPFiles(Phar $phar)
    {
        /**
         * All *.php files.
         */
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->notName('ClassLoader.php')
            ->in(realpath(__DIR__ . '/../../../src'));

        foreach ($finder->files() as $file) {
            $this->addFile($phar, $file);
        }

        return $this;
    }

    /**
     * Add vendor files.
     *
     * @param Phar $phar Phar instance
     *
     * @return Compiler self Object
     */
    private function addVendorFiles(Phar $phar)
    {
        $vendorPath = __DIR__ . '/../../../vendor/';

        /**
         * All *.php files.
         */
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->in(realpath($vendorPath . 'symfony/'));

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        return $this;
    }

    /**
     * Add composer vendor files.
     *
     * @param Phar $phar Phar
     *
     * @return Compiler self Object
     */
    private function addComposerVendorFiles(Phar $phar)
    {
        $vendorPath = __DIR__ . '/../../../vendor/';

        /*
         * Adding composer vendor files
         */
        $this
            ->addFile($phar, new SplFileInfo($vendorPath . 'autoload.php', $vendorPath, $vendorPath . 'autoload.php'))
            ->addFile($phar, new SplFileInfo($vendorPath . 'composer/autoload_namespaces.php', $vendorPath . 'composer', $vendorPath . 'composer/autoload_namespaces.php'))
            ->addFile($phar, new SplFileInfo($vendorPath . 'composer/autoload_psr4.php', $vendorPath . 'composer', $vendorPath . 'composer/autoload_psr4.php'))
            ->addFile($phar, new SplFileInfo($vendorPath . 'composer/autoload_classmap.php', $vendorPath . 'composer', $vendorPath . 'composer/autoload_classmap.php'))
            ->addFile($phar, new SplFileInfo($vendorPath . 'composer/autoload_real.php', $vendorPath . 'composer', $vendorPath . 'composer/autoload_real.php'))
            ->addFile($phar, new SplFileInfo($vendorPath . 'composer/ClassLoader.php', $vendorPath . 'composer', $vendorPath . 'composer/ClassLoader.php'));

        if (file_exists($vendorPath . 'composer/include_paths.php')) {
            $this->addFile($phar, new SplFileInfo($vendorPath . 'composer/include_paths.php', $vendorPath . 'composer', $vendorPath . 'composer/include_paths.php'));
        }

        return $this;
    }

    /**
     * Add license.
     *
     * @param Phar $phar Phar
     *
     * @return Compiler self Object
     */
    private function addLicense(Phar $phar)
    {
        $this->addFile($phar, new SplFileInfo(__DIR__ . '/../../../LICENSE', __DIR__ . '/../../..', __DIR__ . '/../../../LICENSE'), false);

        return $this;
    }

    /**
     * Load versions.
     */
    private function loadVersion()
    {
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

        return $this;
    }
}
