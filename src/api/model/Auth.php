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

}