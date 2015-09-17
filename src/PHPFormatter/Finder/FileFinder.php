<?php

/*
 * This file is part of MITRE's ACE project
 *
 * Copyright (c) 2015 MITRE Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *
 * @author MITRE's ACE Team <ace-team@mitre.org>
 */

namespace Mmoreram\PHPFormatter\Finder;

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
     * @return Finder Finder iterable object with all PHP found files in path
     */
    public function findPHPFilesByPath($path)
    {
        $finder = new Finder();
        
        if (file_exists($path) && !is_dir($path)) {
            $finder->append([0 => $path]);
        } else {
            $finder
                ->files()
                ->in($path)
                ->name('*.php');
        }
        
        return $finder;
    }
}
