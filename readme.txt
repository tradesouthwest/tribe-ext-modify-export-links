=== Tribe Ext Modify Export Links ===
Contributors: Tradesouthwest ModernTribe
Donate link: https://paypal.me/tradesouthwest
Tags: events, calendar, remove, export, button 
Requires at least: 4.5
Tested up to: 4.9.4
Requires PHP: 5.2.4
Stable tag: 1.0.4
License: GPL version 3 or any later version
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Modify the export links from all TEC Calendar Views.

== Description ==

* Remove the "+ Export Events" link from all Calendar Views;
* Remove the "+ Google Calendar" and "+ Ical Export" links from Single Events.

== Installation ==

Install and activate like any other plugin!

* shortcode required on page with definitive name
Page Name: Export Events Page
Shortcode: [tribe_ext_modify_to_links]

* In Tribe Events > Settings > Display add shortcode to initiate main plugin. Do not forget to
add to the appropriate place, Add HTML after event content box should suffice.
Shortcode: [tribe_ext_modify_buttons]


below is the test calendar direct url feed. tswprograms@gmail.com 
$publicGcal = 'https://calendar.google.com/calendar/ical/c6a5o6gnj3ejm9v21v1ppidv6um6pfo1%40import.calendar.google.com/public/basic.ics';
https://calendar.google.com/r?text=%s&dates=%s&details=%s&location=%s&trp=%s&sprop=%s&ctz=%s


* hooks: the 6 different hooks in the the-events-calendar/src/views/list/single-event.php template
    'tribe_events_before_the_event_title'
    'tribe_events_after_the_event_title'
    'tribe_events_before_the_meta' (our example)
    'tribe_events_after_the_meta'
    'tribe_events_before_the_content'
    'tribe_events_after_the_content'

== Frequently Asked Questions ==

= Where can I find more extensions? =

Please visit our [extension library](https://theeventscalendar.com/extensions/) to learn about our complete range of extensions for The Events Calendar and its associated plugins.

= What if I experience problems? =

We're always interested in your feedback and our [premium forums](https://theeventscalendar.com/support-forums/) are the best place to flag any issues. Do note, however, that the degree of support we provide for extensions like this one tends to be very limited.

== Changelog ==

= 1.0.0 2018-04-12 = Initial Version

* Initial release
