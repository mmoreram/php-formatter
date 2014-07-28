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
 
namespace PHPFormatter\Finder;

use Iterator;
use Symfony\Component\Finder\Finder;

/**
 * Class FileFinder
 */
class FileFinder
{
    /**
     * Find all php files by path
     *
     * @param string $path Path
     *
     * @return \Iterator
     */
    public function findPHPFilesByPath($path)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->name('*.php');

        return $finder->getIterator();
    }
}
 