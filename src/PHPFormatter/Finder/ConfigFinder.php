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

use Symfony\Component\Yaml\Yaml;

use Mmoreram\PHPFormatter\PHPFormatter;

/**
 * Class ConfigFinder.
 */
class ConfigFinder
{
    /**
     * Load, if exists, specific project configuration.
     *
     * @param string $path Path
     *
     * @return array loaded config
     */
    public function findConfigFile($path)
    {
        $configFilePath = rtrim($path, '/') . '/' . PHPFormatter::CONFIG_FILE_NAME;
        $config = [];
        if (is_file($configFilePath)) {
            $yamlParser = new Yaml();
            $config = $yamlParser->parse(file_get_contents($configFilePath));
        }

        return $config;
    }
}
