/**
 * GAME Model
 *
 * @constructor
 */
Game = function() {

	var game = this;

	game.started  = false; // @var boolean  True when the game has been started (players cannot be added after that)
	game.complete = false; // @var boolean  True when the game has been filled
	game.canStart = false; // @var boolean  True when gane can start (See frame model for not on ANGULAR DATA BINDING)
	game.players  = [];    // @var [Player] Array of players in the game
	
	game.frame  = null; // @var int Current Frame
	game.player = null; // @var int Current Player

	game.pinsStanding = 10; // @var int Pins remaining
	
};

/**
 * Add a player to the game
 *
 * @return void
 */
Game.prototype.addPlayer = function( name ) {
	this.players.push( new Player(name) );
	this.canStart = this.players.length > 0;
};

/**
 * Set the currently active player
 *
 * @return void
 */
Game.prototype.setPlayer = function( index ) {
	if( this.started )
		this.player = index;
}

/**
 * Update the pins standing
 *
 * This is called as the final stage of adding a new score. Rather than be accessible through
 * a function, this sets the information into the model to be collected by angular
 *
 * @return void
 */
Game.prototype.updatePinsStanding = function() {
	this.pinsStanding = this.players[ this.player ].frames[ this.frame ].getRemainingPins();
}

/**
 * Advance the current player
 *
 * This method will automativally advance the frame if all players in the
 * current frame have completed their bowls
 *
 * @return void
 */
Game.prototype.advancePlayer = function() {

	for( var i=0; i<this.players.length; i++ ) {
		if( ! this.players[i].frames[this.frame].complete )
			return this.setPlayer( i );
	}

	if( this.advanceFrame() ) {
		this.advancePlayer();
	} else {
		this.completeGame();
	}
}

/**
 * Advance the current frame
 *
 * This method is usually called from within Game.advancePlayer(), so
 * unless used for non-standard purposes, should not be called directly
*
 * @return void
 */
Game.prototype.advanceFrame = function() {
	if( this.frame >= 9 )
		return false;
	this.frame++;
	return true;
}

/**
 * Start the game
 *
 * @return void
 */
Game.prototype.start = function() {
	if( ! this.players.length )
		return alert('Not enough players');
	this.started = true;
	this.frame   = 0;
	this.player  = 0;
}

/**
 * Add a bowl to the game
 *
 * @param int score The number of pins knocked over
 */
Game.prototype.addBowl = function( score ) {
	
	var player = this.players[ this.player ],
	    frame  = player.frames[ this.frame ];

	if( frame.canAdd( score ) ) {
		frame.addBowl( score );
		player.updateCumulativeTotals();

		if( frame.complete )
			this.advancePlayer();

		if( !this.complete )
			this.updatePinsStanding();
	}
	else alert('That score is too high to be added to the current frame');
}

/**
 * Get Raw save data
 *
 * @return {string:[mixed]} raw save data
 */
Game.prototype.getSaveData = function() {

	var rawData = {};

	for( var i=0; i<this.players.length; i++ ) {
		var player = this.players[i];
		rawData[ player.name ] = player.getSaveData();
	}

	return rawData;
}

/**
 * Terminate the game
 *
 * @return void
 */
Game.prototype.completeGame = function() {
	this.complete = true;
	this.canStart = false;
	this.frame    = null;
	this.player   = null;
}

