<?php

require 'core/jwt.php';

class Auth{

private $table_acc = 'tbl_accounts';
private $table_session = 'tbl_login_session';

protected $conn = null;


    function __construct(){
        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    function login($data){
        $this->conn->selectQuery("id, firstname, lastname, status, user_role, email", "{$this->table_acc} 
                                WHERE username = '{$data['username']}' && password='{$data['password']}' LIMIT 1");
        
        if($this->conn->getNumRows() > 0){

            $res= $this->conn->getFields();
            $acc_status = $res['aaData'][0]['status'];

            if($acc_status === 'INACTIVE'){
                $data = array('status' => 'inactive');
            }
            else{
                $expiration = time() + (1 * 24 * 60 * 60); //default expiration is one day.
                if(session_id() === ''){
                    session_start();
                    $sid= session_id();
                }
                
                $jwt = JWT::encode($res['aaData'][0]);
                $data = array(
                    'token'  => $jwt,
                    'status' => $res['status'],
                    'aaData' => $res['aaData'][0]
                );
                //store cookies to the browser.
                setcookie('token', $jwt, $expiration, '/');
                setcookie('sid', $sid, $expiration, '/');

                //set session in the server.
                $_SESSION['token'] = $jwt;
                $_SESSION['sid'] = $sid;
            }
        }
        else{
            $data = array('status' => 'invalid');
        }

        return $data;
    }

    function logout(){
        // Initialize the session.
        // If you are using session_name("something"), don't forget it now!
        session_start();

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', -1,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        return array('status' => 'success');

    }

}