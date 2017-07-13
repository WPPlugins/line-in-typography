=== Line In Typography for WordPress ===
Contributors: simonfairbairn
Donate link: http://line-in.co.uk/donate
Tags: typography, theme development, jquery, line-height, vertical rhythm, typographic grid
Requires at least: 2.8
Tested up to: 3.2
Stable tag: 0.3.5

This magical piece of markup monkery will make your mission to muster magnificent missives on your monitor much more manageable.

== Description ==

"You know, for grids." #obscurefilmreferences

This plugin allows you to overlay a 12 or 16 column fluid grid to check your positioning when developing fluid or responsive sites.

It also assists you by overlaying lines based on your theme's line height so that you can correctly set your vertical rhythm and line up all of your typography to a baseline grid like the titan of typography that I'm certain you are. 

One should be aware that this is a plugin primarily promoted to pixel pushers and isn't for production use. You would do well to know quite a bit about CSS and you should know how your theme is laid out in the HTMLs before using this plugin. 

Much to my deep sorrow, it is but a certainty that this fine piece of positional plugin prowess won't work with IE 8 and below (but, surely, one would not be so foolish as to create sites using IE as a development browser) as it requires the magnificent `background-size: 100%` CSS3 property to be present. Which in IE, to my great distress, it's not.

== Installation ==

1. Upload the 'line-in-typography' folder to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Settings -> Line In Typography` and edit your settings (you'll need to know which elements you want to apply the grid to)
1. As if by magic, a control panel should appear on the user side where you should be able to toggle the various options.


== Frequently Asked Questions ==

= Will this make my theme have amazing typography? =

Oh my dear fellow, good grief no. A computer? Automatically setting type? Mr Gutenberg would be aghast at such a thought!

Fear not though, friend, for I have a solution for you! The Inter Web Network!

I would advise a thorough study of [this article](http://24ways.org/2006/compose-to-a-vertical-rhythm) on composing to a vertical rhythm. 

Don't worry, old chap, it took me a good long while to make sense of this obfuscated balderdash too. Should I ever be blessed with a moment, it would cheer me no end to publish some articles about this very topic (maybe with some LESS mixins) over on my [web design blog](http://line-in.co.uk/blog). 

== Screenshots ==

1. The lines activated, with the test paragraph at the top and the control panel displayed.
1. Grids activated with the control panel hidden. Notice that the grids override the current background on the elements that they're applied to so they're not hidden.

== Changelog ==

= 0.3.5 =
* Improved API
* Ability to upload custom column images

= 0.3.4 =
* Pseudo-namespacing on the Pages base class and the Settings API base class. Should help to avoid conflicts with other Line In products and other plugins that use these classes. When PHP5.3 is widespread, I will be happy.

= 0.3.3 =
* Minor bug fixes

= 0.3.2 =
* Updates to the settings API base class. 
* "Improvements" to the About box.

= 0.3.2 =
* Minor updates to the base classes

= 0.3.1 =
* When WP_DEBUG is true, styles and scripts will use time() as version to prevent caching
* Changes to the Settings API base class
* Added cache-busting support to the base page class.

= 0.3.0 =

* Revamped Admin page to take advantage of Meta boxes and the Settings API
* Huge code refactoring to have better reusable base classes to build future plugins on
* Added About Line In meta box 

= 0.2.2 =

* 16 column grid was missing the background-size: 100% declaration

= 0.2.1 =

* Added screenshots
* Updated Readme with more detailed instructions

= 0.2.0 =

* Added options page using settings API
* Now outputs those options to be picked up by the javascript
* Removed option to set font size (irrelevant, as everything is based on Line Height anyway)

= 0.1.0 =

* Initial Release

== Upgrade Notice ==

= 0.3.5 =
Bored with the standard 12 and 16 column layouts? Now you can upload your own! Now go build some asymmetrical 31 column sites!

= 0.3.2 =
This version is much more fun. No functionality has really changed, but it's much more fun!

= 0.3.1 =
Completely revamped the code base and made the settings page look much prettier. Everyone loves a pretty settings page!

= 0.2.2 =

Grids weren't displaying properly because of a missing `background-size: 100%` CSS declaration.

= 0.2.1 =
Minor enhancements.

= 0.2.0 =
Lots of options to play with in the spangly new options page in the dashboard. No need to edit JavaScript directly.


== Roadmap ==

= 0.3.0 =

* Allow user to set the wrapping element that the line-height is derived from
* Consistent styling on overlay box ( not overridden by theme styles)
* More thorough validation checks (check for either a . or a # on each of the DOM elements coming in and make sure there are no spaces)
* Ability to upload custom column images

= 0.4.0 =

* Better UI and more clear instructions
* Better cross-browser support
* Option to set grid/lines to on or off on page load

= 0.5.0 =

* Option to have the control panel hidden or shown and to have the previous state remembered




