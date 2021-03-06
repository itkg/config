Database update command
=======================

to execute this command :

```bash
php app/console.php itkg-core:database:update
```

# Default release structure

* Default release directory is under /script/releases directory (you can override it, see below)
* Your release should be a version number (1.0, 1.1, etc)
* Your release must have two directories : script directory & rollback directory
* Each directory must contain the same number of scripts with the same name
* Each script is a PHP file which is executed with a class context (see : [Loader class](https://github.com/itkg/core/blob/master/src/Itkg/Core/Command/DatabaseUpdate/Loader.php))


Default structure example :
```text
.
+-- script
|  +-- templates
|     +-- layout.template (optional)
|     +-- pre_create_table_template.php (optional)
|     +-- etc
|  +-- releases
|     +-- release_version
|        +-- script
|           +-- script_1.php
|        +-- rollback
|           +-- script_1.php
```

Script example

```php
    /**
     * @var \Itkg\Core\Command\DatabaseUpdate\Loader
     */
    $this->addQuery("insert into YOUR_TABLE (YOUR_FIELDS)
    values (YOUR_VALUES)");
```

# Script options

## Display a release script
```bash
php app/console.php itkg-core:database:update RELEASE_VERSION
```

This command will run all scripts under release_version directory & display results (no execution at this time)

You can add output colors by adding colors option

```bash
php app/console.php itkg-core:database:update RELEASE_VERSION --colors
```

## Execute a release script

To execute release, add execute option

```bash
php app/console.php itkg-core:database:update RELEASE_VERSION --execute
```

If a script execution failed, his rollback is played to restore database structure

## Execute a specific script under a release_version

To execute specific script add script option

```bash
php app/console.php itkg-core:database:update RELEASE_VERSION --script=YOUR_SCRIPT_NAME
```

## Play rollback first

You may want to play rollback only with rollback-first option :

```bash
php app/console.php itkg-core:database:update RELEASE_VERSION --rollback-first
```

## Force a rollback

You can force rollback script to restore state after script execution with force-rollback option :

```bash
php app/console.php itkg-core:database:update RELEASE_VERSION --force-rollback
```

## Override default releases directory

You can define another releases directory by specifiying path option :

```bash
php app/console.php itkg-core:database:update RELEASE_VERSION --path=/path/to/you/releases/directory
```

## Decorate queries with hooks

You may want to add some queries / info before or after a specific query.
For example, your delivery script may contain roles management that don't exist in your DEV environment.

You can create hook for that by adding templates file in your templates folder.

Each hook is a PHP file which is executed with a class context (see : [Loader class](https://github.com/itkg/core/blob/master/src/Itkg/Core/Command/DatabaseUpdate/Template/Loader.php))

Imagine, you want to create a synonym for a create table & grant access to a specific user (only on your PROD env)
Follow these steps :

* Create a file named post_create_table_template.php
* Add this code into this file :

```php
/**
     * @var \Itkg\Core\Command\DatabaseUpdate\Template\Loader
     */
$this->addQuery('CREATE OR REPLACE SYNONYM MY_SYNONYM.{identifier} for {identifier}'); // identifier is your table name
$this->addQuery('GRANT SELECT,INSERT,UPDATE,DELETE ON {identifier} TO YOUR_ROLE'); // identifier is you table name

```
That's all!

You can add "pre" or "post" hook for all queries type :
* insert
* update
* delete
* create_table
* drop_table
* create_view
* drop_view
* create_sequence
* drop_sequence
* create_synonym
* drop_synonym
* alter
* create_index
* drop_index
* grant (Partially implemented)

! Warning : Decorated queries will not be executed with your script. If you want to execute a query, add it in your script !

### Layout

You can format your delivery script using a layout file named layout.template (see standard structure)

You can use vars into this template to place your different queries.

Example :

```php

My personalized layout file

My create tables {create_table}

Others queries (without queries already added) {all}
```

You can use these variables :

* {insert}
* {update}
* {delete}
* {create_table}
* {drop_table}
* {create_sequence}
* {drop_sequence}
* {create_synonym}
* {drop_synonym}
* {alter}
* {create_index}
* {create_view}
* {drop_view}
* {drop_index}
* {grant}