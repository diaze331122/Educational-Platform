<?php
    require_once('validate_input.php');
    require_once('connect_database.php');
    require_once('user_registration.php');
    require_once('student_registration.php');
    require_once('parent_registration.php');

    //check if all fields are not empty
    if (!isEmpty($_POST['firstname'] && validateName($_POST['firstname'])) 
        && !isEmpty($_POST['lastname'] && validateName($_POST['lastname']))
        && !isEmpty($_POST['username'] && validateUsername($_POST['username']))
        && !isEmpty($_POST['email'] && validateEmail($_POST['email']))
        && !isEmpty($_POST['password'] && validatePassword($_POST['password']))
        && !isEmpty($_GET['t']) && validateType($_GET['t'])){

        //open database connection
        $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);

        //clean up input
        $firstname = testInput($_POST['firstname'],$connection);
        $lastname = testInput($_POST['lastname'],$connection);
        $username = testInput($_POST['username'],$connection);
        $email = testInput($_POST['email'],$connection);
        $password = testInput($_POST['password'],$connection);
        $type_id = testInput($_GET['t'],$connection);       
        $register = getRegistration($type_id, $firstname, $lastname, $email, 
        $username, $password, $connection);

        if ($register->registerUser()){
            if ($register->createAccountVerificationToken()){             
                if ($register->sendRegistrationEmail()){
                    echo 'Email verification sent';
                    //redirect('redirect page');
                }else{
                    echo 'Could not create email';
                    //redirect('redirect page');
                    }
            }                   
        }
    }else{
        echo 'Invalid input';
    }

    function getRegistration($type_id, $firstname, $lastname, $email, 
    $username, $password, $connection){
        switch($type_id){
            case 1:{
                return new UserRegistration($type_id, $firstname, $lastname, $email, 
                            $username, $password, $connection);
            }
            case 2:{
                if (is_numeric($_GET['i'])){
                    $instructor = $_GET['i'];
                    return new ParentRegistration($type_id, $firstname, $lastname, $email, 
                                $username, $password, $connection,$instructor);

                }
            }
            case 3:{
                if (is_numeric($_GET['i']) && is_numeric($_GET['p'])){
                    $instructor = $_GET['i'];
                    $parent = $_GET['p'];
                    return new StudentRegistration($type_id, $firstname, $lastname, $email,
                                $username, $password, $connection,$instructor,$parent);    
                }               
            }
            default:{
                return new UserRegistration($type_id, $firstname, $lastname, $email,
                        $username, $password, $connection);
            }
        }  
    }

    function redirect($path){
        header($path);
    }
?>