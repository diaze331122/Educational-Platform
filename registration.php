<?php
    class Registration{
        private const CHECK_USERNAME_QUERY = "SELECT * FROM USERS WHERE username = ?";
        private const CHECK_PASSWORD_QUERY = "SELECT * FROM USERS WHERE password = ?";
        private const INSERT_USER_QUERY = "INSERT INTO USERS(firstname,lastname,username,email,password,user_type,hash,active)".
                                        " VALUES (?,?,?,?,?,?,?,?)";
        private $username;
        private $password;
        private $conn;
        private $userType;
        private $email;
        private $hash;
        
        function __construct($username,$password,$firstname,$lastname,$email,$userType,$hash,$conn){
            $this->username = $username;
            $this->password = $password;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->email = $email;
            $this->userType = $userType;
            $this->hash = $hash;
            $this->conn = $conn;
        }

        function registerUser(){
            //if username or password already exists, return false
            if (checkIfUsernameExists($this->username) || checkIfPasswordExists($this->password)){
                return false;
            }else{
                insertUser();
                return true;
            }
        }

        function insertUser(){
            $stmt = $this->conn->prepare(self::INSERT_USER_QUERY);
            $stmt->bindParam("sssssssi",$this->firstname,$this->lastname,$this->username,
                            $this->email,$this->password,$this->userType,$this->hash,'0');
            $stmt->execute();
        }

        function checkIfUsernameExists($username){
            $stmt = $this->conn->prepare(self::CHECK_USERNAME_QUERY);
            $stmt->bindParam("s",$this->username);
            $stmt->execute();

            if ($stmt->num_rows > 0) {
                return true;
            }else{
                return false;
            }
        }

        function checkIfPasswordExists($password){
            $hashed_password = hashPassword($password);
            $stmt = $this->conn->prepare(self::CHECK_PASSWORD_QUERY);
            $stmt->bindParam("s",$hashed_password);
            $stmt->execute();

            if ($stmt->num_rows > 0){
                return true;
            }else{
                $this->password = $hashed_password;
                return false;
            }
        }

        function hashPassword($password){
            $hashed_password = password_hash($password,PASSWORD_DEFAULT);
            return $hashed_password;
        }

        function __destruct(){
            $connection->close();
        }
    }
?>