<?php

use PHPUnit\Framework\TestCase;

class SanitizeTest extends TestCase {

	public function test_sanitize_event_path_accepts_owner_event(): void {
		$this->assertSame( 'owner/event', Cal_ID_Embed_Sanitize::sanitize_event_path( 'owner/event' ) );
	}

	public function test_sanitize_event_path_accepts_team_owner_event(): void {
		$this->assertSame( 'team/owner/event', Cal_ID_Embed_Sanitize::sanitize_event_path( 'team/owner/event' ) );
	}

	public function test_sanitize_event_path_accepts_https_url(): void {
		$this->assertSame( 'owner/event', Cal_ID_Embed_Sanitize::sanitize_event_path( 'https://cal.id/owner/event' ) );
	}

	public function test_sanitize_event_path_accepts_https_team_url(): void {
		$this->assertSame( 'team/owner/event', Cal_ID_Embed_Sanitize::sanitize_event_path( 'https://cal.id/team/owner/event' ) );
	}

	/**
	 * @dataProvider invalidEventPathsProvider
	 */
	public function test_sanitize_event_path_rejects_invalid_inputs( $raw ): void {
		$this->assertInstanceOf( WP_Error::class, Cal_ID_Embed_Sanitize::sanitize_event_path( $raw ) );
	}

	public function invalidEventPathsProvider(): array {
		return array(
			array( 'http://cal.id/owner/event' ),
			array( 'https://evil.example/owner/event' ),
			array( 'https://cal.id/owner/event/extra' ),
			array( 'https://cal.id/owner/event?x=<script>' ),
			array( 'https://cal.id/owner/event#fragment' ),
			array( 'javascript:alert(1)' ),
			array( '<script>' ),
			array( '../../owner/event' ),
			array( 'team/owner' ),
			array( 'owner/event?bad=1' ),
			array( "ownér/event" ),
			array( str_repeat( 'a', 500 ) ),
		);
	}

	public function test_sanitize_theme(): void {
		$this->assertSame( 'dark', Cal_ID_Embed_Sanitize::sanitize_theme( 'dark' ) );
		$this->assertSame( 'auto', Cal_ID_Embed_Sanitize::sanitize_theme( 'bogus' ) );
	}

	public function test_sanitize_layout(): void {
		$this->assertSame( 'modal', Cal_ID_Embed_Sanitize::sanitize_layout( 'modal' ) );
		$this->assertSame( 'inline', Cal_ID_Embed_Sanitize::sanitize_layout( 'bogus' ) );
	}

	public function test_sanitize_brand_color(): void {
		$this->assertSame( '#abc', Cal_ID_Embed_Sanitize::sanitize_brand_color( '#abc' ) );
		$this->assertSame( '#aabbcc', Cal_ID_Embed_Sanitize::sanitize_brand_color( '#AABBCC' ) );
		$this->assertSame( 'blue', Cal_ID_Embed_Sanitize::sanitize_brand_color( 'blue' ) );
		$this->assertSame( '', Cal_ID_Embed_Sanitize::sanitize_brand_color( 'url(javascript:alert(1))' ) );
	}

	public function test_sanitize_button_text(): void {
		$this->assertSame( 'Book now', Cal_ID_Embed_Sanitize::sanitize_button_text( '' ) );
		$this->assertSame( 'Hello world', Cal_ID_Embed_Sanitize::sanitize_button_text( '<strong>Hello</strong> world' ) );
	}

	public function test_sanitize_embed_height(): void {
		$this->assertSame( 320, Cal_ID_Embed_Sanitize::sanitize_embed_height( 1 ) );
		$this->assertSame( 600, Cal_ID_Embed_Sanitize::sanitize_embed_height( 600 ) );
		$this->assertSame( 1600, Cal_ID_Embed_Sanitize::sanitize_embed_height( 99999 ) );
	}

	public function test_sanitize_utm_param(): void {
		$this->assertSame( 'abc_123.-~', Cal_ID_Embed_Sanitize::sanitize_utm_param( 'a b c_123.-~!@#' ) );
		$this->assertSame( 100, strlen( Cal_ID_Embed_Sanitize::sanitize_utm_param( str_repeat( 'a', 200 ) ) ) );
	}

	public function test_sanitize_boolean_flag(): void {
		$this->assertTrue( Cal_ID_Embed_Sanitize::sanitize_boolean_flag( '1' ) );
		$this->assertFalse( Cal_ID_Embed_Sanitize::sanitize_boolean_flag( '0' ) );
		$this->assertFalse( Cal_ID_Embed_Sanitize::sanitize_boolean_flag( 'nope' ) );
	}

	public function test_sanitize_prefill_flag(): void {
		$this->assertTrue( Cal_ID_Embed_Sanitize::sanitize_prefill_flag( true ) );
		$this->assertFalse( Cal_ID_Embed_Sanitize::sanitize_prefill_flag( false ) );
	}
}
