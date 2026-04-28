<?php

class User {
    private UUIDv7 $id;
    private UserName $name;
    private Email $email;
    private Password $password;

    public function __construct($id, $name, $email, $password) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}

?>