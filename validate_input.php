<?php
    //script for server-side validation

    function isEmpty($data){
        if (isset($data) && $data != ''){
            return false;
        }else{
            return true;
        }
    }

    function validateUsername($data){
        if (preg_match("/^([A-Za-z0-9]+)(_?[A-Za-z0-9]+)+$/",$data)){
            return true;
        }
        return false;
    }

    function validatePassword($data){
        if (strlen($data) >= 6){
            if (preg_match("/[!@#$%^&*(),.?:{}|<>]+/",$data) && preg_match("/[A-Za-z0-9_]+/",$data)){
                return true;
            }
        }
        return false;
    }

    function validateName($data){
        if (preg_match("/^([a-zA-Z' ]+)$/",$data)){
            return true;
        }
        return false;
    }

    function validateEmail($data){
        if (filter_var($data,FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    function validateType($data){
        if (preg_match("/(1|2|3)/",$data)){
            return true;
        }
        return false;
    }
    
    function testInput($data,$conn){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = $conn->real_escape_string($data);
        return $data;
    }
?>