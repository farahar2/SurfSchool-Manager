<?php 
require_once __DIR__ . '/../config/Database.php';
class User {
  private $db;
  private $id;
  private $email;
  private $password;
  private $role;
  private $created_at;

  //initialiser la connexion DB
  public function __construct() {
    $database = new Database();
    $this->db = $database->getConnection();
  }
  //la création d'un nouvel utilisateur
  public function register($email, $password, $role = 'client'){
    if($this->findByEmail($email)){
      echo "Email déjà utilisé.";
      return false;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (email, password, role) VALUES (:email,:password,:role)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      ':email' => $email,
      ':password' => $hashedPassword,
      ':role' => $role
    ]);
  }
  //la connexion d'un utilisateur
  public function login($email, $password){
  $user = $this->findByEmail($email);
  if(!$user){
   return false;
  }
  if(password_verify($password, $user['password'])){
    unset($user['password']);
    return $user;
  }
  return false;
  }
  //trouver un utilisateur par son email
  public function findByEmail($email){
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
  }

  public function findById($id){
    $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
  }

  public function getId(){
    return $this->id;
  }

  public function getEmail(){
    return $this->email;
  }

  public function getRole(){
    return $this->role;
  }
}
?>