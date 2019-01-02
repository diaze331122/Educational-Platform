<?php
    class Login{
        //Query to check if username exists
        private const PASSWORD_HASH_QUERY = "SELECT password FROM Users WHERE username = ? AND status = ?";
        private const INFO_QUERY = "SELECT user_id, type_id FROM Users".
                                    " WHERE username = ? AND password = ?";
        private $username;
        private $password;
        private $password_hash;
        private $conn;
        private $type_id;
        private $user_id;

        //Login constructor
        function __construct($username, $password,$conn){
            $this->username = $username;
            $this->password = $password;
            $this->conn = $conn;
        }

        function login(){
            $this->password_hash = $this->getUserPasswordHash();
            if ($this->password_hash != null){
                if ($this->verifyPassword()){
                    $user_info = $this->getUserInfo();
                    if ($user_info != null){
                        $this->setUserId($user_info[0]);
                        $this->setTypeId($user_info[1]);
                        return true;
                    }
                }
            }
            return false;
        }

        private function setUserId($id){
            $this->user_id = $id;
        }

        function getUsername(){
            return $this->username;
        }

        function getUserId(){
            return $this->user_id;
        }

        private function setTypeId($id){
            $this->type_id = $id;
        }

        function getTypeId(){
            return $this->type_id;
        }

        private function getUserPasswordHash(){
            $status = 1;
            $stmt = $this->conn->prepare(self::PASSWORD_HASH_QUERY);
            $stmt->bind_param("si",$this->username,$status);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($password);
                $stmt->fetch();
                return $password;
            }else{
                return null;
            }
        }

        private function getUserInfo(){
            $stmt = $this->conn->prepare(self::INFO_QUERY);
            $stmt->bind_param("ss",$this->username,$this->password_hash);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id,$type_id);
                $stmt->fetch();
                return array($user_id,$type_id);
            }else{
                return null;
            }           
        }

        private function verifyPassword(){
            return password_verify($this->password, $this->password_hash);
        }
    
        function __destruct(){
            $this->conn->close();
        }
    }    
?>