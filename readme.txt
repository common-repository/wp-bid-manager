=== Plugin Name ===
Contributors: wp-bid-manager.com
Tags: bid manager, bid management, bid quoting, bids
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 1.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bid management dashboard.  Create bids, submit bids for quote responses, and manage them through various stages.

== Description ==

The WP Bid Manager allows users to create and manage bids.  Past or present, accepted or not, bid management is completely at your finger tips.  Enjoy the power of sending emails to whoever you would like in order to provide them with your bid details so they can give you a quote on your bid.  In addition, you can use the power of Google’s Maps to track your current bids locations.  This handy visual aid is available using an API key that is free to obtain from Google.

= Tested on =
* Mac Firefox 	:)
* Mac Safari 	:)
* Mac Chrome	:)
* PC Safari 	:)
* PC Chrome	    :)
* PC Firefox	:)
* iPhone Safari :)
* iPad Safari 	:)
* PC ie7		:S

= Please Vote and Enjoy =
Your votes really make a difference! Thanks.

== Installation ==

1. Upload ‘wp-bid-manager’ to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under "Settings" go to "Bid Manager" and select the account that will be managing bids
4. Click on the new menu item “Bid Manager” and follow the fast-start directions.


== Frequently Asked Questions ==

= Q. I have a question =
A. Please reach out to us via email at suppcontractors@gmail.com

== Screenshots ==

1. Dashboard

2. New Bid

3. Email Configuration

4. Reports


== Changelog ==

= 1.3.3 =
* 10/17/2017 - Multiple fixes / bug fixes surrounding the update scripts and the activation.
* 10/17/2017 - Took away the main Bid Manager under the “Settings” and opened the system up to all users (not just one you choose at the beginning)

= 1.3.2 =
* 09/21/2017 - WordPress repo was not parsing the top attributes of the plugin file correctly and this resulted in the version activity chart in the repo being messed up - fixed

= 1.3.1 =
* 09/20/2017 - Sending a response was not being sent due to old user table not existing anymore - fixed

= 1.3.0 =
* 09/16/2017 - Response page was still looking for a user specific from the legacy version causing it not to load any bid information to respond to - fixed

= 1.2.8 =
* 09/16/2017 - WordPress uses the $table_prefix before the 'user_level' in the database - this has been switched

= 1.2.7 =
* 09/15/2017 - Send another invite link on front end was going to the back end admin - fixed

= 1.2.6 =
* 09/15/2017 - The get started page had an error in the email settings link that if followed was not allowing the settings to be saved because it did not recognize the admin url - fixed
* 09/15/2017 - Fresh installs showing a warning for boolean value being passed when it needed to be array - fixed

= 1.2.5 =
* 09/11/2017 - Added WordPress built in translate for many dashboard strings
* 09/11/2017 - Tested up to WP 4.8
* 09/12/2017 - Beginning the architecture for the paid version to integrate seamlessly with free version upon upgrade
* 09/12/2017 - Created useful functions to report back varius Bid Manager settings and user membership settings to be used throughout
* 09/12/2017 - Added account info page
* 09/13/2017 - Combined all settings into one serialized array in the db table
* 09/14/2017 - Added new WordPress.org landing page banner images


= 1.2.4 =
* 09/03/2017 - Since address is not mandatory to submit a bid we also removed company information being mandatory
* 09/03/2017 - Took out redundant dashboard header
* 09/03/2017 - Invite email had Supplying Contractors info in footer - yanked it

= 1.2.3 =
* 08/31/2017 - Google API setting was not allowing API to be updated after inserted - fixed

= 1.2.2 =
* 08/31/2017 - HTML was not saving correctly on the bid notes and not rendering images and links properly - fixed.
* 08/31/2017 - Check to see if user is using the Google API key before telling them the address is incorrect which was causing bid save issues - fixed
* 08/31/2017 - Added "Delete" functionality to the active bid review so that an incorrect bid can been thrown out and started over - fixed
* 08/31/2017 - Shortcode options were not working with "FALSE" being set - although not breaking the functionality - fixed

= 1.2.1 =
* 08/29/2017 - Styling and added ability to dismiss email settings messages.

= 1.2.0 =
* 08/13/2017 - Major revisions including more settings, a shortcode, and individual bid settings.

= 1.1.9 =
* 05/16/2016 - Fix upload issue with last push.

= 1.1.8 =
* 05/16/2016 - Yanked the youtube video from the description because it is out-dated

= 1.1.7 =
* 02/16/2016 - Table update

= 1.1.6 =
* 02/14/2016 - Sanitization update

= 1.1.5 =
* 02/14/2016 - Added plugin version to the database for future checks to better issue updates to users for table changes, etc

= 1.1.4 =
* 02/03/2016 - Added dashboard admin WordPress "wrap" class
* 02/14/2016 - Added screenshots
* 02/14/2016 - Added a short video
* 02/14/2016 - Added the ability to do up to 10 email invites instead of just one at a time

= 1.1.3 =
* 02/02/2016 - Added a notification system to the dashboard for updates and fresh installs.

= 1.1.2 =
* 01/31/2016 - Fixed bug with email settings not saving/storing properly

= 1.1.1 =
* 01/29/2016 - Initial launch.