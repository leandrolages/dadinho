<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="/dadinho/css/layout.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/dadinho/js/jquery.js"></script>
	<script type="text/javascript" src="/dadinho/js/game.js"></script>
</head>

<body>
	
	<div id="game" style="padding: 5px; margin: 5px; border: 1px solid #000;"></div>
	<div id="bets" style="padding: 5px; margin: 5px; border: 1px solid #000;"></div>
	<div id="alerts" style="padding: 5px; margin: 5px; border: 1px solid #000;"></div>
	
	<div id="models" style="display:none;">
		<div id="betPlayerModel">
			<p>
			[%PLAYER_NAME%] - [%DICES%] - 
			<input type="text" size="2" class="txtBet" id="txtBet[%ID_PLAYER%]" /> - 
			<input type="button" class="btnBet" id="btnBet[%ID_PLAYER%]" value="Apostar" /> - 
			<input type="button" class="btnFold" id="btnFold[%ID_PLAYER%]" value="Desconfiar" />
			</p>
		</div>
		<div id="normalPlayerModel">
			<p>
			[%PLAYER_NAME%] - [%DICES%]
			</p>
		</div>
	</div>
	
</body>
</html>