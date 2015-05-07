<!doctype html>
<html ng-app="bowlingApp">
<head>

	<title>WAS: Bowling Game Kata</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">

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

	<div ng-controller="GameController as gc" id="gameContainer">

		<a class="button" id="startGame" ng-show="! gc.game" ng-click="gc.startGame()">New Game</a>
		<div ng-show="gc.game">

			<!-- table containing score information -->
			<table class="scoresheet">

				<tr class="header">
					<td colspan="2">
						<h2>Bowling Scorecard</h2>
					</td>
				</tr>

				<!-- row for player -->
				<tr ng-class="{hilite: $index==gc.game.player, mobileOnly: true}" ng-repeat-start="player in gc.game.players">
					<th colspan="2" class="mobilePlayer">
						<a ng-click="gc.game.setPlayer($index)">{{player.name}}</a> {{player.score}}
					</th>
				</tr>
				<tr ng-class="{hilite: $index==gc.game.player}" ng-repeat-end>
					<th class="noMobile">
						<a ng-click="gc.game.setPlayer($index)">{{player.name}}</a>
					</th>
					<td>
						<table class="frames" ng-show="gc.game.started">
							<tr>
								<td ng-class="{hilite: $index==gc.game.frame, bowl: $index !=9, final: $index==9}" ng-repeat="frame in player.frames">
									<span class="bowl" ng-repeat="score in frame.scores track by $index">{{score}}</span>
									<span class="cumulative">{{frame.cumulativeScore}}</span>
								</td>
								<td class="total noMobile">{{player.score}}</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- row for game controls -->
				<tr class="controls" ng-show="! gc.game.started">
					<td colspan="2">
						<a class="button" ng-click="gc.addPlayer()">Add new player</a>
						<a class="button" ng-click="gc.game.start()" ng-show="gc.game.canStart">Start Game</a>
					</td>
				</tr>

			</table>

			<!-- control input interface -->
			<div id="interface" ng-show="gc.game.started && !gc.game.complete">
				<a class="button" ng-click="gc.addBowl($index)" ng-repeat="button in gc.scoreButtons" ng-class="{wide: ( $index == 0 || $index == 10 )}">{{button}}</a>
			</div>

		</div>

	</div>

</body>
</html>
