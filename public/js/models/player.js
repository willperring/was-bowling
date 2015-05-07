/**
 * PLAYER Model
 *
 * @param string name The name of the player
 * @constructor
 */
Player = function( name ) {

	var player = this;

	player.name   = name; // @var string  Player name
	player.frames = [];   // @var [Frame] Frames for the player
	player.score  = 0;    // @var int     Player score (See FRAME Model Note on ANGULAR DATA BINDING)

	// Initialise ten frames
	for( var i=1; i<=10; i++ ) {
		player.frames.push( new Frame((i==10 ? true : false)) );
	}
	
}

/**
 * Get Raw save data for the player
 *
 * @return [[int]]
 */
Player.prototype.getSaveData = function() {
	var rawData = [];
	for( var i=0; i<this.frames.length; i++ ) {
		var frame = this.frames[i];
		rawData.push( frame.getSaveData() );
	}

	return rawData;
}

/**
 * Calculate the sumulative scores for each frame in the game
 *
 * Because of the way bowling scores work, certain results (spare/strike) can depend
 * on the results of subsequent bowls. Because of this, the scores need to be calculated from
 * within the scope of the player model, where other Frame models in the chain can be accessed
 * from. This function moves along the chain, passing in sibling objects to the individual frame 
 * method for calculating score
 *
 * @return void
 */
Player.prototype.updateCumulativeTotals = function() {

	var runningTotal = 0;
	for( var i=0; i<this.frames.length; i++ ) {

		var frame = this.frames[i];
		if( frame.bowls.length == 0 )
			continue;

		runningTotal += frame.getTotalScore( this.frames[i+1], this.frames[i+2] );
		frame.cumulativeScore = runningTotal;
	}

	this.score = runningTotal;
}
