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

namespace Mmoreram\PHPFormatter\Loader;

/**
 * Class ConfigLoader.
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
     * @param string $commandName
     * @param mixed  $configValues
     * @param mixed  $commandValues
     * @param mixed  $defaultValues
     *
     * @return array $usableValues Usable values
     *
     * If none of these is defined, then return null
     */
    public function loadConfigValues(
        $commandName,
        $configValues,
        $commandValues,
        $defaultValues
    ) {
        if (!is_array($defaultValues)) {
            return $this->loadConfigValue(
                $commandName,
                $configValues,
                $commandValues,
                $defaultValues
            );
        }

        $configValues = isset($configValues[$commandName]) && is_array($configValues[$commandName])
            ? $configValues[$commandName]
            : [];

        $result = array_merge(
            $defaultValues,
            $configValues,
            $commandValues
        );

        return !empty($result)
            ? $result
            : null;
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
     * If none of these is defined, then return null
     *
     * @param string $commandName
     * @param mixed  $configValue
     * @param mixed  $commandValue
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    public function loadConfigValue(
        $commandName,
        $configValue,
        $commandValue,
        $defaultValue
    ) {
        return array_key_exists($commandName, $configValue)
            ? ($configValue[$commandName] ?? '')
            : (!is_null($commandValue)
                ? $commandValue
                : $defaultValue);
    }
}
