<?php

class Hand {
   
   public $total;
   public $face;
   
   public function __construct($total, $face) {
      $this->total = $total;
      $this->face = $face;
   }
   
   public function toNumber() {
      return (int) ($this->total . $this->face);
   }
   
}

class Bet {
   
   public $idPlayer;
   public $hand;
   
   public function __construct($idPlayer, Hand $hand) {
      $this->idPlayer = $idPlayer;
      $this->hand = $hand;
   }
   
}

class Player {
   
   public $name;
   public $dices = array();
   
   public function __construct($name) {
      $this->name = $name;
   }
   
   public function getRandomDices($numDices) {
      $this->dices = array();
      for ($i = 0; $i < $numDices; $i++) {
         $this->dices[$i] = mt_rand(1,6);
      }
   }
   
   public function numberTotal($number) {
      $total = 0;
      foreach ($this->dices as $d) {
         if ($d  == $number) $total++;
      }
      return $total;
   }
}

class Game {
   
   static $DICE_DESCRIPTION = array("0" => "BAGO(S)", "2"=> "DUQUE(S)", "3"=> "TERNO(S)","4"=> "QUADRA(S)", "5"=> "QUINA(S)", "6"=> "SENA(S)");
   
   public $players = array();
   public $previousPlayersHand = array();
   public $bets = array();
   public $betIdPlayer = 0;
   public $rounds = array();
   public $status = 0; // 0 - in progress; 1 - end round; 2 - end game
   
   public function start($jogadores) {
      if (count($jogadores) < 4 || count($jogadores) > 8) throw new Exception("Jogo deve ter entre 4 e 8 jogadores!");
      $this->bets = array();
      foreach ($jogadores as $id => $nome) {
            $players[$id] = new Player($nome);
            $players[$id]->getRandomDices(3);
      }
      $this->players = $players;
   }
   
   public function bet($idPlayer, Hand $hand) {
      
      if ($this->status > 0) throw new Exception("Rodada finalizada");
      
      if ($hand->total <= 0) throw new Exception("Aposta inválida!");
      
      if ($hand->face != 0 && ($hand->face < 2 || $hand->face > 6)) throw new Exception("Aposta inválida!!");
      
      if ($idPlayer != $this->betIdPlayer) throw new Exception("Não é a vez do jogador {$this->players[$idPlayer]->name}!");
      
      // se for bago
      if ($hand->face == 0) {
         
         // verifica se é maior que ultimo bago
         $last = $this->lastBetByType(0);
         if ($hand->toNumber() <= $last) throw new Exception("Pedido menor ou igual ao número de bagos já jogado!");
         
         // verifica se é maior que a metade da ultima jogada
         $last = $this->lastBetByType(1);
			$half = round($last/20) * 10;
			if ($hand->toNumber() < $half) throw new Exception("Número de bagos deve ser a metade da última jogada!");
         
      } else {
         
         // verifica se é maior que o dobro do ultimo bago
			$last = $this->lastBetByType(0);
			if ($hand->toNumber() < ($last * 2)) throw new Exception("Pedido menor que o dobro de bagos já jogado!");
			
			// verifica se é maior que o ultimo dado jogado
			$last = $this->lastBetByType(1);
			if ($hand->toNumber() <= $last) throw new Exception("Número inferior ao último jogado!");
         
      }
      
      $this->bets[count($this->bets)] = new Bet($idPlayer, $hand);
				
		$this->changeTurn();
      
   }

