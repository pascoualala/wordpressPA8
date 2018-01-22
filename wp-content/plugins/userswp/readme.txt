﻿=== UsersWP - User Profile & Registration ===
Contributors: stiofansisland, paoltaia, ayecode
Donate link: https://userswp.io/
Tags: community, member, membership, user profile, user registration, login form, registration form, users directory
Requires at least: 3.1
Tested up to: 4.9
Stable tag: 1.0.11
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Light weight frontend user registration and login plugin.

== Description ==

= The only lightweight user profile plugin for WordPress. UsersWP features front end user profile, users directory, a registration and a login form. =

While BuddyPress, Ultimate Member, Profile Builder and similar plugins are excellent, we wanted to create something much lighter and simpler to use.

Something that could be gentle on your server resources. With less options to make it easy to setup, more hooks to make it infinitely extensible.

Today UsersWP is by far the simplest solution available to manage users on WordPress. It takes seconds to setup, it is super fast and it's perfect to create a community of users within your website.

= FEATURES =

* Drag and Drop profile builder with all kind of user profile custom fields.
* Shortcode for the Login form
* Shortcode for the Registration form
* Shortcode for the Edit Account form
* Shortcode for the Users Directory
* Shortcode for the User profile
* Shortcode for the Password Recovery form
* Shortcode for the Change Password form
* Shortcode for the Reset Password form
* Custom menu items like login/logout links and links to relevant pages.

After activation all pages are created with the correct shortcodes so that you are good to go in seconds.

= User Profile =

The user profile features a cover image, an avatar and an optional tabbed menu showing :

* User's posts
* User's comments
* Custom fields (if any)

You can chose if you want to hide any section of it and where to show the custom fields. In a sidebar or in their own tab.

Otherwise just customize the tempalates as you wish within your child theme.

= Free Add-ons =

We provide some free extensions:

[Social Login](https://userswp.io/downloads/social-login/)
[ReCAPTCHA](https://userswp.io/downloads/recaptcha/)

= Premium Add-ons =

UsersWP can be extended with several add-ons. Few examples are:

* [Woocommerce](https://userswp.io/downloads/woocommerce/) - Connect WooCommerce with UsersWP, display orders and reviews in user profile pages.
* [bbPress](https://userswp.io/downloads/bbpress-2/) - Connect bbPress with UsersWP, display forum interactions in user profile pages.
* [Easy Digital Downloads](https://userswp.io/downloads/easy-digital-downloads/) - Display “Downloads” and “Purchases” tab in user profile pages.
* [GeoDirectory](https://userswp.io/downloads/geodirectory/) - Create a tab for each listing type submitted, reviews and favorite listings.
* [WP Job Manager](https://userswp.io/downloads/wp-job-manager/) - Connects WP Job Manager with UsersWP, display Jobs tab in user profile pages.
* [Mail Chimp](https://userswp.io/downloads/mailchimp/) - Allows the user to subscribe to your newletters via Mailchimp during registration.

There are many others and we release new Add-ons frequently. You can see the full collection here: [UsersWP Premium Add-ons](https://userswp.io/downloads/category/addons/)

Should you find any bug, please report it in the [support forum](https://userswp.io/support/) and we will fix it asap!

UsersWP is 100% translatable.

== Installation ==

= Minimum Requirements =

* WordPress 3.1 or greater

* PHP version 5.2.4 or greater

* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option. To do an automatic install of UsersWP, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type UsersWP and click Search Plugins. Once you've found our plugin you install it by simply clicking Install Now. [UsersWP installation](https://userswp.io/docs/2017/02/24/userswp-overview/)

= Manual installation =

The manual installation method involves downloading UsersWP and uploading it to your webserver via your favourite FTP application. The WordPress codex will tell you more [here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should seamlessly work. We always suggest you backup up your website before performing any automated update to avoid unforeseen problems.

== Frequently Asked Questions ==

No questions so far, but don't hesitate to ask!

== Screenshots ==

1. User Profile Page.
2. Users Directory.
3. Registration Form.
4. Drag and Drop Profile Builder.
5. Login Form.
6. Edit Account Form.

== Changelog ==

= 1.0.11 =
* Some emails have an extra opening p tag - FIXED
* WP 4.8.3 can cause Edit profile page to not display all fields - FIXED

= 1.0.10 =
* Default value can now be used to set the default country by setting the the two letter country code, example "de" - ADDED
* Fatal error: Class ‘UsersWP_Meta’ not found in new installation - FIXED

= 1.0.9 =
* Upgrade function changed to run on version change and not on activation hook only - FIXED

= 1.0.8 =
* For admin use option added - ADDED
* Username label changed to "Username or Email" - CHANGED
* New email notification tags breaks reset and activate keys - FIXED
* Profile image change popup can sometimes be hidden or not clickable - FIXED
* uwp_form_input_field_xxx filter added - ADDED

= 1.0.7 =
* Edit user in wp-admin does not display country input correctly - FIXED
* Tools page added - ADDED
* Major code refactoring - CHANGED
* Class names renamed from Users_WP to UsersWP for better naming and consistency - CHANGED
* Email links are displayed as plain text - FIXED
* Login widget redirects to current page - ADDED
* Register admin notification setting - ADDED
* File Preview Not working properly - FIXED
* Font awesome icon displayed instead of count 1 for custom field "display as tab" - CHANGED
* Updates not creating the new table columns - FIXED
* Fieldset with its fields can be displayed in own profile tab - ADDED
* Url field value not getting printed in more info tab - FIXED
* Bio not displaying correctly - FIXED
* User bio slashes contains duplicate slashes - FIXED
* User privacy settings not working correctly - FIXED
* Email notifications code refactored to override via hooks - CHANGED
* Email error logs now contains full error in json format - ADDED
* Extra tags added for forgot and activate mails - ADDED
* Register widget - ADDED
* WPML compatibility - ADDED
* Display confirm password and confirm email fields only on register form - CHANGED
* Avatar breaks when social login profile url used - FIXED
* Facebook profile image can break profile image - FIXED
* Some profile page CSS changes - CHANGED

= 1.0.6 =
* First release on WordPress.org - :)
* Checks profile tabs array is unique before saving - ADDED
* fade and show class renamed to avoid conflict with other themes - CHANGED
* Chosen select inputs on form builder CSS issue, too thin - FIXED

= 1.0.4 =
* PHP < 5.5 compatibility changes - FIXED

= 1.0.3 =
* Added callback to show info type setting - ADDED
* Profile tabs now appear in the order they are added - CHANGED

= 1.0.1 =
* First beta release.

= 1.0.0 =
* First alpha release.

== Upgrade Notice ==

TBA