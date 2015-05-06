angular.module('bowlingApp', [])
	.controller('GameController', ['$scope', function( $scope ) {

		var controller = this;

		controller.game         = false;
		controller.scoreButtons = ['miss', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

		controller.startGame = function() {
			controller.game = new Game();

			// TODO: remove dev code
			controller.game.addPlayer('Bob');
			controller.game.addPlayer('Steve');
			controller.game.addPlayer('Chris');
		};

		controller.addPlayer = function() {
			if( !controller.game )
				return false;

			var name = prompt('Enter player name');
			controller.game.addPlayer( name );
		};

		controller.addBowl = function( score ) {
			controller.game.addBowl( score );
		}

	}]);