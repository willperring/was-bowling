Frame = function( final ) {

	this.complete = false;
	this.bowls    = [];
	this.total    = 0;
	this.isFinal  = final;
	this.isSpare  = false;
	this.isStrike = false;

	this.scores   = this.getDisplayScores();

	this.cumulativeScore = 0;

	console.log( 'final', this.isFinal );
}

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

Frame.prototype.addBowl = function( score ) {

	this.bowls.push( score );
	this.total = this.getTotalPins();
	this.scores = this.getDisplayScores();

	if( this.total >= 10 && !this.isFinal ) {
		this.complete = true;

		if( this.bowls.length == 1 )
			this.isStrike = true;
		else
			this.isSpare = true;
	}

	if( this.isFinal && this.bowls.length >= 3 ) {
		this.complete = true;
	} else if( !this.isFinal && this.bowls.length >= 2 ) {
		this.complete = true;
	}
}

Frame.prototype.getDisplayScores = function() {
	var blanks = (this.isFinal) ? [' ',' ',' '] : [' ',' '] ;
	return this.bowls.concat( blanks.slice(this.bowls.length) );
}

Frame.prototype.getTotalPins = function() {
	var totalScore = 0;
	for( var i=0; i<this.bowls.length; i++ ) {
		totalScore += this.bowls[i];
	}
	return totalScore;
}

Frame.prototype.getTotalScore = function( next1, next2 ) {
	
	var score = this.getTotalPins();

	// Spare and stike both get next ball
	if( this.isSpare || this.isStrike )
		score += ( next1 && next1.bowls.length ) ? next1.bowls[0] : 0 ;

	if( this.isStrike ) {
		if( next1 && next1.bowls.length > 1 )
			score += next1.bowls[1];
		else if( next1 && next1.isStrike && next2 && next2.bowls.length > 0 )
			score += next2.bowls[0];
	}

	return score;
}

Frame.prototype.getSaveData = function() {
	return this.bowls;
}
