=== Cal ID Embed ===
Contributors: cal-id
Tags: calendar, booking, embed, shortcode, gutenberg
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add Cal ID booking pages to your WordPress site with a block or shortcode.

== Description ==

Cal ID Embed makes it easy to place hosted Cal ID booking pages inside WordPress using:

- a Gutenberg block
- a shortcode: `[cal_id_embed]`

Supported layouts:

- inline
- modal
- floating

What it supports:

- hosted `https://cal.id/...` links only
- sanitized shared render path for block and shortcode
- logged-in prefill via REST only
- UTM tracking support
- cache-safe output with no user PII in HTML

= External services =

This plugin embeds event pages hosted by Cal ID. When an embed is rendered, the front end loads the Cal ID embed script from `https://cal.id/embed-link/embed.js` and displays the configured Cal ID event from `https://cal.id/`.

The event path and optional display settings such as theme, layout, brand color, button text, and UTM parameters are passed to Cal ID so the booking embed can be displayed. If logged-in prefill is enabled, the plugin exposes the current logged-in user's name and email through a protected local WordPress REST endpoint for use by the embed. This information is not printed into the page HTML.

Use of the Cal ID service is subject to Cal ID's terms and privacy policy.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/cal-id-embed/`
2. Activate the plugin in WordPress
3. Add the `Cal ID Embed` block or place the shortcode in your content

== Usage ==

Block:

1. Insert `Cal ID Embed`
2. Enter a Cal ID event path such as:
   - `owner/event`
   - `team/owner/event`
   - `https://cal.id/owner/event`

Shortcode:

```text
[cal_id_embed event_path="owner/event" layout="inline"]
```

Modal example:

```text
[cal_id_embed event_path="owner/event" layout="modal" button_text="Book now"]
```

Floating example:

```text
[cal_id_embed event_path="owner/event" layout="floating"]
```

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

== Changelog ==

= 1.0.0 =

- Initial release scaffold
- Dynamic block and shortcode support
- Shared sanitization and render layer
- REST prefill endpoint
- Frontend runtime and editor preview
- Security and integration tests
