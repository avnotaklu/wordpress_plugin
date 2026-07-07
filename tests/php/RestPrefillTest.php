<?php

use PHPUnit\Framework\TestCase;

class RestPrefillTest extends TestCase {

	public function test_logged_out_request_is_denied(): void {
		$this->assertInstanceOf( WP_Error::class, Cal_ID_Embed_Rest_Prefill::permissions_check() );
	}

	public function test_registered_route_and_payload_shape(): void {
		$payload = Cal_ID_Embed_Rest_Prefill::handle_request();
		$this->assertArrayHasKey( 'name', $payload );
		$this->assertArrayHasKey( 'email', $payload );
		$this->assertCount( 2, $payload );
	}
}
