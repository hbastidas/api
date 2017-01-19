<?php
/**
proyecto global (todos los sistemas usan parte de este proyecto)
*/

class apiglobal extends Intranet
{
    private $data;
    private $fecha;
    public function __construct()
    {
        //ejecucion de elementos globales
        session_start();
        parent::__construct();
        $this->fecha=date("Y-m-d");
    }

    function __destruct()
    {
        //echo json_encode($this->data);
        unset($this->data);
        parent::__destruct();
    }

    //este codigo es general a todas las consultas en la api.
    function generalexc($conexion, $sql, $condiciones = array(), $debug = false)
    {
        $query = $this->$conexion->prepare($sql);
        $valido=$query->execute($condiciones);
        $this->data=$query->fetchAll(PDO::FETCH_ASSOC);
        if ($debug) {
            $query->debugDumpParams();
            print_r($query);
            $arr = $query->errorInfo();
            print_r($arr);
        }

        return $valido;

    }

    /**
    esta funcion se encarga de enviar datos relacionados a los eventos de vtvcanal8
    */
    function get_accesointranet($idapp, $idmod, $cedula)
    {
        $this->conectar82();
        $sql="SELECT cedula, fecha_exp, fecha_trans, id_modulo, id_aplicacion, niv_con, niv_eli, niv_inc, niv_mod, ced_trans, id_acceso FROM intranet.t_acceso WHERE id_aplicacion=:idapp and id_modulo=:idmod and cedula=:cedula and fecha_exp='2222-12-31';";
        $condiciones=array(':idapp' => $idapp, ':idmod' => $idmod, ':cedula' => $cedula);
        $this->generalexc("con82", $sql, $condiciones);


        return $this->data;
    }
}
