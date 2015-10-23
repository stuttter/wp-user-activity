=== WP User Activity ===
Contributors: johnjamesjacoby, stuttter
Tags: users, activity, log, attachment, comment, core, export, menu, plugin, post, settings, term, theme, user, widget
Requires at least: 4.3
Tested up to: 4.4
Stable tag: 0.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

WP User Activity is the best way to log activity in WordPress. Activity can be sorted, filtered, and viewed per-user, along with session data for logged in users and IP addresses & user-agents for others.

Activity is broken down into object "Types" and "Actions." Types are the objects being acted upon, and actions are what is being done to them. If you're familiar with BuddyPress, this should feel pretty comfortable.

= Examples =

`
Admin logged in 2 minutes ago.
`

`
Admin created the post "Hello World" 33 seconds ago.
`

`
Admin created the topic "I need help!" 5 days ago.
`

= Available Actions =

* Attachments (Upload, Edit, Delete)
* Comments (Create, Approve, Unaprove, Trash, Untrash, Spam, Unspam, Delete)
* Core (Update, Auto-update)
* Exports (Download)
* Menus (Create, Update, Delete)
* Plugins (Install, Update, Activate, Deactivate, Edit, Delete)
* Posts (Create, Update, Delete, Trash, Untrash, Spam, Unspam, Future)
* Settings (Update)
* Terms (Create, Update, Delete)
* Themes (Customize, Install, Update, Activate, Edit, Delete)
* Users (Login, Login Failure, Logout, Register, Update, Delete)
* Widgets (Update, Delete)

= Also checkout =

* [WP Event Calendar](https://wordpress.org/plugins/wp-event-calendar/ "The best way to manage events in WordPress.")
* [WP Term Meta](https://wordpress.org/plugins/wp-term-meta/ "Metadata, for taxonomy terms.")
* [WP Term Order](https://wordpress.org/plugins/wp-term-order/ "Sort taxonomy terms, your way.")
* [WP Term Authors](https://wordpress.org/plugins/wp-term-authors/ "Authors for categories, tags, and other taxonomy terms.")
* [WP Term Colors](https://wordpress.org/plugins/wp-term-colors/ "Pretty colors for categories, tags, and other taxonomy terms.")
* [WP Term Icons](https://wordpress.org/plugins/wp-term-icons/ "Pretty icons for categories, tags, and other taxonomy terms.")
* [WP Term Visibility](https://wordpress.org/plugins/wp-term-visibility/ "Visibilities for categories, tags, and other taxonomy terms.")
* [WP User Avatars](https://wordpress.org/plugins/wp-user-avatars/ "Allow users to upload avatars or choose them from your media library.")
* [WP User Groups](https://wordpress.org/plugins/wp-user-groups/ "Group users together with taxonomies & terms.")
* [WP User Profiles](https://wordpress.org/plugins/wp-user-profiles/ "A sophisticated way to edit users in WordPress.")

== Screenshots ==

1. The log
2. Edit activity

== Installation ==

* Download and install using the built in WordPress plugin installer.
* Activate in the "Plugins" area of your admin by clicking the "Activate" link.
* No further setup or configuration is necessary.

== Frequently Asked Questions ==

= Does this work with custom post types & taxonomies? =

Yes. It will work perfectly with all post-types & taxonomies that define their own labels. Ones that don't are mocked using the post type ID.

= Can I create custom activity types & actions? =

Yes. The autoloader can be filtered, so adding new object types is as simple as:

`
add_filter( 'wp_get_default_user_activity_types', function( $types = array() ) {
	$types[] = 'Your_New_Type' // class that extends WP_User_Activity_Type
} );
`

The `WP_User_Activity_Type_Taxonomy` class is a good example to start with, if you'd like to create your own actions. It registers simple create/update/delete methods, with easy to understand messages & integrations.

= Does this create new database tables? =

No. It uses WordPress's custom post-type and metadata APIs.

= Does this modify existing database tables? =

No. All of WordPress's core database tables remain untouched.

= Where can I get support? =

The WordPress support forums: https://wordpress.org/support/plugin/wp-user-activity/

= Where can I find documentation? =

http://github.com/stuttter/wp-user-activity/

== Changelog ==

= 0.1.6 =
* Prevent user profile activity from leaking out

= 0.1.5 =
* Update column names
* Add support for WP User Profiles

= 0.1.4 =
* Improve compatibility with other plugins

= 0.1.3 =
* Add menu humility filter
* Improve capability checks

= 0.1.2 =
* Improve admin styling
* Add metadata callbacks

= 0.1.1 =
* Improve admin styling

= 0.1.0 =
* Initial release
