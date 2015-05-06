Frame = function( final ) {

	this.complete = false;
	this.bowls    = [];
	this.total    = 0;
	this.isFinal  = final;

	this.scores   = this.getDisplayScores();
}

Frame.prototype.canAdd = function( score ) {
	if( this.complete )
		return false;
	score = score || 1;
	return this.getTotalScore() + score <= 10;
}

Frame.prototype.addBowl = function( score ) {

	this.bowls.push( score );
	this.total = this.getTotalScore();
	this.scores = this.getDisplayScores();

	if( this.total >= 10 )
		this.complete = true;

	if( this.isFinal && this.bowls.length ) {
		// TODO
	} else if( !this.isFinal && this.bowls.length >= 2 ) {
		this.complete = true;
	}
}

Frame.prototype.getDisplayScores = function() {
	var blanks = (this.isFinal) ? [' ',' ',' '] : [' ',' '] ;
	console.log( 'scores', this.bowls.concat( blanks.slice(this.bowls.length)) );
	return this.bowls.concat( blanks.slice(this.bowls.length) );
}

Frame.prototype.getTotalScore = function() {
	var totalScore = 0;
	for( var i=0; i<this.bowls.length; i++ ) {
		totalScore += this.bowls[i];
	}
	return totalScore;
}