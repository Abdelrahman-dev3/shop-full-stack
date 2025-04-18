<?php
class Data {
    private $username;
    private $password;
    private $email;
    private $fullname;
        # Get Data
    public function get_name(){
        return $this->username ;
    }
    public function get_password(){
        return $this->password ;
    }
    public function get_email(){
        return $this->email ;
    }
    public function get_fullname(){
        return $this->fullname ;
    }
        # Set Data
    public function set_name($username){
        $this->username = $username ;
    }
    public function set_password($password){
        $this->password = sha1($password) ;
    }
    public function set_email($email){
        $this->email = $email ;
    }
    public function set_fullname($fullname){
        $this->fullname = $fullname ;
    }
}