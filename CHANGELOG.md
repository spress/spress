CHANGELOG for 2.x
=================
## 2.1.0
* [New] Support for calling to an existing command inside a command plugin. See feature #77.
* [New] Sort items of a collection. See feature #67.
* [New] Support to extends TwigRenderizer with tags. See PR #65.
* [New] Each item of a sorted collection has `next` and `prior` relationships. At compiled time, you have access through `page.relationships.next` and `page.relationships.prior`. See feature #69.
* [New] Relationship collection for items. A new class has been added: `RelationshipCollection`. A new method getRelationshipCollection has been added to `ItemIterface`.
* [New] `MirrorConverter` class has been replaced by `MapConverter`. See feature #73. This fix the ticket #28 "Support .twig extention". 
* [New] Added `ItemCollection` class to Core support classes.
* [New] Added `getCollections` method to `CollectionManager` class.
* [New] Added `clearConverter` and `countConverter` methods in `ConverterManager` class.
* [Improved] Improved permalinks customizations. See PR #64.
* [Improved] Eliminated unnecessary calls to `setItem` method of `SiteAttribute` class in `ContentManager` class.
* [Improved] The methods for managing the collection of plugins in `PluginManager` class have been moved to a `Collection` class.
* [Improved] The methods for managing the collection of collection-item in `CollectionManager` class have been moved to a `Collection` class.
* [Fixed] Fixed the path available at `page.path` variable. Prior to this version, this variable contains the relative path to `src/content/` but with the filename extension changed by the Converter. Now, the original filename extension isn't altered. A new path snapshot has been created in `ItemIterface`.

## 2.0.2 (2016-01-16)
* [New] `PluginTester` class has been added to the core for testing plugins easily.
* [Fixed] Fixed an issue with the content retrieved by "after_render_page" event.
* [Fixed] A constant name of `ItemInterface` has been changed: `SNAPSHOT_AFTER_PAGE` -> `SNAPSHOT_AFTER_RENDER_PAGE`.

## 2.0.1 (2016-01-09)
* [Improved] Normalized the directory separator to '/' irrespective of the operating system.
* [Fixed] Fixed the file's extension `twig.html` in configuration files.
* [Fixed] Fixed the exception "A previous item exists with the same id" thrown by Taxonomy generator due to a key sensitive issue. A normalize method has been added. e.g: "news", "NEWS", " News " are the same term: "news".
* [Fixed] Fixed the namespace of `AttributeValueException` at `PaginationGenerator` class.

## 2.0.0 (2016-01-02)
* [New] `ConsoleIO` class uses Symfony CLI styles.
* [Improved] 100% tests passed in HHVM.
* [Fixed] Renamed the package name `yosymfony/spress-installer` to `spress/spress-installer`.
* [Fixed] Fixed a race condition with the `url` attribute of items before dispatch `spress.before_render_blocks` event.
* [Fixed] Questions made by commands are using `ConsoleIO` methods.
* [Fixed] Fixed a bug with the built-in server by which a relative URL that contains a dot inside the trailing component throws a 404 not found error. e.g: `/doc/2.0`.
* [Fixed] Added a default value for `$fallback` argument of `askHiddenResponseAndValidate` and `askAndHideAnswer` methods in `IOInterface`.

