<?php

Class AjaxController extends Controller {

	/**
	 * Return the status of an AJAX call
	 *
	 * This function is used internally to standardise the appearance of response messages
	 *
	 * @param boolean|string $state   True for success, False for error, or string for custom
	 * @param string         $message (optional) status message
	 * @param array          $data    (optional) additional data to return
	 *
	 * @return void
	 */
	private function returnStatus( $state, $message=null, array $data=null ) {
		
		$state = ( is_string($state) )
			? $state
			: ( $state ) ? 'success' : 'error' ;


		$return = array(
			'status' => $state
		);

		if( $message )
			$return['message'] = $message;

		if( $data )
			$return['data'] = $data;

		echo json_encode( $return );
	}

	/**
	 * Unpack POST data payload
	 *
	 * POST Data, as arriving from angular's $http service, doesn't arrive in 
	 * the usual way that PHP handles, so we need to unpack it ourselves
	 */
	private function parsePostData() {
		
		// Read from the input stream
		$postdata = file_get_contents("php://input");
		if( empty($postdata) )
			return array();

		// Payload if JSON - easy to decode
		$payload  = json_decode( $postdata, true );
		return $payload;
	}
	
	public function save() {

		$payload = $this->parsePostData();

		if( !isset($payload['game']) || !count($payload['game']) ) {
			$this->returnStatus( false, 'No game data supplied' );
		}

		Model::startTransaction();

		try {
			$game = new Game();
			$game->save();
		
		} catch( Exception $e ) {
			$this->returnStatus(false, 'Couldn\'t save game data');
			Model::rollbackTransaction();
			exit;
		}

		// First level is arrays of frames, keyed by player handle
		foreach( $payload['game'] as $playerHandle => $frames ) {

			// See if we've got this player in the system already...
			$playerRows = Player::find_by(array('handle' => $playerHandle));

			try {
				if( count($playerRows) ) {
					// ..we do. Update player count
					$player = $playerRows[0];
					$player->gamecount++;
					$player->save();

				} else {
					// ...we don't. Create new player
					$player = new Player();
					$player->handle = $playerHandle;
					$player->save();
				}

				$f = 0 ;
				// Frames are arrays of...
				foreach( $frames as $frame ) {
					$b = 0;
					// ...bowls, which are pin counts.
					foreach( $frame as $count ) {

						$bowlRow = new Bowl();
						$bowlRow->playerid = $player->id;
						$bowlRow->gameid   = $game->id;
						$bowlRow->frame    = $f;
						$bowlRow->bowl     = $b;
						$bowlRow->count    = $count;
						$bowlRow->save();

						$b++; // increment bowl
					}
					$f++; // increment frame
				}
			
			} catch( Exception $e ) {
				$this->returnStatus(false, 'Couldn\'t save player data');
				Model::rollbackTransaction();
				exit;				
			}
		}

		// Everything's good - commit it all in
		Model::commitTransaction();
		$this->returnStatus( true, 'Game data saved' ); 

	}


}