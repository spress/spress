CHANGELOG for 2.x.x
===================

## 2.0.0
* [New] datasources: data sources can load site data from certain locations like filesystem or database.
* [New] site structure. See issue #41 (https://github.com/spress/Spress/issues/41).
* [New] datawriter: can persist a rendered site.
* [New] collections: collections allow you to define a new type of document like page or post.
* [New] these events "spress.before_convert", "spress.after_convert" receive a ContentEvent as an argument.
* [New] events: "spress.before_render_blocks", "spress.after_render_blocks", "spress.before_render_page", "spress.after_render_page".
* [New] established PHP 5.5 as minimum version.
* [Improved] updated Symfony componentes to 2.7.
* [Improved] updated Markdown parser (michelf/php-markdown) from Michel Fortin.
* [Deleted] methods initialize and getSupportExtension of ConverterInterface.
* [Deleted] TemplateManager class of plugin API.
* [Deleted] events: "spress.after_convert_posts", "spress.after_render_pagination", "spress.before_render_pagination".
* [Deleted] configuration attributes: "baseurl", "paginate", "paginate_path", "limit_posts", "processable_ext", "destination", "posts", "includes", "layouts", "plugins".
