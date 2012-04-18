<?php
include(dirname(__FILE__) . "/GameModel.php");
include(dirname(__FILE__) . "/Game.php");
session_start();

$action = $_POST['action'];

$data = array();
switch ($action) {
   
   case "login":
      
      try {
         
         $nome = trim($_POST['nome']);
         if (strlen($nome) < 2) throw new Exception("Nome inválido!");
         
         $gm = new GameModel();
         $_SESSION['jogador'] = $gm->login($nome);
         
         $data['status'] = 1;
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "salas_abertas":
      
      try {
         
         $gm = new GameModel();
         $salas = $gm->salasAbertas();
         
         $data['status'] = 1;
         $data['salas'] = $salas;
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "status_sala":
      
      try {
         
         if (!isset($_SESSION['jogador'])) throw new Exception("Usuário sem apelido no momento!");
         
         if ($_SESSION['jogador']['id_sala'] == null) throw new Exception("Usuário não pertence a nenhuma sala no momento!");
         
         $gm = new GameModel();
         $data['sala'] = $gm->statusSala($_SESSION['jogador']['id_sala']);
         $_SESSION['jogador']['sala'] = $data['sala'];
         $data['status'] = 1;
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "criar_sala":
      
      try {
         
         if (!isset($_SESSION['jogador'])) throw new Exception("Usuário sem apelido no momento!");
         
         if ($_SESSION['jogador']['id_sala'] != null) throw new Exception("Usuário já está em outra sala!");
         
         $qtd = (int) $_POST['qtd'];
         if ($qtd < 4 || $qtd > 8) throw new Exception("Uma sala deve ter de 4 a 8 jogadores!");
         
         $gm = new GameModel();
         $sala = $gm->criarSala($_SESSION['jogador']['id_jogador'], $qtd);
         
         $_SESSION['jogador']['id_sala'] = $sala['id_sala'];
         
         $_SESSION['jogador']['sala'] = $sala;
         
         $data['status'] = 1;
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "entrar_sala":
      
      try {
         
         if (!isset($_SESSION['jogador'])) throw new Exception("Usuário sem apelido no momento!");
         
         if ($_SESSION['jogador']['id_sala'] != null) throw new Exception("Usuário já está em outra sala!");
         
         $id_sala = (int) $_POST['id_sala'];
         if ($id_sala <= 0) throw new Exception("Sala inválida!");
         
         $gm = new GameModel();
         $sala = $gm->entrarSala($_SESSION['jogador']['id_jogador'], $id_sala);
         
         $_SESSION['jogador']['id_sala'] = $sala['id_sala'];
         
         $_SESSION['jogador']['sala'] = $sala;
         
         $data['status'] = 1;
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "start":
      
      try {
         
         $qtd = (int) $_POST['qtd'];
         
         $game = new Game();
         $game->start($qtd);
         
         $data['status'] = 1;
         $data['game_json'] = $game;
         $_SESSION['game'] = $game;
         $_SESSION['game_json'] = json_encode($game);
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "bet":
      
      try {
         if (!isset($_SESSION['game'])) throw new Exception("Jogo não iniciado!");
         
         $idPlayer = (int) $_POST['player'];
         $face = (int) $_POST['face'];
         $total = (int) $_POST['total'];
         $hand = new Hand($total, $face);
         
         $game = $_SESSION['game'];
         $game->bet($idPlayer, $hand);
         
         $data['status'] = 1;
         $data['game_json'] = $game;
         $_SESSION['game'] = $game;
         $_SESSION['game_json'] = json_encode($game);
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "fold":
      
      try {
         if (!isset($_SESSION['game'])) throw new Exception("Jogo não iniciado!");
         
         $idPlayer = (int) $_POST['player'];
         
         $game = $_SESSION['game'];
         $game->fold($idPlayer);
         $data['status'] = 1;
         $data['game_json'] = $game;
         $_SESSION['game'] = $game;
         $_SESSION['game_json'] = json_encode($game);
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
   case "newRound":
      
      try {
         if (!isset($_SESSION['game'])) throw new Exception("Jogo não iniciado!");
         
         $game = $_SESSION['game'];
         $game->newRound();
         
         $data['status'] = 1;
         $data['game_json'] = $game;
         
         $_SESSION['game'] = $game;
         $_SESSION['game_json'] = json_encode($game);
         
      } catch (Exception $e) {
         $data['status'] = 0;
         $data['msg'] = $e->getMessage();
      }
      
   break;
   
}


header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-type: text/x-json");
echo(json_encode($data));

?>
