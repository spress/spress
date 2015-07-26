<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\Generator;

use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager;
use Yosymfony\Spress\Core\ContentManager\Generator\Pagination\PaginationGenerator;
use Yosymfony\Spress\Core\Support\SupportFacade;

class GeneratorManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratorManager()
    {
        $generator = new PaginationGenerator(new SupportFacade());

        $gm = new GeneratorManager();
        $gm->addGenerator('paginator', $generator);

        $this->assertEquals(1, $gm->countGenerator());
        $this->assertTrue($gm->hasGenerator('paginator'));
        $this->assertFalse($gm->hasGenerator('paginator-not-registered'));
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\Generator\GeneratorInterface', $gm->getGenerator('paginator'));

        $gm->setGenerator('paginator-2', $generator);

        $this->assertEquals(2, $gm->countGenerator());
        $this->assertTrue($gm->hasGenerator('paginator-2'));

        $gm->removeGenerator('paginator-2');

        $this->assertEquals(1, $gm->countGenerator());
        $this->assertFalse($gm->hasGenerator('paginator-2'));

        $gm->clearGenerator();

        $this->assertEquals(0, $gm->countGenerator());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddWithSomeName()
    {
        $generator = new PaginationGenerator(new SupportFacade());

        $gm = new GeneratorManager();
        $gm->addGenerator('paginator', $generator);
        $gm->addGenerator('paginator', $generator);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGeneratorNotFound()
    {
        $generator = new PaginationGenerator(new SupportFacade());

        $gm = new GeneratorManager();
        $gm->addGenerator('paginator', $generator);
        $gm->getGenerator('paginator-1');
    }
}
