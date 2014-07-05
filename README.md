Spress - PHP Static site generator
==============================
[![Build Status](https://travis-ci.org/yosymfony/Spress.png?branch=master)](https://travis-ci.org/yosymfony/Spress)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/yosymfony/Spress/badges/quality-score.png?s=2fde9d65f127dad64c6d3d68f5c47da9b41dfc1b)](https://scrutinizer-ci.com/g/yosymfony/Spress/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77/mini.png)](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77)

Spress is a static site generator building on Silex and Twig and inspired by 
[Jekyll](https://github.com/mojombo/jekyll). See [demo](http://yosymfony.github.io/Spress-example/).

License: [MIT](https://github.com/yosymfony/Spress/blob/master/LICENSE)

Requirements
------------

* Linux, Unix or Mac OS X.
* PHP >= 5.4.
* [Composer](http://getcomposer.org/).

Contributing
------------
When Contributing code to Spress, you must follow its coding standards. Spress follows 
[Symfony's coding style](http://symfony.com/doc/current/contributing/code/standards.html).

Keep in mind a golden rule: **Imitate the existing Spress code**.

### Pull Resquests
* Folk the Spress repository.
* Create a new branch for each feature or improvement.
* Send a pull request from each feature branch to master branch.

### Unit testing

All pull requests must be accompanied by passing unit tests. Spress uses [phpunit](http://phpunit.de/) for testing.

Getting Started
---------------

* Download the last [release](https://github.com/yosymfony/Spress/releases) or clone repository `git clone https://github.com/yosymfony/Spress.git`.
* Get vendors
* Create a blank site.
* Build your site.

### Get vendors

Use Composer to get vendors:

```
$ cd your-path
$ composer.phar update
```

### spress command

Spress command are located in `bin/spress` and you can use this command to create a new site or process your
site.

#### site:new

Create a new site.

`site:new [path="./"] [template="blank"] [--force] [--all]`

* `--force` option force to use the path even though exists and it's not empty.
* `--all` In blank template, creates the complete scaffold.

E.g `$ spress site:new /your-site-dir`

#### site:build

Build your site in your configured destination, typically `_site`. 

`site:build [-s|--source="./"] [--timezone="..."] [--env="dev"] [--drafts] [--safe]`

* `--drafts` To include draft posts in the generated site.
* `--safe` Disable all plugins.

E.g `$ spress site:build -s /your-site-dir` 

##### How to load configuration for production environment:

1. To create `config_prod.yml` with the options that will be overrided in `config.yml`.
2. `$ spress site:build --env=prod`

Unit tests
----------

You can run the unit tests with the following command:
```
$ cd your-path
$ composer.phar install --dev
$ phpunit
```