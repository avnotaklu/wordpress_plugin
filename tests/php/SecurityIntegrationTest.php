<?php

use PHPUnit\Framework\TestCase;

class SecurityIntegrationTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['cal_id_test_is_user_logged_in'] = false;
		$GLOBALS['cal_id_test_current_user'] = (object) array(
			'display_name' => 'Test User',
			'user_email'   => 'test@example.com',
		);
		$GLOBALS['cal_id_test_rest_routes'] = array();
	}

	public function test_hostile_brand_color_is_neutralized_in_render(): void {
		$html = Cal_ID_Embed_Render::render(
			array(
				'eventPath'   => 'owner/event',
				'brandColor'  => 'url(javascript:alert(1))',
				'buttonText'  => '<script>alert(1)</script>',
				'layout'      => 'modal',
			),
			'frontend'
		);

		$this->assertStringNotContainsString( 'javascript:alert(1)', $html );
		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringContainsString( 'Book now', $html );
	}

	public function test_hostile_shortcode_attributes_are_sanitized(): void {
		$html = Cal_ID_Embed_Shortcode::render_shortcode(
			array(
				'eventpath'   => 'https://cal.id/owner/event?bad=<script>',
				'brandcolor'  => 'expression(alert(1))',
				'buttontext'  => '<strong>Reserve</strong>',
				'layout'      => 'modal',
			)
		);

		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringNotContainsString( 'expression(alert(1))', $html );
		$this->assertStringContainsString( 'Reserve', $html );
	}

	public function test_config_json_remains_inert(): void {
		$html = Cal_ID_Embed_Render::render(
			array(
				'eventPath'  => 'owner/event',
				'buttonText' => '</script><script>alert(1)</script>',
				'utmSource'  => 'a" onclick="alert(1)',
			),
			'frontend'
		);

		$this->assertStringContainsString( '<\/script>', $html );
		$this->assertStringNotContainsString( 'onclick=', $html );
	}

	public function test_rest_route_is_registered_and_unauthorized_access_is_rejected(): void {
		Cal_ID_Embed_Rest_Prefill::register_routes();

		$this->assertNotEmpty( $GLOBALS['cal_id_test_rest_routes'] );
		$this->assertSame( 'cal-id/v1', $GLOBALS['cal_id_test_rest_routes'][0]['namespace'] );
		$this->assertSame( '/prefill', $GLOBALS['cal_id_test_rest_routes'][0]['route'] );

		$permission = Cal_ID_Embed_Rest_Prefill::permissions_check();
		$this->assertInstanceOf( WP_Error::class, $permission );
	}

	public function test_rest_route_returns_only_current_user_data_when_logged_in(): void {
		$GLOBALS['cal_id_test_is_user_logged_in'] = true;
		$GLOBALS['cal_id_test_current_user'] = (object) array(
			'display_name' => 'Current User',
			'user_email'   => 'current@example.com',
			'ID'           => 123,
			'roles'        => array( 'subscriber' ),
		);

		$payload = Cal_ID_Embed_Rest_Prefill::handle_request();

		$this->assertSame(
			array(
				'name'  => 'Current User',
				'email' => 'current@example.com',
			),
			$payload
		);
	}
}
