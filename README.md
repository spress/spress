Spress - PHP Static site generator
==============================
[![Build Status](https://travis-ci.org/spress/Spress.svg?branch=2.0)](https://travis-ci.org/spress/Spress)
[![Build status](https://ci.appveyor.com/api/projects/status/mjsjdgauj7ks3ogn?svg=true)](https://ci.appveyor.com/project/yosymfony/spress)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spress/Spress/badges/quality-score.png?b=2.0)](https://scrutinizer-ci.com/g/spress/Spress/?branch=2.0)
[![Code Coverage](https://scrutinizer-ci.com/g/spress/Spress/badges/coverage.png?b=2.0)](https://scrutinizer-ci.com/g/spress/Spress/?branch=2.0)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77/mini.png)](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77)

Spress is a static site generator built with Symfony components. See [demo](http://yosymfony.github.io/Spress-example/).

License: [MIT](https://github.com/spress/Spress/blob/master/LICENSE).

Requirements
------------

* Linux, Unix or Mac OS X.
* PHP >= 5.5.
* [Composer](http://getcomposer.org/).

Community
---------

* Documentation: [spress.yosymfony.com](http://spress.yosymfony.com/docs/).
* Mention [@spress_cms](https://twitter.com/spress_cms) on Twitter.

Discuss and share your opinions in Gitter chat:

[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/spress/Spress?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

### Contributing

When Contributing code to Spress, you must follow its coding standards. Spress follows 
[PSR-2 coding style](http://www.php-fig.org/psr/psr-2/).

Keep in mind a golden rule: **Imitate the existing Spress code**.

#### Pull Resquests
* Fork the Spress repository.
* Create a new branch for each feature or improvement.
* Send a pull request from each feature branch to master branch or appropriated.

#### Unit testing

All pull requests must be accompanied by passing unit tests. Spress uses [phpunit](http://phpunit.de/) for testing.

Getting Started
---------------

* Download the last [release](https://github.com/spress/Spress/releases) or clone repository `git clone https://github.com/spress/Spress.git`.
* Get vendors
* Create a blank site.
* Build your site.

### Get vendors

Use Composer to get vendors:

```bash
$ cd your-path
$ composer.phar update
```

### spress command

Spress command are located in `bin/spress` and you can use this command to create a new site or process your
site.

#### site:build

Build your site in your configured destination, typically `_site`. 

```bash
site:Build 	[-s|--source="./"] [--timezone="..."] [--env="dev"]
			[--server] [--watch] [--drafts]
			[--safe]
```

* `--server` The built-in server will run.
* `--watch` Watch for changes and regenerate your site automatically.
* `--drafts` To include draft posts in the generated site.
* `--safe` Disable all plugins.

E.g `$ spress site:build -s /your-site-dir`

##### How to load configuration for production environment:

1. To create `config_prod.yml` with the options that will be overrided in `config.yml`.
2. `$ spress site:build --env=prod`

#### Scaffolding

##### new:site

Create a new site.

`new:site [path="./"] [template="blank"] [--force] [--all]`

* `--force` option force to use the path even though exists and it's not empty.
* `--all` In blank template, creates the complete scaffold.

E.g `$ spress new:site /your-site-dir`

##### new:post

The `new:post` command helps you generates new posts.
By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction.

```bash
new:post 	[--title="..."] [--layout="default"] [--date="..."]
			[--tags="..."] [--categories="..."]`
```
* `--title`: The title of the post.
* `--layout`: The layout of the post.
* `--date`: The date assigned to the post.
* `--tags`: Comma separated list of tags.
* `--categories`: Comma separated list of categories.

##### new:plugin

The `new:plugin` command helps you generates new plugins.
By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction.

```bash
new:plugin 	[--name="..."] [--command-name="..."] [--command-description="..."]
			[--command-help="..."] [--author="..."] [--email="..."]
			[--description="..."] [--license="MIT"]
```

* `--name`: The name of the plugins should follow the pattern `vendor-name/plugin-name`.
* `--command-name`: In case of you want to create a command plugin this is the name of the command.
* `--command-description`: The description of command in case of command plugin.
* `--command-help`: The description of command in case of command plugin.
* `--author`: The author of the plugin.
* `--email`: The Email of the author.
* `--description`: The description of your plugin.
* `--license`: The license under you publish your plugin. MIT by default.

##### self-update

`self-update` or `selfupdate` command replace your `spress.phar` by the
latest version from spress.yosymfony.com.

How to make spress.phar
-----------------------
We are using [Box Project](http://box-project.github.io/box2/) for generating the `.phar` file.

You may download Box:

```bash 
$ curl -LSs https://box-project.github.io/box2/installer.php | php
```
Next:

```
$ cd spress-folder
$ box build
```

Unit tests
----------

You can run the unit tests with the following command:
```bash
$ cd your-path
$ composer.phar install
$ phpunit
```
