<?php

Class AjaxController extends Controller {

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

		foreach( $payload['game'] as $playerHandle => $frames ) {

			$playerRows = Player::find_by(array('handle' => $playerHandle));

			try {
				if( count($playerRows) ) {
					$player = $playerRows[0];
					$player->gamecount++;
					$player->save();

				} else {
					$player = new Player();
					$player->handle = $playerHandle;
					$player->save();
				}

				$f = 0 ;
				foreach( $frames as $frame ) {
					$b = 0;
					foreach( $frame as $count ) {

						$bowlRow = new Bowl();
						$bowlRow->playerid = $player->id;
						$bowlRow->gameid   = $game->id;
						$bowlRow->frame    = $f;
						$bowlRow->bowl     = $b;
						$bowlRow->count    = $count;
						$bowlRow->save();

						$b++;
					}
					$f++;
				}
			
			} catch( Exception $e ) {
				$this->returnStatus(false, 'Couldn\'t save player data');
				Model::rollbackTransaction();
				exit;				
			}
		}

		Model::commitTransaction();
		$this->returnStatus( true, 'Game data saved' ); 

	}

	private function parsePostData() {
		$postdata = file_get_contents("php://input");
		if( empty($postdata) )
			return array();

		$payload  = json_decode( $postdata, true );
		return $payload;
	}

}