angular.module('bowlingApp', [])
	.controller('GameController', ['$scope', function( $scope ) {

		var controller = this;

		controller.game = false;

		controller.startGame = function() {
			controller.game = new Game();
		};

		controller.addPlayer = function() {
			if( !controller.game )
				return false;

			var name = prompt('Enter player name');
			controller.game.addPlayer( name );
		}

	}]);