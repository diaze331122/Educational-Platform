<?php
    class VerifyAccount{
        private const CHECK_EMAIL_HASH_ACT = 'SELECT * FROM USERS WHERE email = ? AND hash = ? AND active = ?';
        private const UPDATE_ACC_ACT = "UPDATE USERS SET active = ? WHERE email = ? AND hash = ? AND active = ?";
        private $email;
        private $hash;
        private $active;
        private $conn;

        function __construct($email,$hash,$active,$conn){
            $this->email = $email;
            $this->hash = $hash;
            $this->active = $active;
            $this->conn = $conn;
        }

        //verify the account
        function verify(){
            if (checkIfAccountExists()){
                activateAccount();
                return true;
            }else{
                return false;
            }
        }

        function checkIfAccountExists(){
            $stmt = $this->conn->prepare(self::CHECK_EMAIL_HASH_ACT);
            $stmt->bindParam("ssi",$this->username,$this->hash,$this->active);
            $stmt->execute();

            if ($stmt->num_rows == 1) {
                return true;
            }else{
                return false;
            }
        }

        function activateAccount(){
            $stmt = $this->conn->prepare(self::UPDATE_ACC_ACT);
            $stmt->bindParam("ssi",$this->username,$this->hash,$this->active);
            $stmt->execute();
        } 
        
        function __destruct(){
            $this->conn->close();
        }
    }
?>