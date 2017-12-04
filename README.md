# PHP Formatter

[![Build Status](https://travis-ci.org/mmoreram/php-formatter.png?branch=master)](https://travis-ci.org/mmoreram/php-formatter)

This PHP formatter aims to provide you some bulk actions for you PHP projects to
ensure their consistency. None of them fixes PSR rules. If you want to fix PSR
rules, please check [friendsofphp/php-cs-fixer](https://github.com/friendsofphp/PHP-CS-Fixer).

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

Finally you can also compile your own version of the package. ( You need set 
`phar.readonly = Off` in your php.ini ). For the compilation of this package you
need the [box-project/box2](https://github.com/box-project/box2) library.

``` bash
$ git clone git@github.com:mmoreram/php-formatter.git
$ cd php-formatter
$ composer update --no-dev
$ box build -v
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

strict: ~
header: |
    /*
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
consider any kind of Common Coding Standard, like PSR-0 or PSR-1, is more like
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
  formatter:header:fix  Ensures that all PHP files has header defined in config file
  formatter:strict:fix  Ensures that all PHP files have strict mode defined in config file. Only valid for >=PHP7.0
  formatter:use:sort    Sort Use statements
```

### Sort all Use Statements

You can sort your Use Statements in different ways. For this command you
must provide as an argument the path where to look for the PHP files you want to
process.

* command: `php-formatter formatter:use:sort`
* argument: path
* option: --exclude [***multiple***]
* option: --group [***multiple***]
* option: --group-type=***one***|***each***
* option: --sort-type=***alph***|***length***
* option: --sort-direction=***asc***|***desc***
* option: --dry-run [***no value***]

#### Group

You can sort your Use statements using as many groups as you want (***--group***).
It means that you can group lines with same root (***Symfony\\***) in a specific
order.

Common group is named `_main` and if is not specified, is placed in the
beginning. You can define where to place this group with the `--group` option

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

When using a `.formatter.yml` you can also specify subgroups by entering an array

``` yml
use-sort:
    group:
        - [Symfony\Component\HttpKernel, Symfony]
        - _main
```

This will create a Symfony group placing all `Symfony\Component\HttpKernel` classes on top.


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

or if you want only one use for all groups.

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

You can define your PHP header in your `.formatter.yml` file and this command
will check and fix that all PHP files have it properly.

* command: `php-formatter formatter:header:fix`
* argument: path
* option: --exclude [***multiple***]
* option: --dry-run [***no value***]

### Fix all strict declarations

In your >=7.0 PHP applications you can use simple type declarations in your
methods. You can define your application as relaxed as you want by declaring the
`strict_mode` variable in your files. Each php file must be configured itself,
so in order to make sure all your files have the definition after the header if
exists and before the namespace declaration, you can use this command.

* command: `php-formatter formatter:strict:fix`
* argument: path
* option: --exclude [***multiple***]
* option: --dry-run [***no value***]

You can have three values here. If you define a boolean, then each file found
will have the declaration with the boolean given.

``` yml
strict: true
```

Otherwise, if you define a '~' value then your declaration lines will be
removed.

``` yml
strict: '~'
```

## Exclude folders/files

You can exclude folders and files by using the multi-option `--exclude` as many
times as you need. This option works the same way the Symfony component
[Finder](http://symfony.com/doc/current/components/finder.html) works, so to
make sure you properly understand the way this option works, just check the
documentation.

``` bash
$ php-formatter formatter:header:fix src/ --exclude="vendor"
```

In that case, maybe the most used way, you will exclude all vendors from your
process.

## Dry run

You can use this tool just to test the files will be modified, using option
--dry-run

``` bash
$ php-formatter formatter:use:sort src/ --dry-run
```

Any command from this library will never have any impact in your code as long as
you have defined this option.