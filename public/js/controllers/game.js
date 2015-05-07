angular.module('bowlingApp', [])

	/**
	* Game Controller Class
	*
	* Main Angular controller for interacting with the game board.
	* Fairly self explanatory here. 
	*/

	.controller('GameController', ['$scope', '$http', function( $scope, $http ) {

		/** @var Reference to self for use inside other method factories */
		var controller = this;

		/** @var Reference to the Game object */
		controller.game = false;
		
		/** @var Array of buttons used to enter bowl information */
		controller.scoreButtons = ['miss', 1, 2, 3, 4, 5, 6, 7, 8, 9, 'strike'];

		/**
		 * Start a new Game
		 */
		controller.startGame = function() {
			controller.game = new Game();

			// TODO: remove dev code
			//controller.game.addPlayer('Bob');
			//controller.game.addPlayer('Steve');
		};

		/**
		 * Add a player to the game
		 *
		 * Currently this prompts for a username rather than extracting from a social network.
		 * This functionality is yet to be added.
		 * Players cannot be added to a game that has not yet been initialised.
		 */
		controller.addPlayer = function() {
			if( !controller.game )
				return false;

			var name = prompt('Enter player name');
			if( name.trim() == '' )
				return false;

			controller.game.addPlayer( name );
		};

		/**
		 * Add a bowl to the scorecard
		 *
		 * Information about the game state ( current player, frame, etc ) is stored within the 
		 * Game object, so all that's needed to be passed in is the amount of pins knocked down
		 */
		controller.addBowl = function( score ) {
			controller.game.addBowl( score );

			// We save the game data via ajax once complete
			if( controller.game.complete ) {
				
				var request = $http({
					method : 'post',
					url    : '/save',
					data   : {
						game: controller.game.getSaveData()
					}
				});

				request.success( function(response) {
					console.log(response);
				}).error( function() {
					console.log('something went wrong');
				});
				
			}
		}

	}]);