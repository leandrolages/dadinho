<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript">
		
	
	</script>
	
	<style type="text/css">
	
	#game_table {
		border:1px solid #000;
		border-collapse: collapse;
		width: 100%;
	}
	
	#game_table td {
		padding: 0;
		border:1px solid #000;
		height: 100px;
	}
	
	.corner {
		width: 15%;
		
	}
	
	.player_box {
		width: 15%;
		vertical-align: top;
	}
	
	.game_table {
		width: 45%;
	}
	
	.game_board {
		width: 25%;
		vertical-align: top;
	}
	
	.game_board p {
		padding-left: 5px;
	}

	.player_name {
		font-size: 14px;
		font-weight: bold;
		text-align: center;
		padding: 5px;
		border-bottom: 1px solid #000;
	}
	
	.player_game {
		text-align: center;
		padding: 5px;
		border-bottom: 1px solid #000;
	}
	
	.player_move {
		font-size: 12px;
		font-weight: italic;
		text-align: center;
		padding: 5px;
	}
	
	</style>
	
</head>



<body>
	
	<table id="game_table">
		<tr>
			<td class="corner"></td>
			<td class="player_box" id="player_box_1">
   				<div class="player_name">Leandro</div>
   				<div class="player_game">
   					<img src="1.png" class="player_dice_1"/>
   					<img src="1.png" class="player_dice_2"/>
   				</div>
   				<div class="player_move">3 bago(s)</div>
			</td>
			<td class="player_box" id="player_box_2">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>&nbsp;
   			</div>
   			<div class="player_move">3 bago(s)</div>
			</td>
			<td class="player_box" id="player_box_3">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>
   				<img src="q.png" class="player_dice_2"/>
   				<img src="q.png" class="player_dice_3"/>
   			</div>
   			<div class="player_move">3 bago(s)</div>			
			</td>
			<td class="corner"></td>
			<td rowspan="3" class="game_board">
				<p><strong>Quadro de Jogadas da Rodada: </strong></p>
				<p>Rogerio: 3 bago(s)</p>
				<p>Leandro: 6 quina(s)</p>
			</td>
		</tr>
		<tr>
			<td class="player_box" id="player_box_4">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>
   				<img src="q.png" class="player_dice_2"/>
   				<img src="q.png" class="player_dice_3"/>
   			</div>
   			<div class="player_move">3 bago(s)</div>
			</td>
			<td colspan="3" class="game_table"></td>
			<td class="player_box" id="player_box_5">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>
   				<img src="q.png" class="player_dice_2"/>
   				<img src="q.png" class="player_dice_3"/>
   			</div>
   			<div class="player_move">3 bago(s)</div>
			</td>
		</tr>
		<tr>
			<td class="corner"></td>
			<td class="player_box" id="player_box_6">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>
   				<img src="q.png" class="player_dice_2"/>
   				<img src="q.png" class="player_dice_3"/>
   			</div>
   			<div class="player_move">3 bago(s)</div>
			</td>
			<td class="player_box" id="player_box_7">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>
   				<img src="q.png" class="player_dice_2"/>
   				<img src="q.png" class="player_dice_3"/>
   			</div>
   			<div class="player_move">3 bago(s)</div>
			</td>
			<td class="player_box" id="player_box_8">
				<div class="player_name">Leandro</div>
   			<div class="player_game">
   				<img src="q.png" class="player_dice_1"/>
   				<img src="q.png" class="player_dice_2"/>
   				<img src="q.png" class="player_dice_3"/>
   			</div>
   			<div class="player_move">3 bago(s)</div>
			</td>
			<td class="corner"></td>
		</tr>
	</table>
	
</body>

</html>