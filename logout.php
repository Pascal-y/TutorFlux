<?php 
session_start();
include('config.php');

if(!isset($_SESSION['logout'])){

    unset($_SESSION['user_name']);
    unset($_SESSION['auth']);
    unset($_SESSION['role']);

    $_SESSION['message'] = 'Logged out successfully';
    header("Location: ./login/login.php");
    exit(0);

}

?>