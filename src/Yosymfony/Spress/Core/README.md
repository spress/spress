Spress Core - PHP Static site generator
=======================================

[![Build Status](https://travis-ci.org/yosymfony/Spress.png?branch=master)](https://travis-ci.org/yosymfony/Spress)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/yosymfony/Spress/badges/quality-score.png?s=2fde9d65f127dad64c6d3d68f5c47da9b41dfc1b)](https://scrutinizer-ci.com/g/yosymfony/Spress/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77/mini.png)](https://insight.sensiolabs.com/projects/1ea79d8e-894d-4cf5-8f64-c941376b3f77)

Spress is a static site generator building on top of Symfony components and Twig as template engine. This repository is the
core of Spress application. You can to integrate Spress Core in your solutions using Composer.

License: [MIT](https://github.com/yosymfony/Spress/blob/master/LICENSE).

How to use?
-----------
The entry-point class is `Yosymfony\Spress\Core\Application`. The below example point out how to use:

```
use Yosymfony\Spress\Core\Application;

class MyClass
{
    public function parseSite()
    {
        $options = [];
        $app = new Application($options);
        $app->parse('/path-to-my-spress-site/');
    }
}
```