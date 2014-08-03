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

namespace Mmoreram\PHPFormatter\Command;

use Exception;
use IteratorAggregate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Mmoreram\PHPFormatter\Finder\ConfigFinder;
use Mmoreram\PHPFormatter\Finder\FileFinder;
use Mmoreram\PHPFormatter\Loader\ConfigLoader;
use Mmoreram\PHPFormatter\Sorter\UseSorter;

/**
 * Class UseSortCommand
 */
class UseSortCommand extends Command
{
    /**
     * @var string
     *
     * Command name
     */
    const COMMAND_NAME = 'use-sort';

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
                "Sort type"
            )
            ->addOption(
                'sort-direction',
                null,
                InputOption::VALUE_OPTIONAL,
                "Sort direction"
            )
            ->addOption(
                'group-type',
                null,
                InputOption::VALUE_OPTIONAL,
                "Type of grouping"
            )
            ->addOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                "Config file directory",
                getcwd()
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                "Just print the result, nothing is overwritten"
            );
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        /**
         * We load the options to work with
         */
        $options = $this->getUsableConfig($input);

        /**
         * Building the real directory or file to work in
         */
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($path)) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }

        if (!is_file($path) && !is_dir($path)) {

            throw new Exception('Directory or file "' . $path . '" does not exist');
        }

        /**
         * Print dry-run message if needed
         */
        $this->printDryRunMessage(
            $input,
            $output,
            $path
        );

        /**
         * Print all configuration block if verbose level allows it
         */
        $this->printConfigUsed(
            $output,
            $options
        );

        $fileFinder = new FileFinder;
        $files = $fileFinder->findPHPFilesByPath($path);

        /**
         * Parse and fix all found files
         */
        $this->parseAndFixFiles(
            $input,
            $output,
            $files,
            $options
        );
    }

    /**
     * Load config
     *
     * @param InputInterface $input Input
     *
     * @return array Config array
     */
    public function getUsableConfig(InputInterface $input)
    {
        $configLoader = new ConfigLoader;
        $configFinder = new ConfigFinder;

        /**
         * This section is just for finding the right values to work with in
         * this execution.
         *
         * $options array will have, after this block, all these values
         */
        $configPath = rtrim($input->getOption('config'), DIRECTORY_SEPARATOR);

        return $configLoader->loadConfigValues(
            self::COMMAND_NAME,
            $configFinder->findConfigFile($configPath),
            array(
                'group' => $input->getOption('group'),
                'group-type' => $input->getOption('group-type'),
                'sort-type' => $input->getOption('sort-type'),
                'sort-direction' => $input->getOption('sort-direction')
            ),
            array(
                'group' => array('_main'),
                'group-type' => UseSorter::GROUP_TYPE_EACH,
                'sort-type' => UseSorter::SORT_TYPE_ALPHABETIC,
                'sort-direction' => UseSorter::SORT_DIRECTION_ASC
            )
        );
    }

    /**
     * Print the Dry-run message if needed
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     * @param string          $path   Path
     *
     * @return UseSortCommand self Object
     */
    public function printDryRunMessage(
        InputInterface $input,
        OutputInterface $output,
        $path
    )
    {
        $dryRun = $input->getOption('dry-run');
        $verbose = $output->getVerbosity();

        /**
         * Dry-run message
         */
        if ($dryRun && $verbose >= OutputInterface::VERBOSITY_VERBOSE) {

            $output->writeln('# This process has been executed in mode dry-run');
        }

        if ($verbose >= OutputInterface::VERBOSITY_VERBOSE) {

            $output->writeln('# Executing process in ' . $path);
        }

        return $this;
    }

    /**
     * Print the configuration used by the command
     *
     * @param OutputInterface $output  Output
     * @param array           $options Options used by the command
     *
     * @return UseSortCommand self Object
     */
    public function printConfigUsed(
        OutputInterface $output,
        array $options
    )
    {
        $verbose = $output->getVerbosity();

        /**
         * If verbose level is higher or equal than -vv, we print the config
         * file data, if is not empty.
         */
        if ($verbose >= OutputInterface::VERBOSITY_VERBOSE) {

            $output->writeln('# Executing process with this configuration');
            if (!empty($options['group'])) {

                foreach ($options['group'] as $group) {

                    $output->writeln('#   --group="' . $group . '"');
                }
            }

            if (!empty($options['group-type'])) {

                $output->writeln('#   --group-type="' . $options['group-type'] . '"');
            }

            if (!empty($options['sort-type'])) {

                $output->writeln('#   --sort-type="' . $options['sort-type'] . '"');
            }

            if (!empty($options['sort-direction'])) {

                $output->writeln('#   --sort-direction="' . $options['sort-direction'] . '"');
            }
        }

        $output->writeln('#');

        return $this;
    }

    /**
     * Parse and fix all files found
     *
     * @param InputInterface    $input   Input
     * @param OutputInterface   $output  Output
     * @param IteratorAggregate $files   Files
     * @param array             $options Options
     *
     * @return UseSortCommand self Object
     */
    public function parseAndFixFiles(
        InputInterface $input,
        OutputInterface $output,
        IteratorAggregate $files,
        array $options

    )
    {
        $dryRun = $input->getOption('dry-run');
        $verbose = $output->getVerbosity();
        $useSorter = $this->createUseSorter($options);

        /**
         * Each found php file is processed
         */
        foreach ($files as $file) {

            $data = file_get_contents($file);
            $result = $useSorter->sort($data);

            if ($result === false || $data === $result) {

                continue;
            }

            if ($verbose >= OutputInterface::VERBOSITY_NORMAL) {

                $output->writeln('# ' . $file);
            }

            if (!$dryRun) {

                file_put_contents($file, $result);
            }
        }

        return $this;
    }

    /**
     * Create UseSorter Object given a configuration
     *
     * @param array $options Options
     *
     * @return UseSorter Use sorter instance
     */
    public function createUseSorter(array $options)
    {
        /**
         * Creates the new UseSorter file, given config values
         */
        $useSorter = new UseSorter();
        $useSorter
            ->setGroups($options['group'])
            ->setGroupType($options['group-type'])
            ->setSortType($options['sort-type'])
            ->setSortDirection($options['sort-direction']);

        return $useSorter;
    }
}
