<?php

/*
 * This file is part of the php-formatter package
 *
 * Copyright (c) >=2014 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\PHPFormatter\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileFinder.
 */
class FileFinder
{
    /** @var Finder */
    private $finder;

    /**
     * FileFinder constructor.
     */
    public function __construct()
    {
        $this->finder = new Finder();
    }

    /**
     * Find all php files by path.
     *
     * @param string   $path
     * @param string[] $excludes
     *
     * @return Finder|SplFileInfo[]
     */
    public function findPHPFilesByPath(
        string $path,
        array $excludes
    ) {
        if (file_exists($path) && !is_dir($path)) {
            $splFile = new SplFileInfo(
                realpath($path),
                pathinfo($path)['dirname'],
                $path);
            $this->finder->append([0 => $splFile]);
        } else {
            $this->finder
                ->files()
                ->in($path)
                ->exclude($excludes)
                ->name('*.php');
        }

        return $this->finder;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }
}
