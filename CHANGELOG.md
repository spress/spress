CHANGELOG for 2.x.x
===================

## 2.0.0
* [New] Data-sources: data sources can load site data from certain locations like filesystem or database.
* [New] site structure. See issue #41 (https://github.com/spress/Spress/issues/41).
* [New] Data-writer: can persist a rendered site.
* [New] Collections (issue #43): collections allow you to define a new type of document like page or post.
* [New] These events "spress.before_convert", "spress.after_convert" receive a ContentEvent as an argument.
* [New] List of new events: "spress.before_render_blocks", "spress.after_render_blocks", "spress.before_render_page", "spress.after_render_page".
* [New] Established PHP 5.5 as minimum version.
* [New] List of new configuration attributes: "text_extensions", "attribute_syntax", "preserve_path_title", "collections", "data_sources".
* [Improved] Updated Symfony componentes to 2.7.
* [Improved] Updated Markdown parser (michelf/php-markdown) from Michel Fortin.
* [Improved] Updated built-in theme Spresso to 2.0.
* [Deleted] Methods `initialize` and `getSupportExtension` of ConverterInterface have been deleted.
* [Deleted] TemplateManager class of plugin API.
* [Deleted] List of deleted events: "spress.after_convert_posts", "spress.after_render_pagination", "spress.before_render_pagination".
* [Deleted] List of configuration attributes (config.yml) deleted because they have been marked as deprecated: "baseurl", "paginate", "paginate_path", "limit_posts", "processable_ext", "destination", "posts", "includes", "layouts", "plugins".
