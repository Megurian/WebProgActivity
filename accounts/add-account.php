<?php

require_once('../tools/functions.php');
require_once('../classes/account.class.php');

$username = $first_name = $last_name = $password = $role = '';
$usernameErr = $first_nameErr = $last_nameErr = $passwordErr = $roleErr = '';

$accountObj = new Account();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $password = clean_input($_POST['password']);
    $role = clean_input($_POST['role']);
    
    if (empty($username)) {
        $usernameErr = 'Username is required.';
    } else if ($accountObj->usernameExist($username, 0)) {
        $usernameErr = 'Username already exists.';
    }
    if (empty($first_name)) {
        $first_nameErr = 'First Name is required.';
    }
    if (empty($last_name)) {
        $last_nameErr = 'Last Name is required.';
    }
    if (empty($password)) {
        $passwordErr = 'Password is required.';
    }
    if (empty($role)) {
        $roleErr = 'Role is required.';
    }
    // Role must be either 'staff' or 'admin'
    if ($role != 'staff' && $role != 'admin') {
        $roleErr = 'Role must be either staff or admin.';
    }
    // If there are validation errors, return them as JSON
    if (!empty($usernameErr) || !empty($first_nameErr) || !empty($last_nameErr) || !empty($passwordErr) || !empty($roleErr)) {
        echo json_encode([
            'status' => 'error',
            'usernameErr' => $usernameErr,
            'first_nameErr' => $first_nameErr,
            'last_nameErr' => $last_nameErr,
            'passwordErr' => $passwordErr,
            'roleErr' => $roleErr
        ]);
        exit;
    }
    if (empty($usernameErr) && empty($first_nameErr) && empty($last_nameErr) && empty($passwordErr) && empty($roleErr)) {
        $accountObj->username = $username;
        $accountObj->first_name = $first_name;
        $accountObj->last_name = $last_name;
        $accountObj->password = $password;
        $accountObj->role = $role;
        $accountObj->is_staff = $role == 'staff' ? 1 : 0;
        $accountObj->is_admin = $role == 'admin' ? 1 : 0;
        if ($accountObj->add()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong when adding the new account.']);
        }
        exit;
    }

}