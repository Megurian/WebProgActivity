<?php
require_once 'account.class.php';
require_once 'functions.php';

$accountObj = new Account();


$first_name = $last_name = $username = $password = $confirm_password = '';
$first_nameE = $last_nameE = $usernameE = $passwordE = $confirm_passwordE = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = clean_input($_POST['username']);
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);

    //Validation First name
    if(empty($first_name)) {
        $first_nameE = 'First name is required to signup';
    } elseif(is_numeric($first_name)) {
        $first_nameE = 'Enter a valid first name';
    }

    //Validation Last name
    if(empty($last_name)) {
        $last_nameE = 'Last name is required to signup';
    } elseif(is_numeric($last_name)) {
        $last_nameE = 'Enter a valid Last name';
    }

     //Validation username
     if(empty($username)) {
        $usernameE = 'Username is required to signup';
    } elseif(is_numeric($username)) {
        $usernameE = 'Enter a valid username';
    }

    //Validation Password
    if(empty($password)) {
        $passwordE = 'Password is required to signup';
    } elseif(strlen($password) < 8) {
        $passwordE = 'Enter atleast 8 characters password';
    } elseif(!preg_match('/[0-9]/', $password) || 
             !preg_match('/[A-Z]/', $password) || 
             !preg_match('/[a-z]/', $password) || 
             !preg_match('/[^a-zA-Z\d]/', $password)) {
        $passwordE = 'Password must contain at least 1 number, 1 uppercase, 1 lowercase, 1 special';
    } elseif(strpos($password, $first_name) !== false || 
             strpos($password, $last_name) !== false || 
             strpos($password, $username) !== false) {
        $passwordE = 'Weak password, please try a different password';
    } elseif(empty($confirm_password)) {
        $confirm_passwordE = 'Please confirm your password';
    } elseif($confirm_password != $password) {
        $confirm_passwordE = 'Password does not match!';
    }
    
    if(empty($usernameE) && empty($first_nameE) && empty($last_nameE) &&
       empty($passwordE) && empty($confirm_passwordE)) {
        
        $encrytedPassword = password_hash($confirm_password, PASSWORD_DEFAULT, ['cost' => 12]);

        $accountObj->username = $username;
        $accountObj->first_name = $first_name;
        $accountObj->last_name = $last_name;
        $accountObj->password = $encrytedPassword;
        $accountObj->role = 'customer';
        $accountObj->is_staff = false;
        $accountObj->is_admin = false;
            
        if($accountObj->add()) {
            header('location: login.php');
        } else {
            echo '<script>alert("Something went wrong when signing up")</script>';
        }
    }

}else{
    session_start();
    if(isset($_SESSION['account'])){
        if(isset($_SESSION['account'])){
            if($_SESSION['account']['is_staff']){
                header('location: dashboard.php');
            } elseif($_SESSION['account']['is_staff'] == false){
                header('location: customer.php');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://classless.de/classless.css">
</head>
<body>
    <form action="" method="post">
        <h2>Customer Sign-up</h2>
        <p>All field are required</p>
        <hr><br>

        <label for="first_name">Enter your first name:</label><br>
        <input type="text" name="first_name" placeholder="Enter first name:" value="<?php echo $first_name; ?>">
        <?php if(!empty($first_nameE)): ?>
            <span class="error" style="color: red;"><?= $first_nameE ?></span><br>
        <?php endif; ?>
        <br>

        <label for="last_name">Enter your last name:</label><br>
        <input type="text" name="last_name" placeholder="Enter last name:" value="<?php echo $last_name; ?>">
        <?php if(!empty($last_nameE)): ?>
            <span class="error" style="color: red;"><?= $last_nameE ?></span><br>
        <?php endif; ?>
        <br>

        <label for="username">Enter your username:</label><br>
        <input type="text" name="username" placeholder="Enter your username:" value="<?php echo $username; ?>">
        <?php if(!empty($usernameE)): ?>
            <span class="error" style="color: red;"><?= $usernameE ?></span><br>
        <?php endif; ?>
        <br>
        
        <label for="password">Create password:</label><br>
        <input type="password" id="password" name="password" placeholder="Enter at least 8 characters password">
        <?php if(!empty($passwordE)): ?>
            <span class="error" style="color: red;"><?= $passwordE ?></span><br>
        <?php endif; ?>
        <br>

        <label for="confirm_password">Confirm your password</label><br>
        <input type="password" name="confirm_password" placeholder="Confirm your password">
        <?php if(!empty($confirm_passwordE)): ?>
            <span class="error" style="color: red;"><?= $confirm_passwordE ?></span><br>
        <?php endif; ?>
        <br>

        <input type="submit" value="Sign-up" style="background-color: green;">
    </form>
</body>
</html>