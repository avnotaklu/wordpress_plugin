=== Cal ID Embed ===
Contributors: cal-id
Tags: calendar, booking, embed, shortcode, gutenberg
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed hosted Cal ID event pages in WordPress with a dynamic block and shortcode.

== Description ==

Cal ID Embed lets you embed hosted Cal ID event pages inside WordPress using:

- a dynamic Gutenberg block
- a shortcode: `[cal_id_embed]`

Supported layouts:

- inline
- modal
- floating

Key behaviors:

- hosted `https://cal.id/...` links only
- sanitized shared render path for block and shortcode
- logged-in prefill via REST only
- UTM tracking support
- cache-safe output with no user PII in HTML

= External services =

This plugin embeds event pages hosted by Cal ID. When an embed is rendered, the front end loads the Cal ID embed script from `https://cal.id/embed-link/embed.js` and displays the configured Cal ID event from `https://cal.id/`.

The event path and optional display settings such as theme, layout, brand color, button text, and UTM parameters are passed to Cal ID so the booking embed can be displayed. If logged-in prefill is enabled, the plugin exposes the current logged-in user's name and email through a protected local WordPress REST endpoint for use by the embed; this information is not printed into the page HTML.

Use of the Cal ID service is subject to Cal ID's terms and privacy policy.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/cal-id-embed/`
2. Activate the plugin in WordPress
3. Add the `Cal ID Embed` block or use the shortcode

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

= Does this support non-Cal hosts? =

No. This plugin only accepts hosted `cal.id` event URLs and normalized Cal ID paths.

= Does it expose user email or name in page HTML? =

No. Logged-in prefill is fetched client-side from a protected REST endpoint.

= Can I use this with caching plugins? =

Yes. The embed output is cache-safe because it does not render user-specific PII into HTML.

== Changelog ==

= 1.0.0 =

- Initial release scaffold
- Dynamic block and shortcode support
- Shared sanitization and render layer
- REST prefill endpoint
- Frontend runtime and editor preview
- Security and integration tests
