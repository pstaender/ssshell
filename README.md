# ssshell is a REPL for SilverStripe

## Like tinker for Laravel - runs on top of Psy Shell 🚀

### Install

Go to your SilverStripe (v4+) project root, then type:

```sh
  $ composer require pstaender/ssshell dev-master
```

### Usage

To use ssshell just type `./vendor/bin/sshell` in your project folder, and you'll see some welcome messages like

```sh
  Psy Shell v0.9.9 (PHP 7.3.6 — cli) by Justin Hileman
  Welcome to SilverStripeShell v0.0.1 by Philipp Staender
  >>>
```

### Features

`ssshell` comes with a set of [frequent used namespaces](https://github.com/pstaender/ssshell/blob/master/src/SSShell/SilverStripeShell.php#L12) for convenient REPL handling.

As example, creating a SilverStripe User would be:

```sh
  >>> Member::create(['Email' => 'editor', 'Password' => 'supersecret'])->write()
  => 2
  >>> Member::get()->first()
  => SilverStripe\Security\Member {#3229
      +ClassName: "SilverStripe\Security\Member",
      +LastEdited: "2019-07-01 11:34:54",
      +Created: "2019-07-01 11:34:54",
      +Email: "admin",
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

#### Sake is included

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
