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

namespace Mmoreram\PHPFormatter\Fixer\Interfaces;

/**
 * Interface FixerInterface.
 */
interface FixerInterface
{
    /**
     * Fix any piece of code given as parameter.
     *
     * @param string $data Data
     *
     * @return string Data fixed
     */
    public function fix($data);
}
