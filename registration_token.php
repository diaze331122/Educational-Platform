<?php
    class RegistrationToken{
        private const INSERT_TOKEN_QUERY = "INSERT INTO Account_Verification_Token(user_id,token_hash,timestamp,expiry)".
        "VALUES (?,?,?,?)";        
        private $user_id;
        private $token_hash;

        function __construct($user_id){
            $this->user_id = $user_id;
            $this->token_hash = $this->generateHash();
        }

        function generateHash(){
            return hash('md2',rand(1000,1000000));
        }

        function storeAccountVerificationToken($conn){
            $timestamp = date('Y-m-d H:i:s');
            $expiry = date('Y-m-d H:i:s', strtotime('+7 days'));

            $stmt = $conn->prepare(self::INSERT_TOKEN_QUERY);
            $stmt->bind_param("isss",$this->user_id,$this->token_hash,$timestamp,$expiry);
      
            //successful user insert
            if ($stmt->execute()){
                echo 'Created acount verification token';
                return true;
            }else{
                echo 'Could not create token';
                return false;
            }
        }

         function getVerificationURL(){
            return 'http://www.domain-name.com/verify.php?i='.$this->user_id.'&h='.$this->token_hash;
        }       
    }
?>