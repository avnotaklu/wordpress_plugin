<?php

use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase {

	public function test_empty_state_renders_placeholder(): void {
		$html = Cal_ID_Event_Embed_Render::render( array(), 'frontend' );
		$this->assertStringContainsString( 'Enter an event path to preview.', $html );
		$this->assertStringContainsString( 'Use owner/event or team/owner/event.', $html );
	}

	public function test_invalid_state_renders_generic_message_frontend(): void {
		$html = Cal_ID_Event_Embed_Render::render( array( 'eventPath' => 'javascript:alert(1)' ), 'frontend' );
		$this->assertStringContainsString( 'Booking is temporarily unavailable.', $html );
	}

	public function test_invalid_state_renders_editor_message(): void {
		$html = Cal_ID_Event_Embed_Render::render( array( 'eventPath' => 'javascript:alert(1)' ), 'editor' );
		$this->assertStringContainsString( 'Unable to preview embed - check event path.', $html );
	}

	public function test_inline_markup_contains_container_and_min_height(): void {
		$html = Cal_ID_Event_Embed_Render::render( array(
			'eventPath' => 'owner/event',
			'layout' => 'inline',
			'embedHeight' => 720,
		), 'frontend' );

		$this->assertStringContainsString( 'class="cal-id-event-embed__container"', $html );
		$this->assertStringContainsString( 'min-height:720px', $html );
	}

	public function test_modal_markup_contains_trigger_and_hidden_container(): void {
		$html = Cal_ID_Event_Embed_Render::render( array(
			'eventPath' => 'owner/event',
			'layout' => 'modal',
			'buttonText' => 'Book now',
		), 'frontend' );

		$this->assertStringContainsString( 'data-cal-id-trigger=', $html );
		$this->assertStringContainsString( '<button', $html );
		$this->assertStringContainsString( 'hidden', $html );
	}

	public function test_floating_markup_has_no_button(): void {
		$html = Cal_ID_Event_Embed_Render::render( array(
			'eventPath' => 'owner/event',
			'layout' => 'floating',
		), 'frontend' );

		$this->assertStringNotContainsString( '<button', $html );
		$this->assertStringContainsString( 'class="cal-id-event-embed__container"', $html );
	}

	public function test_json_payload_round_trips(): void {
		$html = Cal_ID_Event_Embed_Render::render( array(
			'eventPath' => 'https://cal.id/team/owner/event',
			'layout' => 'inline',
			'brandColor' => '#aabbcc',
			'prefillEnabled' => true,
		), 'frontend' );

		preg_match( '#<script type="application/json" class="cal-id-event-embed__config">(.*?)</script>#s', $html, $matches );
		$this->assertNotEmpty( $matches );
		$config = json_decode( $matches[1], true );
		$this->assertSame( 'team/owner/event', $config['eventPath'] );
		$this->assertSame( '#aabbcc', $config['brandColor'] );
		$this->assertTrue( $config['prefillEnabled'] );
	}

	public function test_no_pii_leakage(): void {
		$html = Cal_ID_Event_Embed_Render::render( array(
			'eventPath' => 'owner/event',
			'prefillEnabled' => true,
		), 'frontend' );

		$this->assertStringNotContainsString( '@', $html );
		$this->assertStringNotContainsString( 'email', strtolower( $html ) );
	}

	public function test_shortcode_and_render_share_output_shape(): void {
		$shortcode = Cal_ID_Event_Embed_Shortcode::render_shortcode( array(
			'event_path' => 'owner/event',
			'layout' => 'inline',
		) );
		$rendered = Cal_ID_Event_Embed_Render::render( array(
			'eventPath' => 'owner/event',
			'layout' => 'inline',
		), 'frontend' );

		$this->assertStringContainsString( 'cal-id-event-embed__config', $shortcode );
		$this->assertStringContainsString( 'cal-id-event-embed__config', $rendered );
	}
}
