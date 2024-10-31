=== RealSatisfied Widget ===

Contributors: RealSatisfied, zengy
Tags: RealSatisfied, Ratings, Real Estate, Testimonials, Reviews
Requires at least: 3.0
Tested up to: 4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display ratings and testimonials from a RealSatisfied Agent/Office Profile RSS feed in a sidebar widget on your WordPress site.

== Description ==

The plugin takes data from the Agent Profile or Office RSS feed and displays Testimonials and Ratings (where available) in a sidebar widget on your WordPress site.

Testimonials that are displayed on your Agent Profile Page are displayed via the plugin and where rating's are displayed on your Agent Profile Page are displayed via the plugin.

**WordPress Plugin Configuration Options**

 * *Title* : default Testimonials, set as required
 * *Vanity-Key* : eg simon-jones. Your vanity key is whatever is on the end of your Agent Profile Page URL (http://www.realsatisfied.com/{vanity-key})
 * *Mode* : Display 'Agent' or 'Office' details. Ratings are only displayed in Agent mode. Office mode displays all testimonials for the office.
 * *Animate Automatically* : default True, if unselected, the visitor will need to navigate between testimonials manually 
 * *Hold each slide for* : Set's the delay between slide transitions in seconds (ignored when Animate Automatically is not selected)
 * *Animation Type* : default Slide Set's the animation type between slide transitions (Slide or Fade) (ignored when Animate Automatically is not selected)
 * *Display Side Arrows* : default True
 * *Display Navigation Dots* : default False (recommended to be 'False' when number of testimonials exceeds 10)
 * *Display Dates with Testimonials* : default True, dates will be shown with testimonials
 * *Display Agent Photo (Office Mode Only): default both, in Office mode Agent photo's are displayed on both the widget in the sidebar and the popup. Options are Both|Page|Popup|None
 * *Display RealSatisfied Verified Shield* : default True, RealSatisfied Shield with link to Agent Profile Page is displayed on testimonial detail pop-up layer

== Installation ==

1. Upload `realsatisfied.zip` to the `/wp-content/plugins/` directory or install directly from wordpress.org
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the sidebar widget as per the instructions above.

== Frequently asked questions ==

Please see http://support.realsatisfied.com/

== Screenshots ==

1. Settings within the widget
2. Example of display on a WordPress Site

== Changelog ==

= 1.3.0 =

 * Initial release

= 1.3.1 =

 * Updated titles to display correctly
 * Minor css fixes
 * localised image path

= 1.3.2 =

 * Minor updates

= 1.3.3 =

 * Resolving upgrade issues and version numbers

= 1.3.4 =

 * Code clean up

= 1.3.5 =

 * Version Maintenance

= 1.4.0 =

 * Version Reset

= 1.4.1 =

 * Flex slider issue on some installations resolved

= 1.4.2 =

 * Tested for WordPress version 3.4.2 

= 1.4.3 =

 * Added option to show or hide ratings 

= 1.4.4 =

 * css fix 

= 1.4.5 =

 * css fix for li elements no always displaying correctly
 * added html <!--comments--> for plugin status where there is no output
 * tested for WP 3.5.1

= 1.5.0 =

 * Added Office display mode using Office RSS feed.
 * Altered default for navigation dots to FALSE
 * Improved error handling

= 1.5.1 =

 * Minor copy updates
 * Animation reset

= 1.5.2 =

 * Namespace update

= 1.5.3 =

 * jQuery update

= 1.5.4 =

 * minor css fix

= 1.5.5 =

 * added support for cURL where allow_url_fopen is unavailable

= 1.6.0 =

 * Added more support for Agent Photo's in Office Mode

= 1.6.1 =

 * css fix : min height

= 1.7.0 =

 * upgrade of flexslider to 2.1
 * namespace for flexslider introduced
 * minor css resets added

= 1.7.1 =

 * minor css fix

= 1.7.2 =

 * minor css fix

= 1.7.3 =

 * markup change and css updates

= 1.7.4 =

 * resolve caching issues and adding css resets 

= 1.8.0 =

 * update for WordPress 3.6.1
 * Added rich snippet support 

= 1.8.1 =

 * css updates for full screen width implementations

= 1.9.0 =

 * general code cleanup
 * full rewrite of the remote rss fetch routines
 * limited the number of testimonials the widget will fetch to 50
 * refactor of css to allow for more fluid layout options like full width widgets
 * changed default setting of navigation dots to FALSE
 * modified the slideshow testimonials to display on a single line
 * changed shield to link to agent profile in both office and agent mode

= 1.9.1 =

 * Removed navigation dots from widget

= 1.9.3 =

 * added improved error messaging
 * compatible with WP 3.9.1

= 1.9.4 =

 * updated RSS pathing
 * compatible with WP 4.1
 * randomised office testimonials

= 1.9.5 =

 * compatible with WP 4.4

= 1.9.6 =

 * constructor update for WP post 4.3

== Upgrade notice ==

For ongoing satisfaction, we recommend upgrading to the latest version of the plugin