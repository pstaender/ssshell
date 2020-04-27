# ssshell

## REPL for SilverStripe running on Psy Shell 🚀

### Install

Install the (latest) version in your current project with:

```sh
  $ composer require pstaender/ssshell dev-master
```

Optionally: To use the handy `ssshell` command, install it globally (ensure that .config/composer/vendor/bin is in the path):

```sh
  $ composer global require pstaender/ssshell dev-master
```

### Usage

To use ssshell just type `./vendor/bin/ssshell` (or `ssshell` if its installed globally) in your project folder and you can start using the shell:

```sh
  Psy Shell v0.9.9 (PHP 7.3.6 — cli) by Justin Hileman
  Loading live environment (SilverStripe Framework ^ v4.0.0)
  >>>
```

### Features

#### Namespaces and views of objects and lists

`ssshell` comes with a set of [frequent used namespaces](https://github.com/pstaender/ssshell/blob/master/src/SSShell/NamespacesCommand.php#L17) for convenient REPL handling.

By default all DataObjects, DataLists, ArrayLists and Query objects will be displayed in a human-readable fashion.

As example, creating a SilverStripe User would be:

```sh
  >>> Member::create(['Email' => 'editor', 'Password' => 'password'])->write()
  => 1
  >>> Member::get()->first()
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
  >>> sake dev/build
  Building database SS_test using SilverStripe\ORM\Connect\MySQL 8.0.16


  CREATING DATABASE TABLES

  * File (0 records)
    * CHECK TABLE command disabled for PDO in native mode
  * SiteConfig (1 records)
  …
```

```sh
>>> sake dev/tasks
SILVERSTRIPE DEVELOPMENT TOOLS: Tasks
--------------------------

 * Migrate SiteTree Linking Task: sake dev/tasks/MigrateSiteTreeLinkingTask
 * Database Migrations: sake dev/tasks/MigrationTask
…
```

#### Static Command

View available static properties / methods of classes.

Displays static properties and methods:

```sh
>>> static SilverStripe\Control\Director
```

To display only one of them:

```sh
>>> static props SilverStripe\Control\Director
…
>>> static methods SilverStripe\Control\Director
…
```
