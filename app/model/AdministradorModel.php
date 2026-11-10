<?php 

namespace app\model;

class AdministradorModel{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }
}