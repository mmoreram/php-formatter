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

use Mmoreram\PHPFormatter\Fixer\HeaderFixer;
use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;

/**
 * Class HeaderCommand.
 */
final class HeaderCommand extends PHPFormatterCommand
{
    /**
     * Get command alias for configuration.
     *
     * @return string
     */
    protected function getCommandConfigAlias() : string
    {
        return 'header';
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this
            ->setName('formatter:header:fix')
            ->setDescription('Ensures that all PHP files have the header defined in the config file');

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
        $output->writeln("# Header used:\n\n" . $config);
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
        return new HeaderFixer($config);
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
