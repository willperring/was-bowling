<?php

Class TwitterController extends Controller {

	private function makeRequest( $url, $post=false ) {
		
		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if( $post )
			curl_setopt($ch, CURLOPT_POST, true);
	}

	public function login() {

	}


}