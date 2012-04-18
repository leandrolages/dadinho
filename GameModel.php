<?php

class GameModel {
   
   private $db;
   
   public function __construct() {
      
      $db = mysql_connect("btbteste","mybt","b!@#qaz$%&^t");      
      if (!$db) throw new Exception("Erro ao conectar na base de dados!");
      
      if (!mysql_select_db("dadinho", $db)) throw new Exception("Base de dados não encontrada!");
      
      $this->db = $db;
      
   }
   
   private function _beginTrans() {
      $sql = "START TRANSACTION";
      if (!mysql_query($sql, $this->db)) throw new Exception(mysql_error());
   }
   
   private function _commitTrans() {
      $sql = "BEGIN";
      if (!mysql_query($sql, $this->db)) throw new Exception(mysql_error());
   }
   
   private function _rollbackTrans() {
      $sql = "ROLLBACK";
      if (!mysql_query($sql, $this->db)) throw new Exception(mysql_error());
   }
   
   private function _execute($sql) {
      $query = mysql_query($sql, $this->db);
      if (!$query) throw new Exception(mysql_error());
      return $query;
   }
   
   
   public function login($nome) {
      
      try {

         $this->_beginTrans();
         
         # selecionar existencia do login na base
         $sql = "SELECT * FROM jogador WHERE nome = '$nome' ORDER BY id_jogador DESC";
         $query = $this->_execute($sql);
         
         # fazer calculo
         $time = time();
         if (mysql_num_rows($query) > 0) {
            
            $row = mysql_fetch_assoc($query);
            
            # login preso
            if ($row['ultima_acao'] < ($time - 3600)) {
               $sql = "DELETE FROM jogador WHERE id_jogador = {$row['id_jogador']}";
               $this->_execute($sql);
               if ($row['id_sala'] != null) {
                  $sql = "UPDATE sala SET qtd_atual = qtd_atual - 1 WHERE id_sala = {$row['id_sala']} AND status = 0";
                  $this->_execute($sql);                  
               }
               
            } else {
               throw new Exception("Apelido sendo usado no momento.");
            }
            
         }
            
         $sql = "INSERT INTO jogador VALUES (0, null, '$nome', $time)";
         $this->_execute($sql);
         $id = mysql_insert_id();
         
         $this->_commitTrans();
         
         return array('id_jogador' => $id, 'id_sala' => null, 'nome' => $nome, 'ultima_acao' => $time);
            
         
      } catch (Exception $e) {
         $this->_rollbackTrans();
         throw $e;
      }
      
      
   }
   
   public function salasAbertas() {
      
      # selecionar existencia do login na base
      $sql = "SELECT * FROM sala WHERE status = 0 ORDER BY id_sala DESC";
      $query = $this->_execute($sql);
      
      $salas = array();
      while ($r = mysql_fetch_assoc($query)) $salas[] = $r;
      
      return $salas;
         
   }
   
   public function statusSala($id_sala) {
      
      # selecionar existencia do login na base
      $sql = "SELECT * FROM sala WHERE id_sala = $id_sala";
      $query = $this->_execute($sql);
      
      if (mysql_num_rows($query) == 0) throw new Exception("Sala não encontrada!");
      
      $sala = mysql_fetch_assoc($query);
      
      $sql = "SELECT * FROM jogador WHERE id_sala = $id_sala ORDER BY id_jogador";
      $query = $this->_execute($sql);
      
      $jogadores = array();
      while ($r = mysql_fetch_assoc($query)) $jogadores[] = $r;
      
      $sala['jogadores'] = $jogadores;
      
      return $sala;
         
   }
   
   public function criarSala($id_jogador, $qtd) {
      
      try {

         $this->_beginTrans();
         
         # selecionar existencia do login na base
         $sql = "INSERT INTO sala VALUES (0, $qtd, 0, null, 1)";
         $query = $this->_execute($sql);
         $id_sala = mysql_insert_id();
         
         # atualizar jogador na sala
         $sql = "UPDATE jogador SET id_sala = $id_sala WHERE id_jogador = $id_jogador";
         $query = $this->_execute($sql);
         
         $this->_commitTrans();
         
         return array('id_sala' => $id_sala, 'total' => $qtd, 'status' => 0, 'game' => null, 'qtd_atual' => 1);
         
      } catch (Exception $e) {
         $this->_rollbackTrans();
         throw $e;
      }
      
      
   }
   
   public function entrarSala($id_jogador, $id_sala) {
      
      try {

         $this->_beginTrans();
         
         $sql = "SELECT * FROM sala WHERE id_sala = $id_sala AND status = 0 AND qtd_atual != total";
         $query = $this->_execute($sql);
         if (mysql_num_rows($query) == 0) throw new Exception("Sala não disponível!");
         $row = mysql_fetch_assoc($query);
         
         # atualizar jogador na sala
         $sql = "UPDATE jogador SET id_sala = $id_sala WHERE id_jogador = $id_jogador";
         $query = $this->_execute($sql);
         
         # ultimo a entrar na sala?
         if (++$row['qtd_atual'] == $row['total']) {
            
            $sql = "SELECT * FROM jogador WHERE id_sala = $id_sala";
            $query = $this->_execute($sql);
            $jogadores = array();
            while ($r = mysql_fetch_assoc($query)) $jogadores[$r['id_jogador']] = $r['nome'];
            $game = new Game();
            $game->start($jogadores);
            $json_game = json_encode($game);
            
            # atualizar valor de usuarios na sala
            $sql = "UPDATE sala SET qtd_atual = qtd_atual + 1, status = 1, game = '$json_game' WHERE id_sala = $id_sala";
            $row['status'] = 1;
            $row['game'] = $json_game;
            
         } else {
            
            # atualizar valor de usuarios na sala
            $sql = "UPDATE sala SET qtd_atual = qtd_atual + 1 WHERE id_sala = $id_sala";
         }
         
         $query = $this->_execute($sql);
         
         $this->_commitTrans();
         
         return $row;
         
      } catch (Exception $e) {
         $this->_rollbackTrans();
         throw $e;
      }
      
      
   }
   
   
}