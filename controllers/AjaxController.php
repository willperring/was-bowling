<?php

Class AjaxController extends Controller {
	
	public function save() {

		$player = new Player();
		$player->handle = 'willperring';

		var_dump($player);

		die('hello?');
	}

}