/**
 * FRAME Model
 *
 * The frame respresents a single turn for a player, and can contain up to 3 bowls
 *
 * @param boolean final True if this is the final frame in the game
 * @constructor
 */
Frame = function( final ) {

	/* ANGULAR DATA BINDING
	 * Some of these properties contain dynamic information that should generally be
	 * accessed through functions, but storing them additionaly in variables allows
	 * Angular to be notified of the update and alter the front end accordingly
	 */

	this.complete = false; // @var boolean True when the game has finished
	this.bowls    = [];    // @var [int]   Array of pins knocked over per bowl
	this.total    = 0;     // @var int     Total score for this frame
	this.isFinal  = final; // @var boolean True if this is the final frame for the player
	this.isSpare  = false; // @var boolean True if frame is completed as a spare
	this.isStrike = false; // @var boolean True if frame is completed as a strike

	// @var [mixed] 'Scores' vary from 'bowls' in that scores are padded with blanks for display
	this.scores   = this.getDisplayScores();

	// @var int Cumulative Score for this framw within the game. Set from within the Player model
	this.cumulativeScore = 0;
}

/**
 * Function to test to see whether a score can be added to a frame
 *
 * @param  int     score The score to try and see if can be added
 * @return boolean       True if score can be added
 */
Frame.prototype.canAdd = function( score ) {

	if( this.complete )
		return false;

	score = score || 1;
	if( !this.isFinal && this.getTotalPins() + score <= 10 )
		return true;

	if( this.isFinal && this.bowls.length < 3 )
		return true;

	return false;
}

/**
 * Add a score to a frame
 *
 * @param  int  score The score to add
 * @return void
 */
Frame.prototype.addBowl = function( score ) {

	this.bowls.push( score );
	this.total = this.getTotalPins();

	// If this is not final frame, test for completeness by score
	if( this.total >= 10 && !this.isFinal ) {
		this.complete = true;

		// Is spare or strike?
		if( this.bowls.length == 1 )
			this.isStrike = true;
		else
			this.isSpare = true;
	}

	// Then test for completeness by number of bowls
	if( this.isFinal && this.bowls.length >= 3 ) {
		this.complete = true;
	} else if( this.isFinal && this.bowls.length == 2 && this.total < 10 ) {
		this.complete = true;
	} else if( ! this.isFinal && this.bowls.length >= 2 ) {
		this.complete = true;
	}

	this.scores = this.getDisplayScores();
}

/**
 * Get a set of scores for display
 *
 * Display scores differ from actual scores in that they are padded with blanks for remaining
 * frames, and also spares and strikes are represented as 's' and 'S', respectively.
 * (Apart from the last frame, currently)
 * TODO: Add last frame functionality
 *
 * @return void
 */
Frame.prototype.getDisplayScores = function() {

	if( this.isStrike )
		return [' ', 'S'];

	if( this.isSpare )
		return this.bowls.slice(0,-1).concat(['s']);

	var blanks = (this.isFinal) ? [' ',' ',' '] : [' ',' '] ;
	return this.bowls.concat( blanks.slice(this.bowls.length) );
}

/**
 * Return the number of possible remaining pins for the next bowl
 *
 * This function is used to drive the disabling of the input buttons. Again, special consideration
 * has to be paid to the final frame of the game
 *
 * @return int Number of pins currently standing
 */
Frame.prototype.getRemainingPins = function() {
	
	var totalPins = this.getTotalPins();

	// 3rd frame of a final set (after spare or strike) - always 10
	if( this.isFinal && this.bowls.length == 2 )
		return 10;
	// If we got a strike on the first bowl of the last frame
	if( this.isFinal && this.bowls.length == 1 && totalPins == 10 )
		return 10;
	return 10 - totalPins;
}

/**
 * Count the number of pins knocked down in this frame
 *
 * @return int Number of pins knocked down
 */
Frame.prototype.getTotalPins = function() {
	var totalScore = 0;
	for( var i=0; i<this.bowls.length; i++ ) {
		totalScore += this.bowls[i];
	}
	return totalScore;
}

/**
 * Get the total score for this frame
 *
 * Score differs from total pins in that the score (in the event of a spare or a strike),
 * depends on the result of the next one or two BOWLS (rather than frames). With this in
 * mind, we need to pass in the next 2 frame objects to allow this calculation to happen
 *
 * @param  Frame next1 The immediately following frame
 * @param  Frame next2 The subsequent frame
 * @return int         The score for this frame
 */
Frame.prototype.getTotalScore = function( next1, next2 ) {
	
	var score = this.getTotalPins();

	// Spare and stike both get next ball
	if( this.isSpare || this.isStrike )
		score += ( next1 && next1.bowls.length ) ? next1.bowls[0] : 0 ;

	if( this.isStrike ) {
		// Strike gets the second ball of the next frame if it's NOT a strike
		if( next1 && next1.bowls.length > 1 )
			score += next1.bowls[1];
		// Or gets the first bowl of the subsequent frame if it is.
		else if( next1 && next1.isStrike && next2 && next2.bowls.length > 0 )
			score += next2.bowls[0];
	}

	return score;
}

/**
 * Return the raw data about the frame for saving
 *
 * @return [int] Bowls played in this frame
 */
Frame.prototype.getSaveData = function() {
	return this.bowls;
}
