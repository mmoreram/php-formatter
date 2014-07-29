<?php

/**
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

namespace PHPFormatter\Command;

use PHPFormatter\Finder\FileFinder;
use PHPFormatter\UseSorter;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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
                'group',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                "Groups defined?"
            )
            ->addOption(
                'sort-type',
                null,
                InputOption::VALUE_OPTIONAL,
                "Sort type",
                UseSorter::SORT_TYPE_ALPHABETIC
            )
            ->addOption(
                'sort-direction',
                null,
                InputOption::VALUE_OPTIONAL,
                "Sort direction",
                UseSorter::SORT_DIRECTION_ASC
            )
            ->addOption(
                'group-type',
                null,
                InputOption::VALUE_OPTIONAL,
                "Type of grouping",
                UseSorter::GROUP_TYPE_EACH
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
        $groups = $input->getOption('group');
        $sortType = $input->getOption('sort-type');
        $sortDirection = $input->getOption('sort-direction');
        $groupType = $input->getOption('group-type');

        if (null !== $path) {

            $filesystem = new Filesystem();
            if (!$filesystem->isAbsolutePath($path)) {
                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            }
        }

        $useSorter = new UseSorter();
        $useSorter
            ->setGroups($groups)
            ->setSortType($sortType)
            ->setSortDirection($sortDirection)
            ->setGroupType($groupType);

        $finder = new FileFinder();
        $files = $finder->findPHPFilesByPath($path);

        foreach ($files as $file) {

            $data = file_get_contents($file);
            $result = $useSorter->sort($data);

            echo $result;
            die();
            //file_put_contents($file, $data);
        }
    }
}
 