=== Cal ID ===
Contributors: calid
Tags: appointment, meeting, scheduling, booking calendar, calid
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add Cal ID booking pages to your WordPress site with a block or shortcode.

== Description ==

Cal ID is a online appointment scheduling tool that handles confirmations, reminders, CRM sync, and payments automatically after every booking.

- a WordPress block
- a shortcode: `[cal_id_embed]`

Supported layouts:

- inline
- modal
- floating

= External services =

This plugin embeds event pages hosted by Cal ID. When an embed is rendered, the front end loads the Cal ID embed script from `https://cal.id/embed-link/embed.js` and displays the configured Cal ID event from `https://cal.id/`.

The event path and optional display settings such as theme, layout, brand color, button text, and UTM parameters are passed to Cal ID so the booking embed can be displayed. If logged-in prefill is enabled, the plugin fetches the current logged-in user's name and email from a protected local WordPress REST endpoint when the embed loads, then sends that data to Cal ID for prefill. The user details are not printed into the page HTML.

Use of the Cal ID service is subject to Cal ID's [Terms of Use](https://cal.id/termofuse) and [Privacy Policy](https://cal.id/privacy-policy).

== Installation ==

1. Install using the WordPress built-in Plugin installer or Upload the plugin files to `/wp-content/plugins/cal-id/`
2. Activate the plugin in WordPress
3. Add the `Cal ID` block or place the shortcode in your content

== Usage ==

Block:

1. Insert `Cal ID`
2. Enter a Cal ID event path such as:
   - `owner/event`
   - `team/owner/event`
   - `https://cal.id/owner/event`

Shortcode:

    [cal_id_embed event_path="owner/event" layout="inline"]

Modal example:

    [cal_id_embed event_path="owner/event" layout="modal" button_text="Book now"]

Floating example:

    [cal_id_embed event_path="owner/event" layout="floating"]

== Screenshots ==

1. Inline booking embed in the block editor.
2. Inline booking embed in live page.
2. Floating booking embed in live page.

== FAQ ==

= What kind of Cal link should I enter? =

Use a hosted Cal ID link or path such as `owner/event`, `team/owner/event`, or a full `https://cal.id/...` URL.

= Can I choose how the booking page appears? =

Yes. You can choose inline, modal, or floating display modes.

= Can I use this on the same page as other content blocks? =

Yes. The embed can sit alongside other blocks, images, text, and shortcodes on the same page.

= How do I change the look of the embed? =

You can use the block settings for layout, theme, brand color, button text, height, and UTM parameters.

= What should I check if the embed does not appear? =

Make sure the event path points to a hosted Cal ID page on `https://cal.id/` and that the layout is set correctly.

= Does prefill happen automatically? =

Only when you turn prefill on. In that case, the plugin fetches the current logged-in user's name and email when the embed loads and passes them to Cal ID for prefill.

== Changelog ==

= 1.0.0 =

- Initial release
- Dynamic block and shortcode support
- Shared sanitization and render layer
- REST prefill endpoint
- Frontend runtime and editor preview
- Security and integration tests
