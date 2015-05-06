<?php

Class AjaxController extends Controller {
	
	public function save() {

		$player = new Player();
		$player->handle = 'willperring';
		$player->save();

		var_dump($player);

		die('hello?');
	}

}