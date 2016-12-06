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

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use Mmoreram\PHPFormatter\Finder\ConfigFinder;
use Mmoreram\PHPFormatter\Finder\FileFinder;
use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;
use Mmoreram\PHPFormatter\Loader\ConfigLoader;

/**
 * Class PHPFormatterCommand.
 */
abstract class PHPFormatterCommand extends Command
{
    /**
     * configure.
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path'
            )
            ->addOption(
                '--exclude',
                '-e',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Excluded folders',
                []
            )
            ->addOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                'Config file directory',
                getcwd()
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Just print the result, nothing is overwritten'
            );
    }

    /**
     * Execute command.
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
        $excludes = $input->getOption('exclude');
        $verbose = $output->getVerbosity();
        $configValue = $this->getUsableConfig($input);

        if ($verbose >= OutputInterface::VERBOSITY_VERBOSE) {
            $this
                ->printInitMessage(
                    $input,
                    $output,
                    $path,
                    $excludes
                );
            $output->writeln('#');
            $output->writeln('# Executing process with this configuration');
            $this->printUsableConfig($output, $configValue);
        }

        $files = $this
            ->loadFiles(
                $path,
                $excludes
            );

        if ($verbose >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('# Found ' . $files->count() . ' files');
            $output->writeln('# Starting format...');
            $output->writeln('#');
        }

        $fixer = $this->getFixer($configValue);
        $this->fixFiles(
            $input,
            $output,
            $files,
            $fixer
        );
    }

    /**
     * Get a fixer instance given the configuration.
     *
     * @param mixed $config
     *
     * @return FixerInterface
     */
    abstract protected function getFixer($config) : FixerInterface;

    /**
     * Get command alias for configuration.
     *
     * @return string
     */
    abstract protected function getCommandConfigAlias() : string;

    /**
     * Print the Dry-run message if needed.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $path
     * @param array           $excludes
     */
    protected function printInitMessage(
        InputInterface $input,
        OutputInterface $output,
        string $path,
        array $excludes
    ) {
        $output->writeln('# PHP Formatter');

        /*
         * Dry-run message
         */
        if ($input->getOption('dry-run')) {
            $output->writeln('# <info> This process has been executed in mode dry-run');
        }

        $output->writeln('# Executing process in ' . $path);
        foreach ($excludes as $exclude) {
            $output->writeln('#     Path excluded - ' . $exclude);
        }
    }

    /**
     * Load config values from configuration files and/or the command execution.
     *
     * @param InputInterface $input Input
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getUsableConfig(InputInterface $input)
    {
        $configLoader = new ConfigLoader();
        $configFinder = new ConfigFinder();

        /**
         * This section is just for finding the right values to work with in
         * this execution.
         *
         * $options array will have, after this block, all these values
         */
        $configPath = rtrim($input->getOption('config'), DIRECTORY_SEPARATOR);

        $configValue = $configLoader->loadConfigValues(
            $this->getCommandConfigAlias(),
            $configFinder->findConfigFile($configPath),
            $this->getCommandConfigValue($input),
            $this->getDefaultConfigValue()

        );

        if (is_null($configValue)) {
            throw new Exception("Config definition must be defined in .formatter.yml file under {$this->getCommandConfigAlias()}");
        }

        return $configValue;
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
    }

    /**
     * Load files to work with.
     *
     * @param string $path
     * @param array  $excludes
     *
     * @return Finder|SplFileInfo[]
     *
     * @throws Exception
     */
    protected function loadFiles(
        string $path,
        array $excludes
    ) {
        $fileFinder = new FileFinder();

        /**
         * Building the real directory or file to work in.
         */
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($path)) {
            $path = getcwd() . DIRECTORY_SEPARATOR . ltrim($path, '/');
        }

        if (!is_file($path) && !is_dir($path)) {
            throw new Exception('Directory or file "' . $path . '" does not exist');
        }

        return $fileFinder
            ->findPHPFilesByPath(
                $path,
                $excludes
            );
    }

    /**
     * Fix files with given fixer.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Finder          $files
     * @param FixerInterface  $fixer
     */
    protected function fixFiles(
        InputInterface $input,
        OutputInterface $output,
        Finder $files,
        FixerInterface $fixer
    ) {
        $verbose = $output->getVerbosity();

        /*
         * Each found php file is processed
         */
        foreach ($files as $file) {
            $data = $file->getContents();
            $result = $fixer->fix($data);

            if ($result === false || $data === $result) {
                continue;
            }

            if ($verbose >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('# ' . $file);
            }

            if (!$input->getOption('dry-run')) {
                file_put_contents($file->getRealPath(), $result);
            }
        }
    }

    /**
     * Get command config values.
     *
     * @param InputInterface $input
     *
     * @return mixed
     */
    abstract protected function getCommandConfigValue(InputInterface $input);

    /**
     * Get default config values.
     *
     * @return mixed
     */
    abstract protected function getDefaultConfigValue();
}
