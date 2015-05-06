angular.module('bowlingApp', [])
	.controller('GameController', ['$scope', '$http', function( $scope, $http ) {

		var controller = this;

		controller.game         = false;
		controller.scoreButtons = ['miss', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

		controller.startGame = function() {
			controller.game = new Game();

			// TODO: remove dev code
			controller.game.addPlayer('Bob');
			controller.game.addPlayer('Steve');
		};

		controller.addPlayer = function() {
			if( !controller.game )
				return false;

			var name = prompt('Enter player name');
			controller.game.addPlayer( name );
		};

		controller.addBowl = function( score ) {
			controller.game.addBowl( score );

			if( controller.game.complete ) {
				console.log( 'game complete', controller.game.getSaveData() );
				
				var request = $http({
					method : 'post',
					url    : '/save',
					data   : controller.game.getSaveData()
				});

				request.success( function(response) {
					console.log(response);
				}).error( function() {
					console.log('something went wrong');
				});
				
			}
		}

	}]);