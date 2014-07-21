Spress Core - PHP Static site generator
=======================================

[![Build Status](https://travis-ci.org/spress/Spress.png?branch=master)](https://travis-ci.org/spress/Spress)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spress/Spress/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spress/Spress/?branch=master)
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

## Options
Options are passed as a key-value array.

* `spress.io`: An implementions of `Yosymfony\Spress\Core\IO\IOInterface` for to interact with the user. The default implementation is `Yosymfony\Spress\Core\IO\NullIO`.
* `spress.paths`: files and path uses by Spress. The standard sub-keys:
  * `config`: path to the global configuration file in case you want to override the default global configuration of the core.
  * `config.file`: Configuration filename. By default: `config.yml`.
  * `config.file_env`: Template for environment configuration file. By default `config_:env.yml`.
  * 

Example:
```
$io = new BufferIO();

$options = [
    'spress.paths' => [
        'config'    => '/my-app/config/',
    ],
    'spress.io' => $io,
];
```

Backward compatibility
----------------------
For backward compatibility events of plugins and plugins class base are located in namespace `Yosymfony\Spress\Plugin`.
This situation end with Spress 2.0.0.