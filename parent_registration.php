<?php
    require_once('user_registration.php');

    class ParentRegistration extends UserRegistration{
        private const INSERT_INSTRUCTOR_REL = "INSERT INTO Parent_Instructor(instructor_id, parent_id) VALUES(?,?)";
        private $instructor_id;

        function __construct($type_id, $firstname, $lastname, $email, $username, $password, $conn, $instructor_id){
            parent::__construct($type_id, $firstname, $lastname, $email, $username, $password, $conn);
            $this->instructor_id = $instructor_id;
        }

        function registerUser(){
            if (parent::registerUser() && $this->insertInstructor()){
                return true;
            }
            return false;            
        }

        function insertInstructor(){
            return parent::insertDependency(self::INSERT_INSTRUCTOR_REL,$this->instructor_id);
        }
        
    }
?>