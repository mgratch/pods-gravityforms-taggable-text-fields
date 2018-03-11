=== Pods Gravityforms Taggable Text Fields ===
Contributors: Marc Gratch
Donate link: https://marcgratch.com/
Tags: pods, gravityforms, gravity-forms. taggable
Requires at least: 4.4
Tested up to: 4.9.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert a text field to a taggable field with Pods, Gravity Forms, and the Pods Gravity Forms add-on.

== Description ==

When submitting a text field of term names to Pods::add() if the term already exists the user will receive an error.
Already existing terms are expected to include `term_id` in the `$tag_data` array.s).

Now text fields in GF marked as taggable with the additional plugin will work as expected.
This code is a bit rough and there is more duplication than I would like... this is a first draft.

== Installation ==

1. Install [Pods](https://wordpress.org/plugins/pods/)
1. Install [gravity-forms-custom-post-types](https://wordpress.org/plugins/gravity-forms-custom-post-types/ ) or a similar plugin that creates a field that autopopulates choices as well as allows adding new choices
1. Install [pods-gravity-forms](https://wordpress.org/plugins/pods-gravity-forms/)
1. Install this plugin
1. Enjoy text fields mapped to taxonomies to have the ability to add new non-hierarchal terms
1. Ensure your text field is outputting comma separated strings.

== Changelog ==

= 0.1.0 =
* initial release