<?php

/**
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 */

namespace PHPFormatter\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UseSortCommand
 */
class UseSortCommand extends BaseCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('php-formatter:use:sort')
            ->setDescription('Sort Use statements')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path'
            )
            ->addOption(
                'groups',
                null,
                InputOption::VALUE_IS_ARRAY,
                "Groups defined?"
            );
    }

    /**
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $groups = $input->getOption('groups');
    }
}
 