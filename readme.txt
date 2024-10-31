=== Restrictly - Restrict Content Plugin ===
Contributors: Catapult_Themes
Donate Link: https://www.paypal.me/catapultthemes
Tags: restrict content, member only, restrict access, protect content, logged in only
Requires at least: 4.7
Tested up to: 4.7.5
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Protect your content by allowing only logged in users to access certain pages or posts

== Description ==
Restrictly is a content restriction plugin. It doesn't try to be complicated - there's only a very few settings:

* Decide whether to protect all content or allow access to some pages and posts
* Specify what role a user needs to be to view protected content
* Define the message to display to users who aren't permitted to access restricted content

You can decide which pages and posts to protect by selecting the ‘Restrict Content?’ checkbox in the page edit screen. It couldn't be any easier.

Restrictly uses the existing user roles to define who can and can't view content.

<strong>Please note:</strong> Restrictly uses `the_content` filter. If your theme doesn't make use of the_content, as with some page builders, you might find that the plugin doesn't work as you'd wish. The plugin is built according to WordPress standards so any themes that don't use the same standards may not function as expected. The Pro version has an option that allows you to redirect restricted users to a pre-defined page instead of displaying a message. This doesn't use `the_content` filter so should work with any theme.

= Restrictly Pro =
You can find additional functionality in the Pro version of the plugin, including:

* Restrict access to media files
* Define different access rules for single pages, posts and custom post types
* Define different access rules for different post types
* Define different access rules for different archive pages
* Define different access rules for different taxonomies
* Choose to redirect restricted users to a specific page

Restrictly Pro is [available to download here](https://catapultthemes.com/downloads/restrictly-pro/).

== Installation ==
1. Upload the `restrictly` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Browse to the Restrictly option page in Settings to configure

== Frequently Asked Questions ==

== Screenshots ==

1. The Restrictly settings screen. That's it.

== Changelog ==

= 1.0.1, May 24 2017 =

* Added: Anyone can view option
* Added: restrictly_filter_restriction_rule filter
* Added: restrictly_filter_restriction_levels filter
* Added: save roles to transient
* Added: restrictly_filter_is_page_restricted filter
* Added: restrictly_filter_can_user_access filter
* Updated: select multiple permitted roles

= 1.0.0 =

* Initial commit

== Upgrade Notice ==

Nothing here
