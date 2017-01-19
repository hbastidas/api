<?php
require("intranet.php");
function __autoload($classname) {
    $filename = "app/". $classname .".php";
    include_once($filename);
}

if($_GET['app']==""){
    $app="api";
}else{
    $app=$_GET['app'];
}

$objeto=new $app();
$method = $_SERVER['REQUEST_METHOD'];
// tendremos que tratar esta variable para obtener el recurso adecuado de nuestro modelo.
$resource = $_SERVER['REQUEST_URI'];
// Dependiendo del método de la petición ejecutaremos la acción correspondiente.
switch ($method) {
    case 'GET':
        // código para método GET
        $accion="get_".$_GET['model'];
        $objeto->$accion();
        break;
    case 'POST':
        $accion="post_".$_GET['model'];
        $objeto->$accion();
        // código para método POST
        break;
    case 'PUT':
        // parse_str(file_get_contents('php://input'), $arguments);
        // código para método PUT
        break;
    case 'DELETE':
        // código para método DELETE
        break;
}
