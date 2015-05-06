<?php

Class Player extends Model {

	protected $_table = 'player';

	protected $_properties = array(
		'id'     => array('pk'),
		'handle' => array(),
	);

}