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

namespace Mmoreram\PHPFormatter\Fixer\Interfaces;

/**
 * Interface FixerInterface.
 */
interface FixerInterface
{
    /**
     * Do the fix. Return the fixed code or false if the code has not changed.
     *
     * @param string $data
     *
     * @return string|false
     */
    public function fix($data);
}
