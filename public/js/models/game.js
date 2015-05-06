Game = function() {

	var game = this;

	game.started  = false;
	game.complete = false;
	game.canStart = false;
	game.players  = [];
	
	game.frame  = null;
	game.player = null;
	
};

Game.prototype.addPlayer = function( name ) {
	this.players.push( new Player(name) );
	this.canStart = this.players.length > 0;
};

Game.prototype.setPlayer = function( index ) {
	if( this.started )
		this.player = index;
}

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

Game.prototype.advanceFrame = function() {
	if( this.frame >= 9 )
		return false;
	this.frame++;
	return true;
}

Game.prototype.start = function() {
	if( ! this.players.length )
		return alert('Not enough players');
	this.started = true;
	this.frame   = 0;
	this.player  = 0;
}

Game.prototype.addBowl = function( score ) {
	
	var player = this.players[ this.player ],
	    frame  = player.frames[ this.frame ];

	if( frame.canAdd( score ) ) {
		frame.addBowl( score );
		player.updateCumulativeTotals();

		if( frame.complete )
			this.advancePlayer();
	}
	else alert('That score is too high to be added to the current frame');
}

Game.prototype.getSaveData = function() {

	var rawData = {};

	for( var i=0; i<this.players.length; i++ ) {
		var player = this.players[i];
		rawData[ player.name ] = player.getSaveData();
	}

	return rawData;
}

Game.prototype.completeGame = function() {
	this.complete = true;
	this.canStart = false;
	this.frame    = null;
	this.player   = null;
}

