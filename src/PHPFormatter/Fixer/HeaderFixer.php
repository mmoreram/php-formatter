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

namespace Mmoreram\PHPFormatter\Fixer;

use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;

/**
 * Class HeaderFixer.
 */
class HeaderFixer implements FixerInterface
{
    /**
     * @var string
     *
     * Header
     */
    private $header;

    /**
     * Construct method.
     *
     * @param string $header Header
     */
    public function __construct($header)
    {
        $this->header = '<?php' . rtrim("\n\n" . trim($header) . "\n\n") . "\n\n";
    }

    /**
     * Do the fix. Return the fixed code or false if the code has not changed.
     *
     * @param string $data
     *
     * @return string|false
     */
    public function fix($data)
    {
        $regex = '~(?P<group>^\s*<\?php(?:(?:(?:/\*.*?\*/)|(?:(?://|#).*?\n{1})|(?:\s*))*))(?P<other>.*)~s';
        preg_match($regex, $data, $results);

        if (!isset($results['group'])) {
            return false;
        }

        $other = $results['other'];
        $fixedData = $this->header . ltrim($other);

        return $fixedData !== $data
            ? $fixedData
            : false;
    }
}
