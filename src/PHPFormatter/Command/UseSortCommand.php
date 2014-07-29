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

use PHPFormatter\UseSorter;
use PHPFormatter\Finder\FileFinder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

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
            ->setName('use:sort')
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
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                "Just print the result, nothing is overwritten"
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
        $dryRun = $input->getOption('dry-run');

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

        if ($dryRun) {

            $output->writeln('This process is Dry-run');
            $output->writeln('');
        }

        foreach ($files as $file) {

            $data = file_get_contents($file);
            $result = $useSorter->sort($data);

            if ($result === false || $data === $result) {

                continue;
            }

            $output->writeln($file);

            if (!$dryRun) {

                file_put_contents($file, $result);
            }
        }
    }
}