## 2.0.0-rc (2015-12-07)
* [New] Added MemoryDataSource, a datasource for generating dynamic content.
* [New] Support to sort items at `PaginationGenerator` with attributes `sort_by` and `sort_type. See #61.
* [New] Added `getGeneratorManager` method to `EnvironmentEvent` for managing generators at plugins.
* [Improved] Improved the way of generating the classname in PluginGenerator.
* [Improved] Minor changes over output styles.
* [Improved] Improved HttpServer with support to load internal resources (used with error page). Added a new hook: `handleOnAfterRequestFunction`. Bootstrap file has been included for using with internal pages like error page.
* [Improved] Minor improvements over the Spress application output.
* [Fixed] Now, `slug` method transform dot characters into dash characters.
* [Fixed] Fixed lifecycle: render phase starts after converter phase has been finished for all items.
* [Fixed] Changed the method `remove` by `removeCollection` in CollectionManager class.
* [Fixed] `PermalinkGenerator` adds an initial slash if the permalink doesn't start with it.
* [Fixed] `MissingAttributeException` and `AttributeValueException` has been moved to `Core\ContentManager\Exception.
* [Fixed] `ConsoleIO` passed to `spress.io` key (DI container) when `SiteBuildCommand` builds `Spress instance.
* [Fixed] Updated `spress-installer` version to ~2.0 at `composer.json.twig`.
* [Fixed] Fixed the message of the exception threw when a previous item exists.
* [Deleted] `ConfigValueException` has been deleted.

## 2.0.0-beta (2015-10-15)
* [New] Added a new converter for Markdown: ParsedownConverter. This converter is based on Parsedown by Emanuil Rusev. See http://parsedown.org/. Deals with issue #40.
* [New] Added command plugins: a new kind of plugins witch provides subcommand for `spress` executable. See #56.
* [New] Added `self-update` command with an alias `selfupdate` for keeping Spress up to date. See #60.
* [New] Taxonomy generator for grouping content around a set of terms. See #57.
* [New] Modified RenderizerInterface for throwing a `Yosymfony\Spress\Core\ContentManager\Renderizer\Exception\RenderException` if an error occurred during redering the content. Method affected: `renderBlocks` and `renderPage`.
* [New] Added a new special attributte `avoid_renderizer` for avoiding the renderizer phase over an item.
* [Improved] Additional autoload only be processed if exists a `composer.json` file in the root of the site folder.
* [Fixed] The separator for tags and categories of `new:post` command has been changed from space to comma. See issue #51.
* [Fixed] New template for spress plugin scaffold (`new:plugin` command) - fixed for 2.0 release. See issue #55.
* [Fixed] The `setUp` method of `FilesystemDataWriter removes the whole content of the output dir but VCS files. This means that `site:build` command doesn't remove the VCS files.
* [Deleted] Deleted the `site:new` alias for command `new:site`.

## 2.0.0-alpha (2015-08-12)
* [New] Data-sources: (issue #46) data sources can load site data from certain locations like filesystem or database.
* [New] Site structure (issue #41).
* [New] Data-writer (issue #44): The DataWriter's responsibility is to persist the content of the items.
* [New] Collections (issue #43): collections allow you to define a new type of document like page or post.
* [New] Generators (issue #45): Generators are used for generating new items of content.
* [New] These events `spress.before_convert`, `spress.after_convert` receive a ContentEvent as an argument.
* [New] Renderizer (issue #48): Renderizer are responsible for formatting content.
* [New] List of new events: `spress.before_render_blocks`, `spress.after_render_blocks`, `spress.before_render_page`, `spress.after_render_page`. See #49.
* [New] Established PHP 5.5 as minimum version (see #42).
* [New] List of new configuration attributes: `text_extensions`, `attribute_syntax`, `preserve_path_title`, `collections`, `data_sources`.
* [Improved] Updated Symfony componentes to 2.7.
* [Improved] Updated Markdown parser (michelf/php-markdown) from Michel Fortin.
* [Improved] Updated built-in theme Spresso to 2.0.
* [Deleted] Methods `initialize` and `getSupportExtension` of ConverterInterface have been deleted.
* [Deleted] TemplateManager class of plugin API.
* [Deleted] EnviromentEvent class.
* [Deleted] List of deleted events: `spress.after_convert_posts`, `spress.before_render_pagination`, `spress.after_render_pagination `, `spress.before_render`, `spress.after_render`. See #49.
* [Deleted] List of configuration attributes (config.yml) deleted because they have been marked as deprecated: `baseurl`, `paginate`, `paginate_path`, `limit_posts`, `processable_ext`, `destination`, `posts`, `includes`, `layouts`, `plugins`.
