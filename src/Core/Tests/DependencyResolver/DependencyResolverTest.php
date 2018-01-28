<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DependencyResolver;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DependencyResolver\DependencyResolver;

class DependencyResolverTest extends TestCase
{
    public function testGetIdsDependingOnMustReturnIdsAreDependentOn()
    {
        $dependencyResolver = new DependencyResolver();
        $dependencyResolver->registerDependency('layouts/default.html', 'content/index.html');

        $this->assertEquals([
            'content/index.html',
            'layouts/default.html',
        ], $dependencyResolver->getIdsDependingOn('layouts/default.html'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Circular reference detected: "content/index.html" -> "layouts/default.html".
     */
    public function testGetIdsDependingOnMustFailWhenThereIsACircularReference()
    {
        $dependencyResolver = new DependencyResolver();
        $dependencyResolver->registerDependency('layouts/default.html', 'content/index.html');
        $dependencyResolver->registerDependency('content/index.html', 'layouts/default.html');

        $dependencyResolver->getIdsDependingOn('layouts/default.html');
    }

    public function testGetAllDependenciesMustReturnAllRegisteredDependencies()
    {
        $dependencyResolver = new DependencyResolver();
        $dependencyResolver->registerDependency('layouts/default.html', 'content/index.html');

        $this->assertEquals([
            'layouts/default.html' => [
                'content/index.html',
            ],
            'content/index.html' => [],
        ], $dependencyResolver->getAllDependencies());
    }

    public function testClearMustClearsAllTheRegisteredDependencies()
    {
        $dependencyResolver = new DependencyResolver();
        $dependencyResolver->registerDependency('layouts/default.html', 'content/index.html');

        $dependencyResolver->clear();

        $this->assertEquals([], $dependencyResolver->getAllDependencies());
    }
}
