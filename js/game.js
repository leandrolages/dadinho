var DICE_DESCRIPTION = {
	"0": "BAGO(S)", "2": "DUQUE(S)", "3": "TERNO(S)",
	"4": "QUADRA(S)", "5": "QUINA(S)", "6": "SENA(S)" 	
}

function Hand(t, f) {
	this.total = t;
	this.face = f;
	
	this.toNumber = function() {
		return parseInt(String(this.total) + String(this.face));		
	}
}

function Bet(p, h) {
	this.idPlayer = p;
	this.hand = h;
}

function Player(n) {
	
	this.name = n;
	this.dices = [];
	
	this.getRandomDices = function(numDices) {
		this.dices = [];
		for (var i = 0; i < numDices; i++) {
			this.dices[i] = Math.floor(Math.random()*6) + 1;
		}
	}
	
	this.numberTotal = function(number) {
		var total = 0;
		for (var i in this.dices) {
			if (this.dices[i] == number) total++;
		}
		return total;
	}
	
}


function Game() {
	
	this.players = [];
	this.bets = [];
	this.betIdPlayer = 0;
	this.rounds = 0;
	
	// 0 - ultimo pedido de bago
	// 1 - ultimo pedido normal
	this.lastBetByType = function(type) {
		if (this.bets.length > 0) {
			var last = 0;
			for (var i = this.bets.length; i > 0; i--) {
				var strBet = String(this.bets[i-1].hand);
				if (type == "0") {
					if (this.bets[i-1].hand.face == 0) {
						last = this.bets[i-1].hand.toNumber();
						break;
					}
				} else {
					if (this.bets[i-1].hand.face != "0") {
						last = this.bets[i-1].hand.toNumber();
						break;
					}
				}
				
			}
			return last; 
		} else {
			return 0;
		}
	}
	
	this.nextTurn = function() {
		var nextTurn = parseInt(this.betIdPlayer) + 1;
		do {
			if (nextTurn >= this.players.length) nextTurn = 0;
			if (this.players[nextTurn].dices.length > 0) {
				return nextTurn;
			}
			nextTurn++;
		} while (true);
	}
	
	this.changeTurn = function() {
		this.betIdPlayer = this.nextTurn();
		this.render();
	}
	
	this.start = function(numPlayers) {
		this.players = [];
		this.bets = [];
		for (var i = 0; i < numPlayers; i++) {
			this.players[i] = new Player("Player " + (i+1));
			this.players[i].getRandomDices(3);
		}
		$("#alerts").html("");
		this.render();
		
	}
	
	this.newRound = function() {
		if (this.betIdPlayer == this.nextTurn()) {
			alert(this.players[this.betIdPlayer].name + " venceu a guerra dos dados!");
			this.start(3);
		} else {
			this.bets = [];
			for (var i in this.players) {
				this.players[i].getRandomDices(this.players[i].dices.length);
			}
			this.render();
		}
	}
	
	this.bet = function(idPlayer, hand) {
		
		if (isNaN(hand.toNumber())) {
			alert("Aposta inválida!");
			return false;
		}
		
		if (hand.total == 0) {
			alert("Aposta inválida!!");
			return false;
		}
		
		if (hand.face != 0 && (hand.face < 2 || hand.face > 6)) {
			alert("Aposta inválida!!!");
			return false;
		}
		
		if (idPlayer != this.betIdPlayer) {
			alert("Não é a vez do Jogador '" + this.players[idPlayer].name + "'!");
			return false
		}
		
		// se for bago
		if (hand.face == 0) {
			
			// verifica se é maior que ultimo bago
			var last = this.lastBetByType("0");
			if (hand.toNumber() <= last) {
				alert("Pedido menor ou igual ao número de bagos já jogado!");
				return false;
			}
			
			// verifica se é maior que a metade da ultima jogada
			var last = this.lastBetByType("1");
			var half = Math.round(last/20) * 10;
			if (hand.toNumber() < half) {
				alert("Número de bagos deve ser a metade da última jogada!");
				return false;
			}
			
		} else {
			
			// verifica se é maior que o dobro do ultimo bago
			var last = this.lastBetByType("0");
			if (hand.toNumber() < (last * 2)) {
				alert("Pedido menor que o dobro de bagos já jogado!");
				return false;
			}
			
			// verifica se é maior que o ultimo dado jogado
			var last = this.lastBetByType("1");
			if (hand.toNumber() <= last) {
				alert("Número inferior ao último jogado!");
				return false;
			}
			
		}
		
		this.bets[this.bets.length] = new Bet(idPlayer, hand);
				
		this.changeTurn();
		return true;
		
	}
	
	this.fold = function(idPlayer) {
		
		if (this.bets.length == 0) {
			alert("Não é possível desconfiar na primeira rodada!");
			return false;
		}
		if (idPlayer != this.betIdPlayer) {
			alert("Não é sua vez de desconfiar.")
			return false;
		}
		
		var betAmount = this.bets[this.bets.length - 1].hand.total
		var betNumber = this.bets[this.bets.length - 1].hand.face;
		
		var roundMsg = "";
		
		var previousPlayer = this.bets[this.bets.length - 1].idPlayer;
		if (betAmount <= this.numberTotal(betNumber)) {
			
			// previous wins
			roundMsg += this.players[previousPlayer].name + ' pediu ' + betAmount + ' ' + DICE_DESCRIPTION[betNumber] + ' para '+ this.players[idPlayer].name + '. ';
			roundMsg += "Deu " + this.numberTotal(betNumber) + ' ' + DICE_DESCRIPTION[betNumber] + '. ' + this.players[previousPlayer].name + ' venceu!';
			
			// withdraw a dice from the looser
			var lastKey = this.players[idPlayer].dices.length - 1;
			this.players[idPlayer].dices.splice(lastKey,1);
			this.betIdPlayer = idPlayer;
			if (this.players[idPlayer].dices.length == 0) {
				this.betIdPlayer = this.nextTurn();
			}
			
		} else {
			
			// desafiador wins
			roundMsg += this.players[previousPlayer].name + ' pediu ' + betAmount + ' ' + DICE_DESCRIPTION[betNumber] + ' para '+ this.players[idPlayer].name + '. ';
			roundMsg += "Deu " + this.numberTotal(betNumber) + ' ' + DICE_DESCRIPTION[betNumber] + '. ' + this.players[idPlayer].name + ' venceu!';
			
			// withdraw a dice from the looser
			var lastKey = this.players[previousPlayer].dices.length - 1;
			this.players[previousPlayer].dices.splice(lastKey,1);
			this.betIdPlayer = previousPlayer;
			if (this.players[previousPlayer].dices.length == 0) {
				this.betIdPlayer = this.nextTurn();
			}
			
		}
		
		$("#alerts").append("Rodada " + (++this.rounds) + ": " + roundMsg + "<br/>");
		
		this.newRound();
		
	}
	
	this.numberTotal = function(number) {
		var total = 0; var bagos = 0;
		for (var i in this.players) {
			total += this.players[i].numberTotal(number);
			bagos += this.players[i].numberTotal(1);
		}
		if (number == 0) {
			return bagos;
		} else {
			return (total > 0) ? (total + bagos) : total;
		}
		
	}
	
	this.render = function() {
		var str = "";
		$("#game").html(str);
		for (var i in this.players) {
			if (i == this.betIdPlayer) {
				str = $("#betPlayerModel").html();
				str = str.replace(/\[%PLAYER_NAME%\]/g,this.players[i].name);
				str = str.replace(/\[%DICES%\]/g,this.players[i].dices);
				str = str.replace(/\[%ID_PLAYER%\]/g,i);
				$("#game").append(str);
			} else {
				str = $("#normalPlayerModel").html();
				str = str.replace(/\[%PLAYER_NAME%\]/g,this.players[i].name);
				str = str.replace(/\[%DICES%\]/g,this.players[i].dices);
				$("#game").append(str);
			}
		}
		var str = "";
		for (var i in this.bets) {
			str += (this.players[this.bets[i].idPlayer].name + " - ");
			str += this.bets[i].hand.total;
			str += (" " + DICE_DESCRIPTION[this.bets[i].hand.face] + "<br/>"); 
		}
		$("#bets").html(str);
	}
	
}

$(function() {
	
	var game = new Game();
	game.start(3);
	
	$('.btnBet').live('click', function() {
		  var idPlayer = this.id.substr(6);
		  var bet = $("#txtBet" + idPlayer).val();
		  var hand = new Hand(bet.substr(0,bet.length-1), bet.substr(bet.length-1));
		  game.bet(idPlayer, hand);
	});
	
	$('.btnFold').live('click', function() {
		  var idPlayer = this.id.substr(7);
		  game.fold(idPlayer);
	});
	
});
