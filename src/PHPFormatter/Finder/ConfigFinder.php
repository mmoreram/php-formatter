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

namespace Mmoreram\PHPFormatter\Finder;

use Symfony\Component\Yaml\Parser as YamlParser;

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
            $yamlParser = new YamlParser();
            $config = $yamlParser->parse(file_get_contents($configFilePath));
        }

        return $config;
    }
}
