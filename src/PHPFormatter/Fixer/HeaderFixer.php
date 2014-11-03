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

namespace Mmoreram\PHPFormatter\Fixer;

use Symfony\Component\Console\Command\Command;

use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;

/**
 * Class HeaderFixer
 */
class HeaderFixer extends Command implements FixerInterface
{
    /**
     * @var string
     *
     * Header
     */
    protected $header;

    /**
     * Construct method
     *
     * @param string $header Header
     */
    public function __construct($header)
    {
        $this->header = "<?php\n\n" . trim($header) . "\n\n";
    }

    /**
     * Fix any piece of code given as parameter
     *
     * @param string $data Data
     *
     * @return string Data fixed
     */
    public function fix($data)
    {
        $regex = '~(?P<group>^\s*<\?php(((/\*.*?\*/)|((//|#).*?\n{1})|(\s*))*))~s';
        preg_match($regex, $data, $results);

        if (!isset($results['group'])) {
            return false;
        }

        $result = $results['group'];

        return str_replace($result, $this->header, $data);
    }
}
