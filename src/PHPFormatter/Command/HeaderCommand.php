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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Mmoreram\PHPFormatter\Finder\ConfigFinder;
use Mmoreram\PHPFormatter\Finder\FileFinder;
use Mmoreram\PHPFormatter\Fixer\HeaderFixer;
use Mmoreram\PHPFormatter\Loader\ConfigLoader;

/**
 * Class HeaderCommand
 */
class HeaderCommand extends Command
{
    /**
     * @var string
     *
     * Command name
     */
    const COMMAND_NAME = 'header';

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('formatter:header:fix')
            ->setDescription('Ensures that all PHP files has header defined in config file')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path'
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
        $verbose = $output->getVerbosity();
        $path = $input->getArgument('path');
        $dryRun = $input->getOption('dry-run');
        $fileFinder = new FileFinder;
        $configLoader = new ConfigLoader;
        $configFinder = new ConfigFinder;

        /**
         * This section is just for finding the right values to work with in
         * this execution.
         *
         * $options array will have, after this block, all these values
         */
        $configPath = rtrim($input->getOption('config'), DIRECTORY_SEPARATOR);

        $header = $configLoader->loadConfigValue(
            self::COMMAND_NAME,
            $configFinder->findConfigFile($configPath)
        );

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
         * Dry-run message
         */
        if ($dryRun && $verbose >= OutputInterface::VERBOSITY_VERBOSE) {

            $output->writeln('# This process has been executed in mode dry-run');
        }

        if ($verbose >= OutputInterface::VERBOSITY_VERBOSE) {

            $output->writeln('# Executing process in ' . $path);
        }

        /**
         * Creates the new HeaderFixer
         */
        $headerFixer = new HeaderFixer($header);

        $files = $fileFinder->findPHPFilesByPath($path);

        /**
         * If verbose level is higher or equal than -vv, we print the config
         * file data, if is not empty.
         */
        if ($verbose >= OutputInterface::VERBOSITY_VERBOSE) {

            $output->writeln("# Header used:\n\n" . $header);
        }

        $output->writeln('#');

        /**
         * Each found php file is processed
         */
        foreach ($files as $file) {

            $data = file_get_contents($file);
            $result = $headerFixer->fix($data);

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
    }
}
