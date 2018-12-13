<?php
    class Login{
        //Query to check if username exists
        private const CHECK_USERNAME_QUERY = "SELECT * FROM USERS WHERE username = ?";
        private $username;
        private $password;
        private $conn;
        private $userType;
        private $id;

        //Login constructor
        function __construct($username, $password,$conn){
            $this->username = $username;
            $this->password = $password;
            $this->conn = $conn;
        }
        //checks if correct authentication is entered
        function verifyLogin(){
            $stmt = $this->conn->prepare(self::CHECK_USERNAME_QUERY);
            $stmt->bindParam("s",$this->username);
            $stmt->execute();
            
            $stmt->bind_result($user_id,$db_password,$type);
            $stmt->fetch();

            //if the username exists
            if ($stmt->num_rows == 1) {
                if (verifyPassword($this->password,$db_password)){
                    setUserId($user_id);
                    setUserType($type);
                    return true;
                }
            }
            return false;          
        }

        //checks if passwords match
        function verifyPassword($password_input,$db_password){
            if (password_verify($this->password,$this->db_password)){
                return true;
            }else{
                return false;
            }
        }

        function getUsername(){
            return $this->username;
        }

        function setUserId($id){
            $this->id = $id;
        }

        function getUserId(){
            return $this->id;
        }

        function setUserType($type){
            $this->userType = $type;
        }

        function getType(){
            return $this->userType;
        }

        //returns array containing query result
        function getUserVariablesArray(){
            if (getUserId() != null && getType() != null && getUsername() != null){
                $user['id'] = getUserId();
                $user['username'] = getUsername();
                $user['type'] = getType();
                return $user;
            }
            return null;
        }

        function __destruct(){
            $this->conn->close();
        }
    }    
?>