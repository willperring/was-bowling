<?php

Class Bowl extends Model {

	protected static $_table = 'bowl';

	protected static $_properties = array(
		'gameid'   => array(),
		'playerid' => array(),
		'frame'    => array(),
		'bowl'     => array(),
		'count'    => array(),
	);

}