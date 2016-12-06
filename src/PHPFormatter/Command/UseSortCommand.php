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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\PHPFormatter\Fixer\Interfaces\FixerInterface;
use Mmoreram\PHPFormatter\Fixer\UseSortFixer;

/**
 * Class UseSortCommand.
 */
final class UseSortCommand extends PHPFormatterCommand
{
    /**
     * Get command alias for configuration.
     *
     * @return string
     */
    protected function getCommandConfigAlias() : string
    {
        return 'use-sort';
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this
            ->setName('formatter:use:sort')
            ->setDescription('Sort Use statements')
            ->addOption(
                'group',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Groups defined?'
            )
            ->addOption(
                'sort-type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Sort type'
            )
            ->addOption(
                'sort-direction',
                null,
                InputOption::VALUE_OPTIONAL,
                'Sort direction'
            )
            ->addOption(
                'group-type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Type of grouping'
            )
            ->addOption(
                'group-skip-empty',
                null,
                InputOption::VALUE_NONE,
                'Skip empty groups'
            );

        parent::configure();
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
        $config = [];
        if (!empty($input->getOption('group'))) {
            $config['group'] = $input->getOption('group');
        }

        if (true === ($input->getOption('group-skip-empty'))) {
            $config['group-skip-empty'] = true;
        }

        return array_merge(
            $config,
            array_filter([
                'group-type' => $input->getOption('group-type'),
                'sort-type' => $input->getOption('sort-type'),
                'sort-direction' => $input->getOption('sort-direction'),
            ], function ($element) {
                return !is_null($element);
            })
        );
    }

    /**
     * Get default config values.
     *
     * @return mixed
     */
    protected function getDefaultConfigValue()
    {
        return [
            'group' => ['_main'],
            'group-type' => UseSortFixer::GROUP_TYPE_EACH,
            'group-skip-empty' => false,
            'sort-type' => UseSortFixer::SORT_TYPE_ALPHABETIC,
            'sort-direction' => UseSortFixer::SORT_DIRECTION_ASC,
        ];
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
        foreach ($config['group'] as $group) {
            $output->writeln('#   --group="' . $group . '"');
        }
        $output->writeln('#   --group-type="' . $config['group-type'] . '"');
        $output->writeln('#   --group-skip-empty="' . ($config['group-skip-empty'] ? 'true' : 'false') . '"');
        $output->writeln('#   --sort-type="' . $config['sort-type'] . '"');
        $output->writeln('#   --sort-direction="' . $config['sort-direction'] . '"');
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
        $useSortFixer = new UseSortFixer();
        $useSortFixer
            ->setGroups($config['group'])
            ->setGroupType($config['group-type'])
            ->setGroupSkipEmpty($config['group-skip-empty'])
            ->setSortType($config['sort-type'])
            ->setSortDirection($config['sort-direction']);

        return $useSortFixer;
    }
}
