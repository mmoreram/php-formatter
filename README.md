PHP Formatter
=============
[![Build Status](https://travis-ci.org/mmoreram/php-formatter.png?branch=master)](https://travis-ci.org/mmoreram/php-formatter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mmoreram/php-formatter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mmoreram/php-formatter/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mmoreram/php-formatter/v/stable.png)](https://packagist.org/packages/mmoreram/php-formatter)
[![Latest Unstable Version](https://poser.pugx.org/mmoreram/php-formatter/v/unstable.png)](https://packagist.org/packages/mmoreram/php-formatter)

This PHP formatter aims to provide you some bulk actions for you PHP projects to
ensure their consistency. Any of them fixes PSR rules. If you want to fix PSR
rules, please check [fabpot/php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer).

## Tags

* Use last unstable version ( alias of `dev-master` ) to stay in last commit
* Use last stable version tag to stay in a stable release.
* [![Latest Unstable Version](https://poser.pugx.org/mmoreram/php-formatter/v/unstable.png)](https://packagist.org/packages/mmoreram/php-formatter)
[![Latest Stable Version](https://poser.pugx.org/mmoreram/php-formatter/v/stable.png)](https://packagist.org/packages/mmoreram/php-formatter)

## Install

Install PHP Formatter in this way:

``` bash
$ composer global require mmoreram/php-formatter=dev-master
```

If it is the first time you globally install a dependency then make sure
you include `~/.composer/vendor/bin` in $PATH as shown [here](http://getcomposer.org/doc/03-cli.md#global).

### Always keep your PHP Formatter installation up to date:

``` bash
$ composer global update mmoreram/php-formatter
```

### .phar file

You can also use already last built `.phar`.

``` bash
$ git clone git@github.com:mmoreram/php-formatter.git
$ cd php-formatter
$ php build/php-formatter.phar
```

You can copy the `.phar` file as a global script

``` bash
$ cp build/php-formatter.phar /usr/local/bin/php-formatter
```

### Compile

Finally you can also compile your own version of the package

``` bash
$ git clone git@github.com:mmoreram/php-formatter.git
$ cd php-formatter
$ composer update
$ php bin/compile
$ sudo chmod +x build/php-formatter.phar
$ build/php-formatter.phar
```

You can copy the `.phar` file as a global script

``` bash
$ cp build/php-formatter.phar /usr/local/bin/php-formatter
```

## Config

You can place a file named `.formatter.yml` in the root of your project. In
every command execution, this will be the priority of the definitions.

If an option is set in the command, this will be used. Otherwise, if is defined
in a found config file, this will be used. Otherwise, default value will be
used.

This is the config reference

``` yml
use-sort:
    group:
        - _main
        - Mmoreram
    group-type: each
    sort-type: alph
    sort-direction: asc

header: |
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
```

you can also define where to search the `.formatter.yml` file using the
`--config|-c` option

``` bash
$ php-formatter formatter:use:sort src/ --config="src/"
```

## Commands

PHP Formatter is a set of commands useful for your PHP projects. They do not
consider any kind of Common Coding Standard, lke PSR-0 or PSR-1, is more like
a useful way of working for developers and reviewers.

``` bash
Console Tool

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any message.
  --verbose        -v|vv|vvv Increase the verbosity of messages
  --version        -V Display this application version.
  --ansi              Force ANSI output.
  --no-ansi           Disable ANSI output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  help                   Displays help for a command
  list                   Lists commands
formatter
  formatter:header:fix   Ensures that all PHP files has header defined in config file
  formatter:use:sort     Sort Use statements
```

### Sort all Use Statements

You can sort your Use Statements in a very different ways. For this command you
must provide as an argument the path where to look for the PHP files you want to
process.

* command: `php-formatter formatter:use:sort`
* argument: path
* option: --group [***multiple***]
* option: --group-type=***one***|***each***
* option: --sort-type=***alph***|***length***
* option: --sort-direction=***asc***|***desc***
* option: --dry-run [***no value***]

#### Dry run

You can use this tool just to test the files will be modified, using option
--dry-run

``` bash
$ php-formatter formatter:use:sort src/ --dry-run
```

#### Group

You can sort your Use statements using as many groups as yo want (***--group***).
It means that you can group lines with same root (***Symfony\\***) in a specific
order.

Common group is named `_main` and if is not specified, is placed in the
begining. You can define where to place this group with `--group` option

``` bash
$ php-formatter formatter:use:sort src/ --group="Mmoreram" --group="_main" --group="Symfony"
```

This command will sort the code like this

``` php
use Mmoreram\MyClass;
use Mmoreram\MySecondClass;

use OneBundle\OneClass;
use AnotherBundle\AnotherClass;

use Symfony\OneClass;
use Symfony\AnotherClass;
```

As you can see, a blank line is placed between groups. If any group is defined,
one big group is created with all namespaces.

Finally, the `--group-type` defines if you want one `use` literal in every
namespace line

``` bash
$ php-formatter formatter:use:sort src/ --group="Mmoreram" --group-type="each"
```

This command will sort the code like this

``` php
use AnotherBundle\AnotherClass;
use OneBundle\OneClass;
use Symfony\AnotherClass;
use Symfony\OneClass;

use Mmoreram\MyClass;
use Mmoreram\MySecondClass;
```

or if you want only one use for all group.

``` bash
$ php-formatter formatter:use:sort src/ --group="Mmoreram" --group-type="one"
```

This command will sort the code like this

``` php
use AnotherBundle\AnotherClass,
    OneBundle\OneClass,
    Symfony\AnotherClass,
    Symfony\OneClass;

use Mmoreram\MyClass,
    Mmoreram\MySecondClass;
```

#### Sort

There are two options of sorting. You can sort your namespaces alphabetically
***(default value)***

``` bash
$ php-formatter formatter:use:sort src/ --sort-type="alph"
```

This command will sort the code like this

``` php
use AnotherBundle\AnotherClass;
use Mmoreram\MyClass;
use Mmoreram\MySecondClass;
use OneBundle\OneClass;
use Symfony\AnotherClass;
use Symfony\OneClass;
```

or by length (two namespaces with same length will be sorted alphabetically)

``` bash
$ php-formatter formatter:use:sort src/ --sort-type="length"
```

This command will sort the code like this

``` php
use AnotherBundle\AnotherClass;
use Mmoreram\MySecondClass;
use Symfony\AnotherClass;
use OneBundle\OneClass;
use Mmoreram\MyClass;
use Symfony\OneClass;
```

You can also define the direction of the sorting. This can be ascending
***(default value)***

``` bash
$ php-formatter formatter:use:sort src/ --sort-direction="asc"
```

This command will sort the code like this

``` php
use AnotherBundle\AnotherClass;
use Mmoreram\MyClass;
use Mmoreram\MySecondClass;
use OneBundle\OneClass;
use Symfony\AnotherClass;
use Symfony\OneClass;
```

or descending

``` bash
$ php-formatter formatter:use:sort src/ --sort-direction="desc"
```

This command will sort the code like this

``` php
use Symfony\OneClass;
use Symfony\AnotherClass;
use OneBundle\OneClass;
use Mmoreram\MySecondClass;
use Mmoreram\MyClass;
use AnotherBundle\AnotherClass;
```

### Fix all PHP headers

You can define your PHP header in your `.formatter.yml` file an this command
will check and fix that all PHP files have it properly.

* command: `php-formatter formatter:header:fix`
* argument: path
* option: --dry-run [***no value***]

#### Dry run

You can use this tool just to test the files will be modified, using option
--dry-run

``` bash
$ php-formatter formatter:header:fix src/ --dry-run
```

Your PHP Header definition must have this format.

``` yml
header: |
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
```
