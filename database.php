<?php
//ARCHIVO DE CONFIGURACION DE CONEXIONES INTRANET.
class database extends PDO
{
    //nombre base de datos
    private $dbname;
    //nombre servidor
    private $host;
    //nombre usuarios base de datos
    private $user;
    //password usuario
    private $pass;
    //puerto postgreSql
    private $port;
    //---
    private $dbh;
    private $manejador;

    //creamos la conexión a la base de datos prueba
    public function __construct($host , $dbname, $user , $pass, $port , $manejador )
    {
        //set vars
        $this->dbname = $dbname;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
        $this->manejador = $manejador;

        try {
            $this->dbh = parent::__construct("$this->manejador:host=$this->host;port=$this->port;dbname=$this->dbname;user=$this->user;password=$this->pass");
            //$this->dbh->exec("SET NAMES 'UTF8'");
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    //función para cerrar una conexión pdo
    public function close_con()
    {
        $this->dbh = null;
    }
}

?>