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

namespace Mmoreram\PHPFormatter\Fixer;

use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;

/**
 * Class StrictFixer.
 */
class StrictFixer implements FixerInterface
{
    /**
     * @var string
     *
     * Strict
     */
    protected $strict;

    /**
     * Construct method.
     *
     * @param bool $strict
     */
    public function __construct($strict)
    {
        if (is_bool($strict)) {
            $this->strict = $strict
                ? 'declare(strict_types=1);'
                : 'declare(strict_types=0);';
        }
    }

    /**
     * Fix any piece of code given as parameter.
     *
     * @param string $data Data
     *
     * @return string Data fixed
     */
    public function fix($data)
    {
        $regex = '~(?P<header>^\s*<\?php(?:(?:(?:/\*.*?\*/)|(?:(?://|#).*?\n{1})|(?:\s*))*))(?P<declare>\s*declare\(\s*strict_types\s*=\s*[01]{1}\s*\)\s*;\s*\n*)?(?P<other>.*)~s';
        preg_match($regex, $data, $results);

        if (!isset($results['header'])) {
            return false;
        }

        $header = $results['header'];
        $other = isset($results['other']) ? $results['other'] : '';

        return trim($header) . rtrim("\n\n" . $this->strict . "\n\n") . "\n\n" . ltrim($other);
    }
}
