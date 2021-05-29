=== WP User Activity ===
Author:            Triple J Software, Inc.
Author URI:        https://jjj.software
Donate link:       https://buy.stripe.com/7sI3cd2tK1Cy2lydQR
Plugin URI:        https://wordpress.org/plugins/wp-user-activity
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
License:           GPLv2 or later
Contributors:      johnjamesjacoby
Tags:              user, profile, activity, log
Requires PHP:      7.2
Requires at least: 5.2
Tested up to:      5.8
Stable tag:        2.2.1

== Description ==

WP User Activity is the best way to log activity in WordPress. Activity can be sorted, filtered, and viewed per-user, along with session data for logged in users and IP addresses & user-agents for others.

Activity is broken down into object "Types" and "Actions." Types are the objects being acted upon, and actions are what is being done to them. If you're familiar with BuddyPress, this should feel pretty comfortable.

= Examples =

`Admin logged in 2 minutes ago.`

`Admin created the post "Hello World" 33 seconds ago.`

`Admin created the topic "I need help!" 5 days ago.`

= Available Actions =

* Attachments (Upload, Edit, Delete)
* Comments (Create, Pending, Approve, Unaprove, Trash, Untrash, Spam, Unspam, Delete)
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

= Recommended Plugins =

If you like this plugin, you'll probably like these, too!

* [WP User Profiles](https://wordpress.org/plugins/wp-user-profiles/ "A sophisticated way to edit users in WordPress.")
* [WP User Activity](https://wordpress.org/plugins/wp-user-activity/ "The best way to log activity in WordPress.")
* [WP User Avatars](https://wordpress.org/plugins/wp-user-avatars/ "Allow users to upload avatars or choose them from your media library.")
* [WP User Groups](https://wordpress.org/plugins/wp-user-groups/ "Group users together with taxonomies & terms.")
* [WP Term Authors](https://wordpress.org/plugins/wp-term-authors/ "Authors for categories, tags, and other taxonomy terms.")
* [WP Term Colors](https://wordpress.org/plugins/wp-term-colors/ "Pretty colors for categories, tags, and other taxonomy terms.")
* [WP Term Families](https://wordpress.org/plugins/wp-term-families/ "Associate taxonomy terms with other taxonomy terms.")
* [WP Term Icons](https://wordpress.org/plugins/wp-term-icons/ "Pretty icons for categories, tags, and other taxonomy terms.")
* [WP Term Images](https://wordpress.org/plugins/wp-term-images/ "Pretty images for categories, tags, and other taxonomy terms.")
* [WP Term Locks](https://wordpress.org/plugins/wp-term-locks/ "Protect categories, tags, and other taxonomy terms from being edited or deleted.")
* [WP Term Order](https://wordpress.org/plugins/wp-term-order/ "Sort taxonomy terms, your way.")
* [WP Term Visibility](https://wordpress.org/plugins/wp-term-visibility/ "Visibilities for categories, tags, and other taxonomy terms.")
* [WP Media Categories](https://wordpress.org/plugins/wp-media-categories/ "Add categories to media & attachments.")
* [WP Pretty Filters](https://wordpress.org/plugins/wp-pretty-filters/ "Makes post filters better match what's already in Media & Attachments.")
* [WP Chosen](https://wordpress.org/plugins/wp-chosen/ "Make long, unwieldy select boxes much more user-friendly.")

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
	$types[] = 'Your_New_Type'; // class that extends WP_User_Activity_Type
	return $types;
} );
`

The `WP_User_Activity_Type_Taxonomy` class is a good example to start with, if you'd like to create your own actions. It registers simple create/update/delete methods, with easy to understand messages & integrations.

= Does this create new database tables? =

No. It uses the WordPress custom post-type and metadata APIs.

= Does this modify existing database tables? =

No. All of the WordPress core database tables remain untouched.

= Where can I get support? =

* Community: https://wordpress.org/support/plugin/wp-user-activity
* Development: https://github.com/stuttter/wp-user-activity/discussions

== Changelog ==

= [2.2.1] - 2021-05-29 =
* Update author info
* Add sponsor link

= 2.2.0 =
* Fix filtering by user
* Fix compatibility with WP User Profiles
* Fix untrashing individual items
* Fix an incompatibility with ACF

= 2.1.0 =
* Fix user not being linked to "Logout" action

= 2.0.2 =
* Add escaping to admin area row output
* Return IDs in useful places for easier extending

= 2.2.0 =
* Fix filtering by user
* Fix compatibility with WP User Profiles
* Fix untrashing individual items
* Fix an incompatibility with ACF

= 2.1.0 =
* Fix user not being linked to "Logout" action

= 2.0.2 =
* Add escaping to admin area row output
* Return IDs in useful places for easier extending

= 1.1.0 =
* Remove all actions from transition_post_status to avoid infinite loops

= 1.0.0 =
* Official public release
* Add support for "pending" comments
* Improved mu-plugins location support

= 0.2.0 =
* Support for User Profiles 0.2.0

= 0.1.10 =
* Add missing action for 'plugin delete'

= 0.1.9 =
* Support for WP User Profiles 0.1.9

= 0.1.8 =
* Fix conflict causing widgets not to save

= 0.1.7 =
* Fix list-table issue causing fatal errors

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
