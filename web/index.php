<?php
require_once __dir__.'../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Simple skeleton for Silex
 * 
 * Run in PHP >=5.4: $ php -S localhost:8080 -t web web/index.php
 * Assume that your webroot are in web and your app file is index.php.
 */

$app = new \YoSymfony\Spress\Application();
$app['debug'] = true;

/**
 * Simple controller
 */
$app->get('/', function (Request $request, $name)
{ 
    return "hello.";
}); 

/**
 * Manage errors:
 */
$app->error(function(\Exception $e, $code) use($app) 
{
    if($app['debug'])
    {
        return;
    }
    
    return new Response('Something is wrong');
});

$app->run();
