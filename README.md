PHP Formatter
=============

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
$ sudo chmod +x php-formatter.phar
$ php-formatter.phar
```

You can copy the `.phar` file as a global script

``` bash
$ cp php-formatter.phar /usr/local/bin/php-formatter
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
  help       Displays help for a command
  list       Lists commands
use
  use:sort   Sort Use statements
```

### Sort all Use Statements

You can sort your Use Statements in a very different ways. For this command you
must provide as an argument the path where to look for the PHP files you want to
process.

* command: `php-formatter use:sort`
* argument: path
* option: --group [***multiple***]
* option: --group-type=***one***|***each***
* option: --sort-type=***alph***|***length***
* option: --sort-direction=***asc***|***desc***

#### Group

You can sort your Use statements using as many groups as yo want (***--group***).
It means that you can group lines with same root (***Symfony\\***) in a specific
order.

Common group is named `_main` and if is not specified, is placed in the
begining. You can define where to place this group with `--group` option

``` bash
$ php-formatter use:sort src/ --group="Mmoreram" --group="_main" --group="Symfony"
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
$ php-formatter use:sort src/ --group="Mmoreram" --group-type="each"
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
$ php-formatter use:sort src/ --group="Mmoreram" --group-type="one"
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
$ php-formatter use:sort src/ --sort-type="alph"
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
$ php-formatter use:sort src/ --sort-type="length"
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
$ php-formatter use:sort src/ --sort-direction="asc"
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
$ php-formatter use:sort src/ --sort-direction="desc"
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
