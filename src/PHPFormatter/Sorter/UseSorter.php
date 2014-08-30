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

namespace Mmoreram\PHPFormatter\Sorter;

use Mmoreram\PHPFormatter\Sorter\Interfaces\SorterInterface;

/**
 * Class UseSorter
 */
class UseSorter implements SorterInterface
{
    /**
     * @var integer
     *
     * Sort type Alphabetic
     */
    const SORT_TYPE_ALPHABETIC = 'alph';

    /**
     * @var integer
     *
     * Sort type length
     */
    const SORT_TYPE_LENGTH = 'length';

    /**
     * @var integer
     *
     * Sort direction ascendent
     */
    const SORT_DIRECTION_ASC = 'asc';

    /**
     * @var integer
     *
     * Sort direction descendent
     */
    const SORT_DIRECTION_DESC = 'desc';

    /**
     * @var integer
     *
     * Group type one USE
     */
    const GROUP_TYPE_ONE = 'one';

    /**
     * @var integer
     *
     * Group type each USE
     */
    const GROUP_TYPE_EACH = 'each';

    /**
     * @var
     */

    /**
     * @var array
     *
     * Groups
     */
    protected $groups = array();

    /**
     * @var integer
     *
     * Sort type
     */
    protected $sortType = self::SORT_TYPE_ALPHABETIC;

    /**
     * @var integer
     *
     * Sort direction
     */
    protected $sortDirection = self::SORT_DIRECTION_ASC;

    /**
     * @var integer
     *
     * Group type
     */
    protected $groupType = self::GROUP_TYPE_EACH;

    /**
     * Sets Groups
     *
     * @param array $groups Groups
     *
     * @return UseSorter Self object
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Sets SortDirection
     *
     * @param mixed $sortDirection SortDirection
     *
     * @return UseSorter Self object
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * Sets SortType
     *
     * @param mixed $sortType SortType
     *
     * @return UseSorter Self object
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;

        return $this;
    }

    /**
     * Sets GroupType
     *
     * @param int $groupType GroupType
     *
     * @return UseSorter Self object
     */
    public function setGroupType($groupType)
    {
        $this->groupType = $groupType;

        return $this;
    }

    /**
     * Sort any piece of code given as parameter
     *
     * @param string $data Data
     *
     * @return false|string Data processed or false if no use block has been found
     */
    public function sort($data)
    {
        $regex = '/(\s*(?:(?:\s+use\s+?[\w\\\\\,\s]+?;)+)\s+)/s';
        preg_match($regex, $data, $results);

        if (!isset($results[0])) {
            return false;
        }

        $result = $results[0];
        $blocks = explode(';', $result);
        $namespaces = array();

        foreach ($blocks as $block) {

            /**
             * Removing use literal
             */
            $block = trim(preg_replace('/^\s+use\s+/', '', $block));

            $namespaces = array_merge(
                $namespaces,
                explode(',', $block)
            );
        }

        /**
         * Trim all results of blank spaces and line breaks
         */
        $namespaces = array_map(function ($namespace) {
            return trim($namespace);
        }, $namespaces);

        /**
         * If any position becomes empty, removes
         */
        $namespaces = array_filter($namespaces, function ($namespace) {
            return !empty($namespace);
        });

        /**
         * Grouping use statements by blocks defined in blocks variable
         */
        $groups = $this->createGroups($namespaces);

        /**
         * Every block is sorted as desired
         */
        foreach ($groups as $groupKey => $group) {

            $groups[$groupKey] = $this->sortGroup($group);
        }

        $doubleEOL = PHP_EOL . PHP_EOL;

        $processedResult = $doubleEOL . trim(implode($doubleEOL, array_map(
                    function ($group) {
                        return $this->renderGroup($group);
                    }, $groups)
            )) . $doubleEOL;

        return str_replace($result, $processedResult, $data);
    }

    /**
     * Create blocks
     *
     * @param array $namespaces Namespaces
     *
     * @return array Groups
     */
    private function createGroups(array $namespaces)
    {
        $groups = array_fill_keys($this->groups, array());

        if (!array_key_exists('_main', $groups)) {

            $groups = array_merge(
                array('_main' => array()),
                $groups
            );
        }

        foreach ($namespaces as $namespace) {

            foreach ($groups as $groupKey => $group) {

                if (strpos($namespace, $groupKey) === 0) {

                    array_push($groups[$groupKey], $namespace);

                    continue 2;
                }
            }

            array_push($groups['_main'], $namespace);
        }

        return $groups;
    }

    /**
     * Sort a group
     *
     * @param array $group Group to sort
     *
     * @return array $group Sorted
     */
    private function sortGroup(array $group)
    {
        if ($this->sortType == self::SORT_TYPE_LENGTH) {

            usort($group, function ($a, $b) {

                $cmp = strlen($b) - strlen($a);

                if ($cmp === 0) {

                    $a = strtolower($a);
                    $b = strtolower($b);

                    $cmp = strcmp($b, $a);
                }

                return $cmp;
            });
        } elseif ($this->sortType == self::SORT_TYPE_ALPHABETIC) {

            usort($group, function ($a, $b) {
                $a = strtolower($a);
                $b = strtolower($b);

                $cmp = strcmp($b, $a);
                if ($cmp === 0) {

                    $cmp = strlen($b) - strlen($a);
                }

                return $cmp;
            });
        }

        if ($this->sortDirection == self::SORT_DIRECTION_ASC) {

            $group = array_reverse($group);
        }

        return $group;
    }

    /**
     * Render a group
     *
     * @param array $group Group
     *
     * @return string Group rendered
     */
    private function renderGroup(array $group)
    {
        if ($this->groupType === self::GROUP_TYPE_EACH) {
            return implode(PHP_EOL, array_map(function ($namespace) {

                return 'use ' . $namespace . ';';
            }, $group));
        } elseif ($this->groupType === self::GROUP_TYPE_ONE) {

            $group = implode(',' . PHP_EOL . '    ', $group);

            return 'use ' . $group . ';';
        }
    }

}
