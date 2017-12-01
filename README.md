Spress - PHP Static site generator
==============================
[![Build Status](https://travis-ci.org/spress/spress.svg?branch=master)](https://travis-ci.org/spress/spress)
[![Build status](https://ci.appveyor.com/api/projects/status/mjsjdgauj7ks3ogn/branch/master?svg=true)](https://ci.appveyor.com/project/yosymfony/spress/branch/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spress/Spress/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spress/Spress/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/spress/spress/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/spress/spress/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77/mini.png)](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77)

Spress is a static site generator built with Symfony components.

License: [MIT](https://github.com/spress/spress/blob/master/LICENSE).

Requirements
------------

* Linux, Unix, Mac OS X or Windows.
* PHP >= 5.6.
* [Composer](http://getcomposer.org/).

Community
---------

* Documentation: [spress.yosymfony.com](http://spress.yosymfony.com/docs/).
* Mention [@spress_cms](https://twitter.com/spress_cms) on Twitter.

Discuss and share your opinions in Gitter chat:

[![Gitter](https://badges.gitter.im/spress/Spress.svg)](https://gitter.im/spress/Spress?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

### Contributing

When Contributing code to Spress, you must follow its coding standards. Spress follows
[PSR-2 coding style](http://www.php-fig.org/psr/psr-2/).

Keep in mind a golden rule: **Imitate the existing Spress code**.

#### Pull Resquests
* Fork the Spress repository.
* Create a new branch for each feature or improvement.
* New features: Send a pull request from each feature branch to master branch.
* Fixes: Send a pull request to 2.1 branch.

#### Unit testing

All pull requests must be accompanied by passing unit tests. Spress uses [phpunit](http://phpunit.de/) for testing.

How to make spress.phar
-----------------------
We are using [Box Project](http://box-project.github.io/box2/) for generating the `.phar` file.

You may download Box:

```bash
$ curl -LSs https://box-project.github.io/box2/installer.php | php
```
Next:

```bash
$ cd spress-folder
$ box build
```

Unit tests
----------

Spress uses [PHPUnit](https://phpunit.de) >= 5.7. You can run the unit tests with
the following command:

```bash
$ cd your-path
$ phpunit
```
