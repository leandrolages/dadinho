<?php 
include(dirname(__FILE__) . "/GameModel.php");
include(dirname(__FILE__) . "/Game.php");
session_start();
if ($_GET['destroy']) {
   session_destroy();
   header("location: game_page.php");
   die;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="/dadinho/css/layout.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/dadinho/js/jquery.js"></script>
	<script type="text/javascript" src="/dadinho/js/gamephp.js"></script>
        <title>Dadinho Online</title>
</head>

<body>
	
	<div id="topo">
		<img src="/dadinho/img/dado.gif"></img><h1>Dadinho Online</h1>
	</div>
	
	<div id="corpo">
	
	<?php if (!isset($_SESSION['jogador'])) { // usuario nao logado... ?>
	
	<script type="text/javascript">
	$(function() {
		$("#login").focus();
		$("#frmlogin").submit(function() {
			loginGame($("#login").val());
			return false;
		});
	});
	</script>
	
	<div id="loginGame">
		<form id="frmlogin">
		Escolha seu apelido: 
		<input type="text" id="login" size="25" maxlength="25" />
		<button type="submit">Entrar</button>
		</form> 
	</div>
	
	<?php } elseif ($_SESSION['jogador']['id_sala'] == null) { // usuario logado mas sem sala no momento...?>
	
	<script type="text/javascript">
	$(function() {
		$("#frmsala").submit(function() {
			criarSala($("#sala").val());
			return false;
		});

		$('.entrar_sala').live('click', function() {
			var id = $(this).attr("id").substr(12);
			entrarSala(id);
		});
		
		carregarSalas();
		
	});
	</script>
	<div id="criarsala">
		<form id="frmsala">
		Criar uma sala nova com : 
		<select id="sala">
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
		</select> lugares
		<button type="submit">Criar</button>
		</form> 
	</div>
	<div id="salas_abertas"></div>
	
	<?php } elseif ($_SESSION['jogador']['id_sala'] > 0 && $_SESSION['sala']['status'] == 0) { // aguardando inicio do jogo?>
	
	<script type="text/javascript">
	$(function() {
		statusSala();
	});
	</script>
	<div id="game_board">
		
		<div id="player_waitbox"></div>
		
		<div id="models" style="display:none;">
			<div id="playerAreaModel" class="player_area">
				<div class="player_area">
   				<div class="headline">[%ORDER%] - [%NAME%]</div>
   				<div class="player_dices">
   					<img src="/dadinho/q.png"/>
   					<img src="/dadinho/q.png"/>
   					<img src="/dadinho/q.png"/>
   				</div>
   			</div>
			</div>
		</div>
	   <?php //echo "<xmp>"; print_r($_SESSION['jogador']); print_r($_SESSION['sala']); echo "</xmp>";?>
	</div>
	
	<?php } elseif ($_SESSION['jogador']['id_sala'] > 0 && $_SESSION['sala']['status'] > 0) { // jogo rolando ou finalizado?>
	<script type="text/javascript">
	$(function() {
		
	});
	</script>
	<div id="game_board">
		
		<div id="teclado">
			<form>
			Teclado: 
			<?php for ($i = 0; $i < 10; $i++) { ?><span><?php echo $i?></span><?php } ?>
			<?php for ($i = 1; $i <= 6; $i++) { ?><img src="/dadinho/img/dice<?php echo $i?>.gif" align="top"/><?php } ?>
			<input type="text" id="string_bet" value="367 TERNO(S)" disabled="disabled" size="15" />
			<button type="submit">Apostar</button>
			<button type="submit">Desconfiar</button>
   		<input type="hidden" id="bet" value=""/>
   		</form>
		</div>
		
		<div id="player_box">
			
			<?php 
			$total = 8;
			for ($i = 0; $i < $total; $i++) { 
			?>
			
			<div class="player_area">
				<div class="headline"><?php echo $i+1?> - Jose Pablo</div>
				<div class="player_dices">
					<img src="/dadinho/4.png"/>
					<img src="/dadinho/4.png"/>
				</div>
			</div>
			<?php 
			}
			?>
		</div>
		<div id="bet_box">
			<h4>Rodada atual: </h4>
			<p>- Pablo pediu 5 quinas.</p>
			<p>- Pablo pediu 6 quinas.</p>
			<p>- Pablo pediu 5 quinas.</p>
			<p>- Pablo pediu 6 quinas.</p>
			<p>- Pablo pediu 5 quinas.</p>
			<p>- Pablo pediu 6 quinas.</p>
		</div>
		<div id="history_box">
			<h4>Historico do Jogo: </h4>
			<p><strong>Rodada 1: </strong>Pablo pediu 3 quina(s) para Leandro. Deu 4 quina(s). Pablo venceu.</p>
		</div>
			
	   <?php //echo "<xmp>"; print_r($_SESSION['jogador']); print_r($_SESSION['sala']); echo "</xmp>";?>
	</div>
	<?php } elseif (1 == 0) { ?>
	<script type="text/javascript">
	$(function() {
		
	});
	</script>
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
	
	<?php } else { ?>
	<?php echo "<xmp>"; print_r($_SESSION['jogador']); echo "</xmp>";?>
	<?php } ?>
	
	</div>
	
	<div id="rodape">
      Todos os direitos reservados.	
	</div>
</body>
</html>