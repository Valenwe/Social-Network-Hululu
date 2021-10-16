<?php

class User
{
    public $firstName;
    public $lastName;
    public $birthDate;
    public $gender;

    public $id;

    public function setVariables($firstName, $lastName, $birthDate, $gender, $id)
    {
        $this . $firstName = $firstName;
        $this . $lastName = $lastName;
        $this . $birthDate = $birthDate;
        $this . $gender = $gender;
        $this . $id = $id;
    }
}
