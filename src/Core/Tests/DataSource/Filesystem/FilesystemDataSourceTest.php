<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DataSource\Filesystem;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Core\DataSource\Filesystem\FilesystemDataSource;
use Yosymfony\Spress\Core\DataSource\ItemInterface;

class FilesystemDataSourceTest extends TestCase
{
    protected $sourcePath;
    protected $extraPagesPath;
    protected $textExtensions;

    public function setUp()
    {
        $this->sourcePath = sys_get_temp_dir().'/spress-tests';

        $fs = new FileSystem();
        $fs->mirror(__dir__.'/../../fixtures/project/src', $this->sourcePath);

        $this->extraPagesPath = __dir__.'/../../fixtures/extra_pages';
        $this->textExtensions = ['html.twig', 'html', 'md', 'css'];
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->sourcePath);
    }

    public function testGetItemsMustReturnAnArrayWithTheItemIdAsKey()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertTrue(count($items) > 0);

        foreach ($items as $key => $item) {
            $this->assertEquals($item->getId(), $key);
        }
    }

    public function testGetLayoutsMustReturnAnArrayWithTheItemIdAsKey()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertTrue(count($layouts) > 0);

        foreach ($layouts as $key => $layout) {
            $this->assertEquals($layout->getId(), $key);
        }
    }

    public function testGetIncludesMustReturnAnArrayWithTheItemIdAsKey()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertTrue(count($includes) > 0);

        foreach ($includes as $key => $include) {
            $this->assertEquals($include->getId(), $key);
        }
    }

    public function testGetItemsMustReturnTheItemsOfASite()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertCount(15, $items);
        $this->assertArrayHasKey('index.html', $items);
        $this->assertArrayHasKey('LICENSE', $items);
        $this->assertArrayHasKey('about/index.html', $items);
        $this->assertArrayHasKey('about/me/index.html', $items);
        $this->assertArrayHasKey('pages/index.html', $items);
        $this->assertArrayHasKey('projects/index.md', $items);
        $this->assertArrayHasKey('robots.txt', $items);
        $this->assertArrayHasKey('sitemap.xml', $items);
        $this->assertArrayHasKey('posts/2013-08-12-post-example-1.md', $items);
        $this->assertArrayHasKey('posts/2013-08-12-post-example-2.mkd', $items);
        $this->assertArrayHasKey('posts/2016-02-02-spress-2.1.1-released.md', $items);
        $this->assertArrayHasKey('posts/books/2013-08-11-best-book.md', $items);
        $this->assertArrayHasKey('posts/books/2013-09-19-new-book.md', $items);
        $this->assertArrayHasKey('assets/style.css', $items);
        $this->assertArrayHasKey('.htaccess', $items);
    }

    public function testGetLayoutsMustReturnTheLayoutsOfASite()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertCount(1, $layouts);
        $this->assertArrayHasKey('default', $layouts);
    }

    public function testGetLayoutsMustReturnALayoutItemWithPathFilenameAsIdWhenFilenameOneIsUnique()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layout = $fsDataSource->getLayouts()['default'];

        $this->assertEquals('default', $layout->getId());
    }

    public function testGetLayoutsMustReturnALayoutItemWithPathFilenameExtensionAsIdWhenFilenameIsNotUnique()
    {
        $fs = new FileSystem();
        $fs->dumpFile($this->sourcePath.'/layouts/default.html.twig', '');

        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertCount(2, $layouts);
        $this->assertArrayHasKey('default', $layouts);
        $this->assertArrayHasKey('default.html', $layouts);
    }

    public function testGetIncludesMustReturnTheIncludesOfASite()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertCount(1, $includes);
        $this->assertArrayHasKey('test.html', $includes);
    }

    public function testGetLayoutsMustReturnsAnArrayOfItemInterface()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertTrue(is_array($layouts), 'getLayouts must return an array');
        $this->assertContainsOnlyInstancesOf(
            ItemInterface::class,
            $layouts,
            'getLayouts can only returns elements of ItemInterface type'
        );
    }

    public function testGetIncludesMustReturnsAnArrayOfItemInterface()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertTrue(is_array($includes), 'getIncludes must return an array');
        $this->assertContainsOnlyInstancesOf(
            ItemInterface::class,
            $includes,
            'getIncludes can only returns elements of ItemInterface type'
        );
    }

    public function testGetItemsMustReturnsAnArrayOfItemInterface()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertTrue(is_array($items), 'getItems must return an array');
        $this->assertContainsOnlyInstancesOf(
            ItemInterface::class,
            $items,
            'getItems can only returns elements of ItemInterface type'
        );
    }

    public function testGetTypeOfAnIncludeItemMustBeInclude()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertEquals(
            'include',
            $includes['test.html']->getType(),
            'The type of an include item must be "include"'
        );
    }

    public function testGetContentOfAnIncludeItemMustBeRight()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertRegExp(
            '/Include test/',
            $includes['test.html']->getContent(),
            'The content of the include item must contains the text "Include test"'
        );
    }

    public function testGetTypeOfAnLayoutItemMustBeLayout()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertEquals(
            'layout',
            $layouts['default']->getType(),
            'The type of a layout item must be "layout"'
        );
    }

    public function testGetContentOfALayoutItemMustBeRight()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertRegExp(
            '/Welcome to my site/',
            $layouts['default']->getContent(),
            'The content of the layout item must contains the text "Welcome to my site"'
        );
    }

    public function testGetBinaryOfAnItemMustReturnTrueIfThereIsNotFilenameExtension()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertTrue(
            $items['LICENSE']->isBinary(),
            'An item without a filename extension must be treated as a binary item'
        );
    }

    public function testGetBinaryOfAnItemMustReturnFalseIfTheFilenameExtensionBelongsToTextExtensions()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertFalse(
            $items['posts/2013-08-12-post-example-1.md']->isBinary(),
            'An item with a filename extension included in the text extension list must be treated as a text item'
        );
    }

    public function testGetBinaryOfAnItemMustReturnTrueIfTheFilenameExtensionDoesNotBelongsToTextExtensions()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => ['html'],
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertTrue(
            $items['posts/2013-08-12-post-example-1.md']->isBinary(),
            'An item with a filename extension not included in the text extension list must be treated as a binary item'
        );
    }

    public function testGetPathOfABinaryItemMustHasSourceAndRelativePaths()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();
        $item = $items['LICENSE'];

        $this->assertTrue(
            strlen($item->getPath('source')) > 0,
            'A binary item must have a source path'
        );
        $this->assertTrue(
            strlen($item->getPath('relative')) > 0,
            'A binary item must have a relative path'
        );
    }

    public function testGetPathOfATextItemMustHasOnlyARelativePaths()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();
        $item = $items['index.html'];

        $this->assertTrue(
            strlen($item->getPath('relative')) > 0,
            'A text item must have a relative path'
        );
        $this->assertFalse(
            strlen($item->getPath('source')) > 0,
            'A text item must not have a source path'
        );
    }

    public function testGetAttributesMustReturnsTheBasicAttributes()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();
        $item = $items['index.html'];
        $itemAttributes = $item->getAttributes();

        $this->assertArrayHasKey('mtime', $itemAttributes);
        $this->assertArrayHasKey('filename', $itemAttributes);
        $this->assertArrayHasKey('extension', $itemAttributes);
    }

    public function testGetLayoutsMustGivesPreferenceSiteLayoutsOverThemeLayoutsWhenThereIsAEnabledTheme()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'theme_name' => 'theme01',
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertCount(2, $layouts);
        $this->assertRegExp('/Welcome to my site/', $layouts['default']->getContent());
    }

    public function testGetIncludesMustGivesPreferenceSiteIncludesOverThemeIncludesWhenThereIsAEnabledTheme()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'theme_name' => 'theme01',
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertCount(2, $includes);
        $this->assertRegExp('/Include test/', $includes['test.html']->getContent());
    }

    public function testGetItemsMustGivesPreferenceSiteAssetsOverThemeAssetsWhenThereIsAEnabledTheme()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'theme_name' => 'theme01',
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();
        $this->assertRegExp('/styles of the site/', $items['assets/style.css']->getContent());
    }

    public function testGetItemsMustReturnThemeAssetsIfItDoesNotExistsInTheSiteAssetsWhenThereIsAEnabledTheme()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'theme_name' => 'theme01',
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertArrayHasKey('assets/extra.css', $items);
        $this->assertRegExp('/extra styles of the theme/', $items['assets/extra.css']->getContent());
    }

    public function testGetIncludesMustReturnThemeIncludesIfItDoesNotExistsInTheSiteIncludesWhenThereIsAEnabledTheme()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'theme_name' => 'theme01',
        ]);

        $fsDataSource->load();
        $includes = $fsDataSource->getIncludes();

        $this->assertCount(2, $includes);
        $this->assertRegExp('/Include theme 01 - test2/', $includes['test2.html']->getContent());
    }

    public function testGetLayoutsMustReturnThemeLayoutsIfItDoesNotExistsInTheSiteLayoutsWhenThereIsAEnabledTheme()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'theme_name' => 'theme01',
        ]);

        $fsDataSource->load();
        $layouts = $fsDataSource->getLayouts();

        $this->assertCount(2, $layouts);
        $this->assertRegExp('/Theme 01 layout/', $layouts['page']->getContent());
    }

    public function testGetItemsMustReturnAnExtraItemWhenIncludeOptionIsSetWithAFile()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'include' => [$this->extraPagesPath.'/extra-page1.html'],
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertCount(16, $items);
        $this->assertArrayHasKey('extra-page1.html', $items);
    }

    public function testGetItemsMustReturnDotItemsWhenThereAreDotFiles()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertArrayHasKey('.htaccess', $items);
    }

    public function testGetItemsMustNotReturnItemsInVCSFolders()
    {
        $gitDir = $this->sourcePath.'/.git';
        $fs = new FileSystem();
        $fs->dumpFile($gitDir.'/HEAD', '');
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $this->assertArrayNotHasKey('HEAD', $items);
    }

    public function testGetItemsMustReturnItemsOfIncludedFolderWhenIncludeOptionIsUsedWithAFolder()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'include' => [$this->extraPagesPath],
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(17, $fsDataSource->getItems());
    }

    public function testGetItemMustNotContainExcludedItemWhenExcludeOptionIsSetWithAFile()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'exclude' => ['robots.txt'],
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(14, $fsDataSource->getItems(), 'Failed to exclude the file.');
    }

    public function testGetItemMustNotContainTheItemsOfAExcludedFolderWhenExcludeOptionIsSetWithAFolder()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'exclude' => ['about'],
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(13, $fsDataSource->getItems(), 'Failed to exclude the elements of about folder.');
    }

    public function testGetItemMustReturnItemsWithAvoidRenderizerAttributeWhenTheybelongToAFolderDeclaredAtAvoidRenderizerPathOption()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'avoid_renderizer_path' => ['projects'],
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $itemAttributes = $items['projects/index.md']->getAttributes();
        $this->assertArrayHasKey('avoid_renderizer', $itemAttributes);
    }

    public function testGetItemMustReturnItemsWithoutAvoidRenderizerAttributeWhenTheyDoesNotbelongToAFolderDeclaredAtAvoidRenderizerPathOption()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'avoid_renderizer_path' => ['projects'],
        ]);

        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $itemAttributes = $items['posts/books/2013-08-11-best-book.md']->getAttributes();
        $this->assertArrayNotHasKey('avoid_renderizer', $itemAttributes);
    }

    public function testGetItemMustReturnItemsWithAvoidRenderizerAttributeWhenTheyHaveAnFilenameExtensionDeclaredAtAvoidRenderizerExtensionOption()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'avoid_renderizer_extension' => ['mkd'],
        ]);
        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $itemAttributes = $items['posts/2013-08-12-post-example-2.mkd']->getAttributes();
        $this->assertArrayHasKey('avoid_renderizer', $itemAttributes);
    }

    public function testGetItemMustReturnItemsWithoutAvoidRenderizerAttributeWhenTheyHaveAnFilenameExtensionNotDeclaredAtAvoidRenderizerExtensionOption()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'text_extensions' => $this->textExtensions,
            'avoid_renderizer_extension' => ['mkd'],
        ]);
        $fsDataSource->load();
        $items = $fsDataSource->getItems();

        $itemAttributes = $items['posts/books/2013-08-11-best-book.md']->getAttributes();
        $this->assertArrayNotHasKey('avoid_renderizer', $itemAttributes);
    }

    /**
     * @expectedException Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException
     */
    public function testLoadMethodMustThrowAMissingAttributeExceptionWhenTheClassDoesNotHaveConfigParams()
    {
        $fsDataSource = new FilesystemDataSource([]);
        $fsDataSource->load();
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException
     */
    public function testLoadMethodMustThrowAMissingAttributeExceptionWhenTheClassDoesNotHaveSetTextExtensionsOption()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
     */
    public function testLoadMethodMustThrowAnAttributeValueExceptionWhenTheClassHasAnInvalidSourceRootParam()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => [],
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
     */
    public function testLoadMethodMustThrowAnAttributeValueExceptionWhenTheClassHasAnInvalidIncludeParam()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'include' => './',
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
     */
    public function testLoadMethodMustThrowAnAttributeValueExceptionWhenTheClassHasAnInvalidExcludeParam()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root' => $this->sourcePath,
            'exclude' => './',
            'text_extensions' => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }
}
