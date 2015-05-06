<?php

Class Player extends Model {

	protected static $_table = 'player';

	protected static $_properties = array(
		'id'     => array('pk'),
		'handle' => array(),
	);

}