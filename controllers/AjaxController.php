<?php

Class AjaxController extends Controller {
	
	public function save() {

		var_dump( $this->parsePostData() );
		exit;

		$player = new Player();
		$player->handle = 'willperring';
		$player->save();

		var_dump($player);

		die('hello?');
	}

	private function parsePostData() {
		$postdata = file_get_contents("php://input");
		if( empty($postdata) )
			return array();

		$payload  = json_decode( $postdata, true );
		return $payload;
	}

}