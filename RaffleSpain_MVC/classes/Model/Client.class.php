<?php

class Client {
    
    private $id;
    private $name;
    private $password;
    private $surnames;
    private $born;
    private $email;
    private $phone;
    private $sex;
    private $poblation;
    private $address;
    private $type;
    
    public function __construct($id, $name = null, $password = null, $surnames = null, $born = null, $email = null, $phone = null, $sex = null, $poblation = null, $address = null, $type = 0) {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->surnames = $surnames;
        $this->born = $born;
        $this->email = $email;
        $this->phone = $phone;
        $this->sex = $sex;
        $this->poblation = $poblation;
        $this->address = $address;
        $this->type = $type;
    }
    
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new Exception("No existeix la propietat $property a Entrada");
        }
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new Exception("No existeix la propietat $property a Entrada");
        }
    }
    
    public function __debugInfo() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'password' => $this->password,
            'surnames' => $this->surnames,
            'born' => $this->born,
            'email' => $this->email,
            'phone' => $this->phone,
            'sex' => $this->sex,
            'poblation' => $this->poblation,
            'address' => $this->address,
            'type' => $this->type,
        ];
    }
    
}