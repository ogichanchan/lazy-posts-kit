=== Lazy Posts Kit ===
Contributors: ogichanchan
Tags: wordpress, plugin, tool, shortcode, posts, custom post types, listing, utility, performance
Requires at least: 6.2
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Lazy Posts Kit is a simple, efficient, and PHP-only WordPress utility that allows you to display customizable lists of posts, pages, or any public custom post type using a straightforward shortcode.

Key Features:
*   **Easy Shortcode Integration**: Use `[lazy_posts_kit]` anywhere on your posts, pages, or widgets to display a list of recent content.
*   **Flexible Display Options**: Control how many posts to show, which post types to include, ordering (date, title, random, etc.), and the display of thumbnails, excerpts, and publication dates.
*   **Admin Settings**: A dedicated settings page under "Settings" allows you to set global defaults for the shortcode, simplifying its usage across your site.
*   **Shortcode Attribute Overrides**: All default settings can be easily overridden on a per-shortcode basis using attributes, giving you fine-grained control for each instance.
*   **Lightweight & Efficient**: Built with a focus on simplicity and performance, utilizing WordPress's `WP_Query` efficiently and including minimal inline styles.
*   **Customizable Title Tag**: Choose the HTML tag (e.g., h1, h2, h3, p, div) for your post titles within the list.

This plugin is designed to be a "kit" for quick and simple post listings without unnecessary bloat.

This plugin is open source. Report bugs at: https://github.com/ogichanchan/lazy-posts-kit

== Installation ==
1. Upload the `lazy-posts-kit` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure default settings under `Settings > Lazy Posts Kit` in your WordPress admin.
4. Use the `[lazy_posts_kit]` shortcode in your posts, pages, or widgets.

== Changelog ==
= 1.0.0 =
* Initial release.
    * Implemented `[lazy_posts_kit]` shortcode.
    * Added admin settings page for default options (posts per page, post types, thumbnail, excerpt, date, title tag).
    * Shortcode attributes for overriding default settings.
    * Basic inline frontend and admin styling.