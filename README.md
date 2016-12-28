# WP User Activity

WP User Activity is the best way to log activity in WordPress. Activity can be sorted, filtered, and viewed per-user, along with session data for logged in users and IP addresses & user-agents for others.

Activity is broken down into object "Types" and "Actions." Types are the objects being acted upon, and actions are what is being done to them. If you're familiar with BuddyPress, this should feel pretty comfortable.

# Available actions

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

# Examples

```
Admin logged in 2 minutes ago.
```

```
Admin created the post "Hello World" 33 seconds ago.
```

```
Admin created the topic "I need help!" 5 days ago.
```

# Installation

* Download and install using the built in WordPress plugin installer.
* Activate in the "Plugins" area of your admin by clicking the "Activate" link.
* No further setup or configuration is necessary.

# FAQ

### Does this work with custom post types & taxonomies?

Yes. It will work perfectly with all post-types & taxonomies that define their own labels. Ones that don't are mocked using the post type ID.

### Can I create custom activity types & actions?

Yes. The autoloader can be filtered, so adding new object types is as simple as:

```
add_filter( 'wp_get_default_user_activity_types', function( $types = array() ) {
	$types[] = 'Your_New_Type' // extends `WP_User_Activity_Type`
} );
```

The `WP_User_Activity_Type_Taxonomy` is a good example to start with, if you'd like to create your own actions. It registers simple create/update/delete methods, with easy to understand messages & integrations.

### Does this create new database tables?

No. It uses the WordPress custom post-type and metadata APIs.

### Does this modify existing database tables?

No. All of the WordPress core database tables remain untouched.

### Where can I get support?

* Basic: https://wordpress.org/support/plugin/wp-user-activity/
* Priority: https://chat.flox.io/support/channels/wp-user-activity/

### Can I contribute?

Yes, please! The number of users needing activity logging in WordPress is always growing. Having an easy-to-use API and powerful set of functions is critical to managing complex WordPress installations. If this is your thing, please help out!