   public function fold($idPlayer) {
      
      if ($this->status > 0) throw new Exception("Rodada finalizada");
      
      if (count($this->bets) == 0) throw new Exception("Não é possível desconfiar na primeira rodada!");
		
      if ($idPlayer != $this->betIdPlayer) throw new Exception("Não é sua vez de desconfiar.");
		
		$betAmount = $this->bets[count($this->bets) - 1]->hand->total;
		$betNumber = $this->bets[count($this->bets) - 1]->hand->face;
		
		$roundMsg = "";
		$newObj = clone $this;
		$this->previousPlayersHand = $newObj->players;
		
		$previousPlayer = $this->bets[count($this->bets) - 1]->idPlayer;
		if ($betAmount <= $this->numberTotal($betNumber)) {
			
			// previous player wins
			$roundMsg .= $this->players[$previousPlayer]->name . ' pediu ' . $betAmount . ' ' . Game::$DICE_DESCRIPTION[$betNumber] . ' para ' . $this->players[$idPlayer]->name . '. ';
			$roundMsg .= "Deu " . $this->numberTotal($betNumber) . ' ' . Game::$DICE_DESCRIPTION[$betNumber] . '. ' . $this->players[$previousPlayer]->name . ' venceu!';
			
			// withdraw a dice from the looser
			$lastKey = count($this->players[$idPlayer]->dices) - 1;
			unset($this->players[$idPlayer]->dices[$lastKey]);
			$this->betIdPlayer = $idPlayer;
			if (count($this->players[$idPlayer]->dices) == 0) {
				$this->betIdPlayer = $this->nextTurn();
			}
			
		} else {
			
			// desafiador wins
			$roundMsg .= $this->players[$previousPlayer]->name . ' pediu ' . $betAmount . ' ' . Game::$DICE_DESCRIPTION[$betNumber] . ' para ' . $this->players[$idPlayer]->name . '. ';
			$roundMsg .= "Deu " . $this->numberTotal($betNumber) . ' ' . Game::$DICE_DESCRIPTION[$betNumber] . '. ' . $this->players[$idPlayer]->name . ' venceu!';
			
			// withdraw a dice from the looser
			$lastKey = count($this->players[$previousPlayer]->dices) - 1;
			unset($this->players[$previousPlayer]->dices[$lastKey]);
			$this->betIdPlayer = $previousPlayer;
			if (count($this->players[$previousPlayer]->dices) == 0) {
				$this->betIdPlayer = $this->nextTurn();
			}
			
		}
		
		$this->rounds[] = $roundMsg;
		$this->status = ((int) ($this->betIdPlayer == $this->nextTurn())) + 1;
		if ($this->status == 2) {
		   $this->rounds[] = $this->players[$this->betIdPlayer]->name . ' venceu o jogo!';
		}
		
   }
   
   private function lastBetByType($type) {
      if (count($this->bets) > 0) {
			$last = 0;
			for ($i = count($this->bets); $i > 0; $i--) {
				if ($type == 0) {
					if ($this->bets[$i-1]->hand->face == 0) {
						$last = $this->bets[$i-1]->hand->toNumber();
						break;
					}
				} else {
					if ($this->bets[$i-1]->hand->face != 0) {
						$last = $this->bets[$i-1]->hand->toNumber();
						break;
					}
				}
				
			}
			return $last; 
		} else {
			return 0;
		}
      
   }
   
   public function newRound() {
      
      if ($this->betIdPlayer == $this->nextTurn()) {
			throw new Exception($this->players[$this->betIdPlayer]->name . " venceu a guerra dos dados!");
			// this.start(3);
		} else {
			$this->bets = array();
			foreach ($this->players as $i => $p) {
				$this->players[$i]->getRandomDices(count($this->players[$i]->dices));
			}
			$this->status = 0;
			$this->previousPlayersHand = array();
			# this.render();
		}
      
   }
   
   private function numberTotal($number) {
      $total = $bagos = 0;
		foreach ($this->players as $p) {
			$total += $p->numberTotal($number);
			$bagos += $p->numberTotal(1);
		}
		if ($number == 0) {
			return $bagos;
		} else {
			return ($total > 0) ? ($total + $bagos) : $total;
		}
   }
   
   private function nextTurn() {
      $nextTurn = $this->betIdPlayer + 1;
		do {
			if ($nextTurn >= count($this->players)) $nextTurn = 0;
			if (count($this->players[$nextTurn]->dices) > 0) {
				return $nextTurn;
			}
			$nextTurn++;
			if ($nextTurn > 100) throw new Exception("Bug no jogo!");
		} while (true);
   }
   
   private function changeTurn() {
      $this->betIdPlayer = $this->nextTurn();
   }
   
}