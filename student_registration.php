<?php
    require_once('user_registration.php');

    class StudentRegistration extends UserRegistration{
        private $instructor_id;
        private $parent_id;

        private const INSERT_PARENT_REL = "INSERT INTO Parent_Student(parent_id, student_id) VALUES(?,?)";
        private const INSERT_INSTRUCTOR_REL = "INSERT INTO Teacher_Student(teacher_id, student_id) VALUES(?,?)";

        function __construct($type_id, $firstname, $lastname, $email, $username, $password, $conn, $instructor_id, $parent_id){
            parent::__construct($type_id, $firstname, $lastname, $email, $username, $password, $conn);
            $this->instructor_id = $instructor_id;
            $this->parent_id = $parent_id;
        }

        function registerUser(){
            if (parent::registerUser() && $this->insertParent() && $this->insertInstructor()){
                echo 'registered!!';
                return true;
            }
            echo 'could not register';
            return false;
        }
        
        function insertParent(){
            return parent::insertDependency(self::INSERT_PARENT_REL,$this->parent_id);
        }

        function insertInstructor(){
            return parent::insertDependency(self::INSERT_INSTRUCTOR_REL,$this->instructor_id);
        }
    }
?>