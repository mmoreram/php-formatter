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

namespace Mmoreram\PHPFormatter\Loader;

/**
 * Class ConfigLoader
 */
class ConfigLoader
{
    /**
     * This method parses the config file, if exists, and determines the real
     * options values.
     *
     * * If an option is defined as a command parameter, this will be used
     * * Otherwise, if an option is defined in the configuration file, this will
     * be used.
     * * Otherwise, default values will be used
     *
     * @param string $commandName   Command called
     * @param array  $configValues  Config values
     * @param array  $commandValues Values defined in command
     * @param array  $defaultValues Default values to use if these are not defined
     *
     * @return array $usableValues Usable values
     */
    public function loadConfigValues(
        $commandName,
        array $configValues,
        array $commandValues = [],
        array $defaultValues = []
    ) {
        $configValues = isset($configValues[$commandName]) && is_array($configValues[$commandName])
            ? $configValues[$commandName]
            : [];

        return array_merge(
            $defaultValues,
            array_filter($configValues),
            array_filter($commandValues)
        );
    }

    /**
     * This method parses the config file, if exists, and determines the real
     * option value.
     *
     * * If an option is defined as a command parameter, this will be used
     * * Otherwise, if an option is defined in the configuration file, this will
     * be used.
     * * Otherwise, default values will be used
     *
     * @param string $commandName  Command called
     * @param array  $configValues Config values
     * @param array  $commandValue Value defined in command
     * @param array  $defaultValue Default value to use if this is not defined
     *
     * @return array $usableValues Usable values
     */
    public function loadConfigValue(
        $commandName,
        array $configValues,
        array $commandValue = null,
        array $defaultValue = null
    ) {
        return isset($configValues[$commandName])
            ? $configValues[$commandName]
            : $commandValue
                ?: $defaultValue;
    }
}
