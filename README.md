# ssshell

## REPL for SilverStripe running on Psy Shell 🚀

![status](https://github.com/pstaender/ssshell/actions/workflows/ci.yml/badge.svg)

### Install

Install in your current project with:

```sh
  $ composer require --dev pstaender/ssshell
```

### Usage

To use ssshell just type `./vendor/bin/ssshell` (or `ssshell` if its installed globally) in your project folder and you can start using the shell:

```sh
  Psy Shell v0.12.9 (PHP 8.4.8 — cli) by Justin Hileman
  Loading dev environment (SilverStripe CMS: 6.0.0)
  >
```

### Command Line Options

You can use most of psyshs' cli arguments. Type `psysh -h` for help.

### Requirements

sshell runs on SilverStripe v6.

For SilverStripe v4 and v5 you can use the `ss4` / `ss5`-branches or stick to corresponding versions.

### Features

#### Namespaces and views of objects and lists

`ssshell` comes with a set of [frequent used namespaces](https://github.com/pstaender/ssshell/blob/master/src/SSShell/NamespacesCommand.php#L17) for convenient REPL handling.

By default all DataObjects, DataLists, ArrayLists and Query objects will be displayed in a human-readable fashion.

As example, creating a SilverStripe User would be:

```sh
  > Member::create(['Email' => 'editor', 'Password' => 'password'])->write()
  => 1
  > Member::get()->first()
  => SilverStripe\Security\Member {#3229
      +ClassName: "SilverStripe\Security\Member",
      +LastEdited: "2019-07-01 11:34:54",
      +Created: "2019-07-01 11:34:54",
      +Email: "editor",
      +Password: "$2y$10$9b5f51921992948f40cf7uHeqjQLuG9Bnqf4sq54TBnsB80CmwJhC",
      +PasswordEncryption: "blowfish",
      +Salt: "10$9b5f51921992948f40cf75",
      +Locale: "en_US",
      +FailedLoginCount: 0,
      +ID: 1,
      +RecordClassName: "SilverStripe\Security\Member",
      +LoggedPasswords: [
        [
          "ClassName" => "SilverStripe\Security\MemberPassword",
          "LastEdited" => "2019-07-01 11:34:54",
          "Created" => "2019-07-01 11:34:54",
          "Password" => "$2y$10$9b5f51921992948f40cf7uHeqjQLuG9Bnqf4sq54TBnsB80CmwJhC",
          "Salt" => "10$9b5f51921992948f40cf75",
          "PasswordEncryption" => "blowfish",
          "MemberID" => 1,
          "ID" => 1,
          "RecordClassName" => "SilverStripe\Security\MemberPassword",
        ],
      ],
      +RememberLoginHashes: [],
      +LinkTracking: [],
      +FileTracking: [],
      +Groups: [],
    }
```

#### Sake command

You can use all familiar sake commands:

```sh
  > sake db:build
  Building database SS_test using SilverStripe\ORM\Connect\MySQL 8.0.16
  ----------------------------------------------------------------------------
  Creating database tables

  * File (0 records)
  * SiteConfig (1 records)
  …
```

```sh
> sake dev/tasks
SILVERSTRIPE DEVELOPMENT TOOLS: Tasks
--------------------------

 * Migrate SiteTree Linking Task: sake dev/tasks/MigrateSiteTreeLinkingTask
 * Database Migrations: sake dev/tasks/MigrationTask
…
```

#### Flush command

```sh
> flush
```

Same effect when using `sake` with `?flush`.

#### Static Command

View available static properties / methods of classes.

Displays static properties and methods:

```sh
> static SilverStripe\Control\Director
```

To display only one of them:

```sh
> static props SilverStripe\Control\Director
…
> static methods SilverStripe\Control\Director
…
```
