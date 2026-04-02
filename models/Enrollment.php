<?php 
class Enrollment {
  private $db;
  public function __construct($db) {
    $this->db = $db;
  }

  public function enroll($studentId, $lessonId){

  }

  public function getStudentLessons($studentId){
    
  }

  public function updatePaymentStatus($enrollmentId, $status){

  }

}
?>