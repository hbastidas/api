<?php
/**
proyecto pantalla
*/

class api extends Intranet
{
    private $data;
    private $fecha;
    private $fecha1;
    private $fecha_hasta;
    public function __construct()
    {
        //ejecucion de elementos globales
        parent::__construct();
        //para presentar las notas de la semana.
        $this->fecha=date("Y-m-d", strtotime(date("Y-m-d")." - 15 day"));       
        $this->fecha_hasta=date("Y-m-d", strtotime(date("Y-m-d")." + 30 day"));
        //Para presentrar las pautas en estudio o remoto
        $this->fecha1=date("Y-m-d", strtotime(date("Y-m-d")));
        /*if($_SERVER['REMOTE_ADDR']=="192.168.21.132"){
            print_r($this->fecha_hasta);
            print_r("\n");
            print_r(" fecha: " . $this->fecha);
            print_r("\n");
        }*/

    }

    function __destruct()
    {

        foreach ($this->data as $key => $value) {
            if ($this->data[$key]['descripcion']!="") {
                $this->data[$key]['descripcion']=trim(str_replace("\n", "", str_replace("\r\n", " ", strip_html_tags($value['descripcion']))));
            }
        }


        echo json_encode($this->data, true);
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
    function get_eventos()
    {
        /*print_r("\n");
        print_r(" SQL " . $sql);
        print_r("\n");*/
       /*if($_SERVER['REMOTE_ADDR']=="192.168.21.132"){
            
        }else{
            /*$sql="select id_eveinfo, titulo,  regexp_replace(intranet.strip_tags(descripcion), E'[\\n\\r]+', ' ', 'g' ) as descripcion , fecha_publi, fecha_desde, fecha_hasta, archivo, imagen  from relaciones_publicas.t_intranet_eveinfo
                where
                (fecha_desde >= :desde and
                fecha_hasta <= :hasta) and
                fecha_exp=:exp and
                id_tipo_eveinfo=:id_t_event";
            $condiciones=array(':desde' => "'$this->fecha'", ':hasta' => "'$this->fecha_hasta'", ':exp' => '2222-12-31', ':id_t_event' => '1');
        }
        */
        $this->conectar82();
        $sql="select id_eveinfo, titulo,  
             regexp_replace(intranet.strip_tags(descripcion), E'[\\n\\r]+', ' ', 'g' ) as descripcion , 
             fecha_publi, 
             fecha_desde, 
             fecha_hasta, 
             archivo, 
             imagen  
             from relaciones_publicas.t_intranet_eveinfo                   
            where (fecha_desde <= :desde and fecha_hasta >= :hasta) and
            fecha_exp=:exp 
            and 
            id_tipo_eveinfo= :id_t_event";
            $condiciones=array( ':desde' => "'$this->fecha1'", 
                                ':hasta' => "'$this->fecha1'", 
                                ':exp' => '2222-12-31', 
                                ':id_t_event' => '1');
        $this->generalexc("con82", $sql, $condiciones);
    
    }


    function get_cumpleano()
    {
        $this->conectarsigesp();
        $sql="select nl_apelynom::varchar, de_gerencia::varchar, date as dia, cedula from v_sistemas_vtv_cumpleaneros_dia";
        $this->generalexc("sigesp", $sql);
        //transformar los datos

        foreach ($this->data as $key => $value) {
            $datos[$value['de_gerencia']][]=array("nl_apelynom"=>$value['nl_apelynom'], "cedula"=>$value['cedula'], "dia"=>$value['dia'] );
        }
        $this->data=$datos;
        unset($datos);
    }

    /**
    esta funcion se encarga de enviar datos relacionados a los video de vtvcanal8
    */
    function get_videos()
    {
        $this->conectar82();
        $sql="select id_videos,
                titulo,
                descripcion,
                fecha_publi,
                archivo,
                imagen 
                from relaciones_publicas.t_intranet_videos
                where
                fecha_exp = :fecha_exp and
                id_tipo_videos = :id_tipo_videos and
                fecha_publi > :fecha_publi 
                ORDER BY id_videos desc limit 5";       
        $condiciones=array(':fecha_exp' => '2222-12-31' , 
                            ':id_tipo_videos' =>0 , 
                            ':fecha_publi' => '2016-01-01');
        $this->generalexc("con82", $sql, $condiciones);       
    }

    //se ejecuta funcion de cintillo de anuncios N1.
    function get_cintillo1()
    {
        $this->conectar82();
            $sql="select id_cintillo,descripcion,fecha_publi,fecha_desde,fecha_hasta from relaciones_publicas.t_intranet_cintillo
                where
                (fecha_desde >= :desde and
                fecha_hasta <= :hasta) and
                fecha_exp=:exp
                order by fecha_publi ASC";
            $condiciones=array(':desde' => "'$this->fecha'", ':hasta' => "'$this->fecha_hasta'", ':exp' => '2222-12-31');
        $this->generalexc("con82", $sql, $condiciones);
    }

    function get_cintillo2()
    {
        $this->conectar82();
        $sql="select id_eveinfo, titulo,  descripcion , fecha_publi, fecha_desde, fecha_hasta, archivo, imagen  from relaciones_publicas.t_intranet_eveinfo
            where
            (fecha_desde >= :desde and
            fecha_hasta <= :hasta) and
            fecha_exp=:exp and
            id_tipo_eveinfo=:id_t_event
            order by id_eveinfo desc";
        $condiciones=array(':desde' => "'$this->fecha'", ':hasta' => "'$this->fecha_hasta'", ':exp' => '2222-12-31', ':id_t_event' => '1');
        $this->generalexc("con82", $sql, $condiciones);
    }


    //este esta desabilitada para la pantalla
    function get_cintillo3()
    {
        $this->conectar82();
        $sql="select id_cintillo_pantalla,descripcion,fecha_publi,fecha_desde,fecha_hasta from relaciones_publicas.t_intranet_cintillopantalla
            where
            (fecha_desde >= :desde and
            fecha_hasta <= :hasta) and
            fecha_exp=:exp
            order by fecha_publi ASC";
        $condiciones=array(':desde' => "'$this->fecha'", ':hasta' => "'$this->fecha_hasta'", ':exp' => '2222-12-31');
        $this->generalexc("con82", $sql, $condiciones);
    }

    function get_pauta()
    {
        $this->conectar82();
        $sql="select id_pauta, substring(detalle for 55) as detalle,id_pauta,fecha,hora,fecha_publi,fecha_exp,ip_exp,hostname_exp, ced_exp, hostname_reg, ced_reg from relaciones_publicas.t_intranet_pauta";


        if ($_GET['tpauta']!="") {
            settype($tpauta, int);
            $tpauta=$_GET['tpauta'];
            $sql.=" where id_tpauta=:tpauta and fecha_exp=:exp and fecha=:fecha order by hora asc";
            $condiciones=array(":tpauta"=>$tpauta,  ':exp' => '2222-12-31', ":fecha"=>"'$this->fecha1'");
        }


        $this->generalexc("con82", $sql, $condiciones);
    }
}
