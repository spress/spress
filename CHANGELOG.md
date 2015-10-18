CHANGELOG for 2.0.x
===================
## 2.0.0-rc

## 2.0.0-beta (2015-1015)
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
