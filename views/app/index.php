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

			<!-- table containing score information -->
			<table class="scoresheet">

				<!-- row for player -->
				<tr ng-class="{hilite: $index==gc.game.player}" ng-repeat="player in gc.game.players">
					<td>
						<a href="#" ng-click="gc.game.setPlayer($index)">{{player.name}}</a>
					</td>
					<td>

						<!-- table containing game frames -->
						<table class="frames" ng-show="gc.game.started">
							<tr>
								<td ng-class="{hilite: $index==gc.game.frame}" ng-repeat="frame in player.frames">
									<span class="bowl" ng-repeat="score in frame.scores track by $index">{{score}}</span>
									<span class="cumulative">{{frame.cumulativeScore}}</span>
								</td>
								<td>{{player.score}}</td>
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

			<!-- control input interface -->
			<div id="interface" ng-show="gc.game.started && !gc.game.complete">
				<a href="#" class="button" ng-click="gc.addBowl($index)" ng-repeat="button in gc.scoreButtons">{{button}}</a>
			</div>

		</div>

	</div>

</body>
</html>
