$(function() {
	
	$('.btnBet').live('click', function() {
		  var idPlayer = this.id.substr(6);
		  var bet = $("#txtBet" + idPlayer).val();
		  var total = bet.substr(0,bet.length-1);
		  var face = bet.substr(bet.length-1);
		  betGame(idPlayer, total, face);
	});
	
	$('.btnFold').live('click', function() {
		  var idPlayer = this.id.substr(7);
		  foldGame(idPlayer);
	});
	
	$('.btnNewRound').live('click', function() {
		  newRound();
	});
	
	$("#qtd").change(function() {
		 startGame(this.value);
	});
	
});

var DICE_DESCRIPTION = {"0": "BAGO(S)", "2": "DUQUE(S)", "3": "TERNO(S)","4": "QUADRA(S)", "5": "QUINA(S)", "6": "SENA(S)" }

function loginGame(nome) {
	$.post("GameController.php", { "action": "login", "nome":nome }, function(data){
		if (data.status == "1") {
			window.location.reload();
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function carregarSalas() {
	$.post("GameController.php", { "action": "salas_abertas" }, function(data){
		if (data.status == "1") {
			var str = "<h2>Salas abertas no momento:</h2>";
			if (data.salas.length == 0) {
				str += "<ul><li>Não há salas abertas no momento.</li></ul>";
			} else {
				str += "<ul>";
				for (var i in data.salas) {
					str += "<li>Sala " + data.salas[i].id_sala + " (" + data.salas[i].qtd_atual + "/" + data.salas[i].total + ') - <a href="javascript: ;" class="entrar_sala" id="entrar_sala_'+ data.salas[i].id_sala +'">Entrar</a></li>';
				}
				str += "</ul>";
			}
			$("#salas_abertas").html(str);
		} else {
			alert(data.msg);			
		}
		window.setTimeout(function () { carregarSalas(); } ,10000);
	}, "json");	
}

function statusSala() {
	$.post("GameController.php", { "action": "status_sala" }, function(data){
		if (data.status == "1") {
			loadStatusSala(data.sala.jogadores);
			if (data.sala.status != 0) {
				window.location.reload();		
			} else {
				window.setTimeout(function () { statusSala(); } ,10000);
			}
		} else {
			alert(data.msg);
			window.location.reload();			
		}
		
	}, "json");	
}

function criarSala(qtd) {
	$.post("GameController.php", { "action": "criar_sala", "qtd":qtd }, function(data){
		if (data.status == "1") {
			window.location.reload();
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function entrarSala(sala) {
	$.post("GameController.php", { "action": "entrar_sala", "id_sala":sala }, function(data){
		if (data.status == "1") {
			window.location.reload();
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function startGame(qtd) {
	$.post("GameController.php", { "action": "start", "qtd":qtd }, function(data){
		if (data.status == "1") {
			alert("Jogo Iniciado!");
			window.location.reload();
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function betGame(player, total, face) {
	$.post("GameController.php", { "action":"bet", "player":player, "face":face, "total":total }, function(data){
		if (data.status == "1") {
			renderGame(data.game_json);
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function newRound() {
	$.post("GameController.php", { "action":"newRound" }, function(data){
		if (data.status == "1") {
			renderGame(data.game_json);
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function foldGame(player) {
	$.post("GameController.php", { "action":"fold", "player":player }, function(data){
		if (data.status == "1") {
			renderGame(data.game_json);
		} else {
			alert(data.msg);			
		}
	}, "json");	
}

function renderGame(oJson) {
	
	renderTable(oJson);
	
	renderActualRound(oJson);
	
	renderAllRounds(oJson);
}

function renderAllRounds(oJson) {
	var str = "";
	for (var i in oJson.rounds) {
		if (oJson.status == 2 && (i == (oJson.rounds.length - 1))) {
			str += "<b>Vencedor:</b> " + (oJson.rounds[i] + "<br/>");
		} else {
			str += "<b>Rodada " + (parseInt(i)+1) + ":</b> " + (oJson.rounds[i] + "<br/>");
		}
	}
	$("#alerts").html(str);
	
	if (oJson.status == 2) {
		$("#alerts").append('<a href="game_page.php?destroy=1">Novo Jogo</a>');
	} else {
		if (oJson.status == 1) { 
			$("#alerts").append('<a href="javascript:;" class="btnNewRound">Nova Rodada</a>');
		}
	}
}

function renderActualRound(oJson) {
	var str = "<b>Rodada Atual:</b><br>";
	for (var i in oJson.bets) {
		str += (oJson.players[oJson.bets[i].idPlayer].name + " - ");
		str += oJson.bets[i].hand.total;
		str += (" " + DICE_DESCRIPTION[oJson.bets[i].hand.face] + "<br/>"); 
	}
	$("#bets").html(str);
}

function loadStatusSala(oJson) {
	var str = "";
	$("#player_waitbox").html(str);
	for (var i in oJson) {
		str = $("#playerAreaModel").html();
		str = str.replace(/\[%ORDER%\]/g,parseInt(i)+1);
		str = str.replace(/\[%NAME%\]/g,oJson[i].nome);
		$("#player_waitbox").append(str);
	}
	
}

function renderTable(oJson) {
	var str = "";
	$("#game").html(str);
	
	if (oJson.status == 0) {
		for (var i in oJson.players) {
			if (i == oJson.betIdPlayer) {
				str = $("#betPlayerModel").html();
				str = str.replace(/\[%PLAYER_NAME%\]/g,oJson.players[i].name);
				str = str.replace(/\[%DICES%\]/g,oJson.players[i].dices);
				str = str.replace(/\[%ID_PLAYER%\]/g,i);
				$("#game").append(str);
			} else {
				str = $("#normalPlayerModel").html();
				str = str.replace(/\[%PLAYER_NAME%\]/g,oJson.players[i].name);
				str = str.replace(/\[%DICES%\]/g,oJson.players[i].dices);
				$("#game").append(str);
			}
		}
	} else {
		for (var i in oJson.previousPlayersHand) {
			str = $("#normalPlayerModel").html();
			str = str.replace(/\[%PLAYER_NAME%\]/g,oJson.players[i].name);
			str = str.replace(/\[%DICES%\]/g,oJson.players[i].dices);
			$("#game").append(str);
		}
	}
}