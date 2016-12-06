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

namespace Mmoreram\PHPFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;
use Mmoreram\PHPFormatter\Fixer\StrictFixer;

/**
 * Class StrictCommand.
 */
final class StrictCommand extends PHPFormatterCommand
{
    /**
     * Get command alias for configuration.
     *
     * @return string
     */
    protected function getCommandConfigAlias() : string
    {
        return 'strict';
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this
            ->setName('formatter:strict:fix')
            ->setDescription('Ensures that all PHP files have strict mode defined in config file. Only valid for >=PHP7.0');

        parent::configure();
    }

    /**
     * Print used config.
     *
     * @param OutputInterface $output
     * @param mixed           $config
     */
    protected function printUsableConfig(
        OutputInterface $output,
        $config
    ) {
        if (is_bool($config)) {
            $output->writeln('# Adding strict_mode=' . ($config ? '1' : '0') . ' in your files');
        } else {
            $output->writeln('# Removing strict_mode from your files');
        }
    }

    /**
     * Get a fixer instance given the configuration.
     *
     * @param mixed $config
     *
     * @return FixerInterface
     */
    protected function getFixer($config) : FixerInterface
    {
        return new StrictFixer($config);
    }

    /**
     * Get command config values.
     *
     * @param InputInterface $input
     *
     * @return mixed
     */
    protected function getCommandConfigValue(InputInterface $input)
    {
        return null;
    }

    /**
     * Get default config values.
     *
     * @return mixed
     */
    protected function getDefaultConfigValue()
    {
        return null;
    }
}
