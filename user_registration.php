<?php
    require_once('registration_token.php');

    class UserRegistration{
        private const CHECK_USERNAME_QUERY = "SELECT * FROM Users WHERE username = ?";
        private const CHECK_PASSWORD_QUERY = "SELECT * FROM Users WHERE password = ?";
        private const INSERT_USER_QUERY = "INSERT INTO Users(type_id,firstname,lastname,email,username,password,status)".
        "VALUES (?,?,?,?,?,?,?)";

        private $user_id;
        private $type_id;
        private $firstname;
        private $lastname;
        private $email;
        private $username;
        private $password;
        private $token;
        private $conn;

        function __construct($type_id, $firstname, $lastname, $email, $username, $password, $conn){
            $this->type_id = $type_id;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->email = $email;
            $this->username = $username;
            $this->password = $password;
            $this->conn = $conn;
        }

        function registerUser(){
            //if username or password already exists, return false
            if ($this->ifUsernameExists($this->username) || $this->ifPasswordExists($this->password)){
                return false;
            }else{
                if ($this->insertUser()){
                    return true;
                }else{
                    return false;
                }
            }
        }

        private function insertUser(){
            $status = 0;
            $stmt = $this->conn->prepare(self::INSERT_USER_QUERY);
            $stmt->bind_param("isssssi",$this->type_id,$this->firstname,$this->lastname,
            $this->email,$this->username,$this->password,$status);
  
            //successful user insert
            if ($stmt->execute()){
                //retrieve user id
                $this->user_id = $this->conn->insert_id;
                return true;
            }else{
                echo 'Error creating user';
                return false;
            }
        }

        private function ifUsernameExists($username){
            $stmt = $this->conn->prepare(self::CHECK_USERNAME_QUERY);
            $stmt->bind_param("s",$this->username);
            $stmt->execute();

            if ($stmt->num_rows > 0) {
                return true;
            }else{
                return false;
            }
        }

        private function ifPasswordExists($password){
            $hashed_password = $this->hashPassword($password);
            $stmt = $this->conn->prepare(self::CHECK_PASSWORD_QUERY);
            $stmt->bind_param("s",$hashed_password);
            $stmt->execute();

            if ($stmt->num_rows > 0){
                return true;
            }else{
                //save hashed password   
                $this->password = $hashed_password;
                return false;
            }
        }

        private function hashPassword($password){
            $hashed_password = password_hash($password,PASSWORD_DEFAULT);
            return $hashed_password;
        }

        function insertDependency($query,$param){
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii",$param,$this->user_id);
            if ($stmt->execute()){
                return true;
            }else{
                return false;
            }
        }

        //create and store verification token
        function createAccountVerificationToken(){
            $this->token = new RegistrationToken($this->user_id);
            if ($this->token->storeAccountVerificationToken($this->conn)){
                return true;
            }else{
                return false;
            }
        }

        function sendRegistrationEmail(){
            $url = $this->token->getVerificationURL();
            $to = $this->email;
            $subject = 'Confirm Account';
            $mssg = 'Thank you for signing up. You can verify your account by clicking this link: '.$url;
            $from = 'From:noreply@yourwebsite.com'.'\r\n';
    
            // Send verification email
            if (mail($to, $subject, $mssg, $from)){
                return true;
            }else{
                return false;
            }
        }

        function __destruct(){
            $this->conn->close();
        }
    }
?>