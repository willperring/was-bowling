Game = function() {

	var game = this;

	game.started  = false;
	game.canStart = false;
	game.players  = [];
	
	game.frame  = null;
	game.player = null;
	
};

Game.prototype.addPlayer = function( name ) {
	this.players.push( new Player(name) );
	this.canStart = this.players.length > 0;
};

Game.prototype.start = function() {
	if( ! this.players.length )
		return alert('Not enough players');
	this.started = true;
	this.frame   = 0;
	this.player  = 0;
}

