<!doctype html>
<html ng-app="bowlingApp">
<head>

	<title>WAS: Bowling Game Kata</title>

	<link rel="stylesheet" type="text/css" href="css/main.css">

	<!-- 3rd Party Libararies -->
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>

	<!-- Object Models -->
	<script src="js/models/game.js"></script>
	<script src="js/models/player.js"></script>
	<script src="js/models/frame.js"></script>

	<!-- Angular Controllers -->
	<script src="js/controllers/game.js"></script>

</head>

<body>

	<h1>WeAreSocial: Bowling Kata</h1>

	<div ng-controller="GameController as gc">

		<a href="#" class="button" ng-show="! gc.game" ng-click="gc.startGame()">New Game</a>
		<div ng-show="gc.game">

			<table class="scoresheet">

				<!-- row for player -->
				<tr class="player" ng-repeat="player in gc.game.players">
					<td>{{player.name}}</td>
					<td>

						<!-- table containing game frames -->
						<table class="frames" ng-show="gc.game.started">
							<tr ng-class="{hilite: $index==gc.game.player}">
								<td ng-class="{hilite: $index==gc.game.frame}" ng-repeat="frame in player.frames">0</td>
							</tr>
						</table>

					</td>
				</tr>

				<!-- row for game controls -->
				<tr class="controls" ng-show="! gc.game.started">
					<td colspan="2">
						<a href='#' class="button" ng-click="gc.addPlayer()">Add new player</a>
						<a href='#' class="button" ng-click="gc.game.start()" ng-show="gc.game.canStart">Start Game</a>
					</td>
				</tr>

			</table>

		</div>

	</div>

</body>
</html>
