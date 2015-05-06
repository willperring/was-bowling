Player = function( name ) {

	var player = this;

	player.name   = name;
	player.frames = [];

	for( var i=1; i<=10; i++ ) {
		player.frames.push( new Frame(i==10) );
	}
	
}