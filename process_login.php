<?php
    require_once('login.php');
    require_once('validate_input.php');
    require_once('connect_database.php');

    //check if all fields are not empty
    if (isset($_POST['username']) && $_POST['username'] != '' 
    && isset($_POST['password']) && $_POST['password'] != ''){
        //connect to database and clean up posts
        $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);
        $username = testInput($_POST['username'],$connection);
        $password = testInput($_POST['password'],$connection);
        //create new login object
        $login = new Login($username,$password,$connection);

        //check if correct authentication is entered
        if ($login->verifyLogin()){
            //fetch user variables
            $user = $login->getUserVariablesArray();
            if ($user != null){
                setSession($user);
                $type = $user['type'];
                $path = '';

                switch($type){
                    case 'instructor':{
                        $path = '';
                        break;
                    }
                    case 'parent':{
                        $path= '';
                        break;
                    }
                    case 'student':{
                        $path = '';
                        break;
                    }
                    default:{
                        break;
                    }
                }
                redirect($path);
            }
        }else{
            //to be determined
        }

        //set session veriables
        function setSession($user){
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['type'] = $user['type'];
            $_SESSION['login'] = true;
        }

        function redirect($path){
            header($path);
        }

    }
?>