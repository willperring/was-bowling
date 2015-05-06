Player = function( name ) {

	var player = this;

	player.name   = name;
	player.frames = [];
	player.score  = 0;

	for( var i=1; i<=10; i++ ) {
		player.frames.push( new Frame((i==10 ? true : false)) );
	}
	
}

Player.prototype.getSaveData = function() {
	var rawData = [];
	for( var i=0; i<this.frames.length; i++ ) {
		var frame = this.frames[i];
		rawData.push( frame.getSaveData() );
	}

	return rawData;
}

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