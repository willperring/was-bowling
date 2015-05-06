<?php

Class AjaxController extends Controller {
	
	public function save() {

		$x = Player::find_by( array(
			'handle' => 'willperring'
		));

		var_dump($x);

		$player = new Player();
		$player->handle = 'willperring2';
		$player->save();

		var_dump($player);

		die('hello?');
	}

}