<?php
    class VerifyAccount{
        private const CHECK_VERIFICATION_TOKEN = 'SELECT expiry FROM Account_Verification_Token WHERE user_id = ? AND token_hash = ?';
        private const UPDATE_ACC_ACT = 'UPDATE Users SET status = ? WHERE user_id = ? AND status = ?';

        private $user_id;
        private $token_hash;
        private $conn;

        function __construct($user_id,$token_hash,$conn){
            $this->user_id = $user_id;
            $this->token_hash = $token_hash;
            $this->conn = $conn;
        }

        //verify the account
        function verify(){
            $stmt = $this->conn->prepare(self::CHECK_VERIFICATION_TOKEN);
            $stmt->bind_param("is",$this->user_id,$this->token_hash);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1){
                $stmt->bind_result($expiry);
                $stmt->fetch();

                if (!$this->isTokenIsExpired($expiry)){
                    if ($this->activateAccount()){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    echo 'Token is expired. ';
                    return false;
                }
            }
            return false;
        }

        function isTokenIsExpired($expiry){
            $currentTime = date('Y-m-d H:i:s');
            if ($expiry < $currentTime){
                return true;
            }
            return false;
        }

        function activateAccount(){
            $activeStatus = 1;
            $inactiveStatus = 0;
            $stmt = $this->conn->prepare(self::UPDATE_ACC_ACT);
            $stmt->bind_param("iii",$activeStatus,$this->user_id,$inactiveStatus);
            $stmt->execute();

            if($stmt->affected_rows === 0){
                return false;
            }else{
                return true;
            }
        } 
        
        function __destruct(){
            $this->conn->close();
        }
    }
?>