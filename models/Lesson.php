<?php
class Lesson {
  private $db;
  public function __construct($db) {
    $this->db = $db;
  }

  public function create($title, $coach, $start, $end, $maxStudents, $level){

  }
  
  public function getAll(){

  }

  public function getById($id){

  }

  public function getAvailableSpots($lessonId){

  }

}

?>