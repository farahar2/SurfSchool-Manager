<?php
class Database {

private $host ="localhost";
private $dbname = "taghazout_surf";
private $username = "root";
private $password = "";
private $connection = null;

public function getConnection(){
  if($this->connection === null){
    try{
    $this->connection = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                    $this->username,
                    $this->password
                );
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e){
       die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
  }
      return $this->connection;
}
}
?>