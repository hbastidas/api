<?php

/**
 proyecto directorio institucional
 */
class dirinster extends Intranet
{
    private $data;
    private $fecha;
    public function __construct()
    {
        //ejecucion de elementos globales
        session_start();
        parent::__construct();
        $this->fecha = date('Y-m-d');

        //definir ambientes (false para desarrollo)
        //Ip Lisandro 192.168.21.132
        /*if (($_SERVER['REMOTE_ADDR'] == '192.168.21.132') || ($_SERVER['REMOTE_ADDR'] == '192.168.21.28')) {
            $this->appDirEntes = false;//ambiente Desarrollo
        } else {
            $this->appDirEntes = true;//ambiente Produccion
        }*/
        $this->appDirEntes = true;//ambiente Produccion
    }

    public function __destruct()
    {
        foreach ($this->data as $key => $value) {
            if ($this->data[$key]['descripcion'] != '') {
                $this->data[$key]['descripcion'] = trim(str_replace("\n", '', str_replace("\r\n", ' ', strip_html_tags($value['descripcion']))));
            }
        }

        echo json_encode($this->data, true);
        unset($this->data);
        parent::__destruct();
    }

    //este codigo es general a todas las consultas en la api.
    public function generalexc($conexion, $sql, $condiciones = array(), $debug = false)
    {
        $query = $this->$conexion->prepare($sql);



        $valido = $query->execute($condiciones);
        $this->data = $query->fetchAll(PDO::FETCH_ASSOC);
        if ($debug) {
            /*$query->debugDumpParams();
            print_r($query);*/
            var_dump($valido);
            $arr = $query->errorInfo();
            print_r($arr);
        }

        return $valido;
    }

    public function get_auth()
    {
        $this->data = $_SESSION;
    }

    public function get_pais()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'select codpai,trim(despai) as despai from directoriointerinstitucional.pais  where codpai>0 order by despai asc';
        $this->generalexc('con84', $sql);
    }

    public function get_estados()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'select codest,trim(desest) as desest ,codpai from directoriointerinstitucional.estados where codpai=:codpai and codest>0 order by desest asc';
        $condiciones = array(':codpai' => $_GET['codpai']);
        $this->generalexc('con84', $sql, $condiciones);
    }
    public function get_municipio()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'select codmun,trim(desmun) as desmun,codpai,codest from directoriointerinstitucional.municipio where codpai=:codpai and codest=:codest order by desmun asc';
        $condiciones = array(':codpai' => $_GET['codpai'],':codest' => $_GET['codest']);
        $this->generalexc('con84', $sql, $condiciones);
    }
    public function get_parroquia()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'select codpar, trim(despar) as despar, codmun, codest, codpai FROM directoriointerinstitucional.parroquia where codmun=:codmun and codest=:codest and codpai=:codpai order by despar asc';
        $condiciones = array(':codpai' => $_GET['codpai'],':codest' => $_GET['codest'],':codmun' => $_GET['codmun']);
        $this->generalexc('con84', $sql, $condiciones);
    }
    public function get_instituciones()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'select codentes, nombre_institucion, website,telefonos,observaciones FROM directoriointerinstitucional.institucion where auditoria is null order by nombre_institucion asc';
        $this->generalexc('con84', $sql);
    }
    /*function get_contactoinstituciones(){
        $this->conectar84db('SISTEMAS',$this->appDirEntes);
        $sql="select codentes, nombre_institucion,telefonos,observaciones  website FROM directoriointerinstitucional.institucion";
        $this->generalexc("con84",$sql);
    }*/

    //para revisar
    public function get_busqueda()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);     
        $sql = 'SELECT codcontacto, nombre, apellido, foto, codentes, nombre_institucion,website, telefono_insti,telefono_ofi, telefono_cel,
        cargo, email, observaciones_equipo, observaciones,codpai, despai, codest, desest, codmun, desmun, codpar,
        despar,direccion_detallada FROM directoriointerinstitucional.main01 ';
        $datosbusqueda = explode(' ', $_GET['q']);

        $c = 0;
        foreach ($datosbusqueda as $key => $value) {
            if ($c > 0) {
                $busqueda .= ' and ';
            }
            $busqueda .= " busqueda ilike '%".$value."%'";
            $c++;
        }

        if (trim($_GET['q']) != '') {
            $buscador = ' where '.$busqueda;
        }

        //$condiciones=array(':busqueda' => "'%".$_GET['q']."%'");
        $this->generalexc('con84', $sql.$buscador." order by nombre_institucion asc", null);
        //alg ordenacion..
        //$reindex[0]=null;

        foreach ($this->data as $key => $value) {

            
            $reindex[$value['nombre_institucion']]['nombre'] = $value['nombre_institucion'];
            $reindex[$value['nombre_institucion']]['website'] = $value['website'];
            $reindex[$value['nombre_institucion']]['telefono_insti'] = $value['telefono_insti'];
            $reindex[$value['nombre_institucion']]['contactos'][] = $value;
        }
        $this->data = $reindex;
    }
    public function get_busqueda2()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'SELECT codcontacto, nombre, apellido, foto, codentes, nombre_institucion, telefono_ofi, telefono_cel,
        cargo, email, observaciones_equipo, codpai, despai, codest, desest, codmun, desmun, codpar,
        despar, observaciones, direccion_detallada FROM directoriointerinstitucional.main01';
        $datosbusqueda = explode(' ', $_GET['q']);

        $c = 0;
        foreach ($datosbusqueda as $key => $value) {
            if ($c > 0) {
                $busqueda .= ' and ';
            }
            $busqueda .= " busqueda ilike '%".$value."%'";
            $c++;
        }

        if (trim($_GET['q']) != '') {
            $buscador = ' where '.$busqueda;
        }

        //$condiciones=array(':busqueda' => "'%".$_GET['q']."%'");
        $this->generalexc('con84', $sql.$buscador, null);
        //alg ordenacion..
        //$reindex[0]=null;
    }
    public function get_busqueda3()
    {
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'SELECT codentes, nombre_institucion, website, telefonos, observaciones FROM directoriointerinstitucional.main02';
        $datosbusqueda = explode(' ', $_GET['q']);

        $c = 0;
        foreach ($datosbusqueda as $key => $value) {
            if ($c > 0) {
                $busqueda .= ' and ';
            }
            $busqueda .= " busqueda ilike '%".$value."%'";
            $c++;
        }

        if (trim($_GET['q']) != '') {
            $buscador = ' where '.$busqueda;
        }

        //$condiciones=array(':busqueda' => "'%".$_GET['q']."%'");
        $this->generalexc('con84', $sql.$buscador, null);
        //alg ordenacion..
        //$reindex[0]=null;
    }

    public function post_guardarcontacto()
    {

        /*ob_start();
        PR($_FILE);
        $resultados=ob_get_contents();
        ob_end_clean();*/

        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'INSERT INTO directoriointerinstitucional.contacto(
            nombre, apellido, foto, codentes, telefono_ofi,
            telefono_cel, cargo, email, observaciones_equipo, codpai, codest,
            codmun, codpar, observaciones, direccion_detallada)
            VALUES (:nombrecontact ,:apellidocontact,:foto,:instituciones,:tlf_oficina,
                    :tlf_celular,:cargo,:email,:observaciones_equipo,:pais,:estado,
                    :municipio,:parroquia,:observaciones,:direccion_detallada) RETURNING codcontacto;';

        $datos = array(':nombrecontact' => mb_strtoupper($_POST['nombrecontact']),':apellidocontact' => $_POST['apellidocontact'],':foto' => null,
        ':instituciones' => $_POST['instituciones'],':tlf_oficina' => $_POST['tlf_oficina'],':tlf_celular' => $_POST['tlf_celular'],
        ':cargo' => mb_strtoupper($_POST['cargo']),':email' => $_POST['email'],':observaciones_equipo' => mb_strtoupper($_POST['observaciones_equipo']),':pais' => $_POST['pais'],
        ':estado' => $_POST['estado'],':municipio' => $_POST['municipio'],':parroquia' => $_POST['parroquia'], ':observaciones' => mb_strtoupper($_POST['observaciones']),  ':direccion_detallada' => mb_strtoupper(str_replace("–", "-", $_POST['direccion_detallada'])));
        $url = explode('?', $_SERVER['HTTP_REFERER']);


        $valido = $this->generalexc('con84', $sql, $datos, true);
       

        $idcontacto = $this->data[0]['codcontacto'];
        /// Imagen
        if ($_FILES['foto']['tmp_name'] != '') {
            $idcontacto = $this->data[0]['codcontacto'];
            list($valor, $extension) = explode('/', $_FILES['foto']['type']);
            $dir = 'resources/dirinster/img';
            $tmp_name = $_FILES['foto']['tmp_name'];
            $name = $idcontacto.'.'.$extension;
            $directorio_subida = "$dir/$name";
            move_uploaded_file($tmp_name, "$directorio_subida");
            $update = true;
        }
        /// Imagen
        if ($valido) {
            if ($update) {
                //Imagen
                $this->conectar84db('SISTEMAS', $this->appDirEntes);
                $sql1 = 'update directoriointerinstitucional.contacto SET foto=:foto WHERE codcontacto=:idcontacto';
                $condiciones1 = array(':idcontacto' => $idcontacto,':foto' => $directorio_subida);
                $this->generalexc('con84', $sql1, $condiciones1);
                //Imagen
            }
            header('Location: '.$url[0].'?mensaje='.base64_encode('success'));
        } else {

            header('Location: '.$url[0].'?mensaje='.base64_encode('danger'));
        }
    }

    public function post_modificarinstitucion(){
        $url = explode('?', $_SERVER['HTTP_REFERER']);
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql1 = 'UPDATE directoriointerinstitucional.institucion
        SET nombre_institucion=:nombreinstitucion, website=:website, telefonos=:telefonos, observaciones=:observaciones
        WHERE codentes=:codentes;';
        $datos = array(':nombreinstitucion'=> mb_strtoupper($_POST['nombreinstitucion']) , ':website'=> $_POST['web'], ':telefonos'=> $_POST['telefonos'] , ':observaciones' => mb_strtoupper($_POST['observaciones']) , ':codentes' => $_POST['identes']);
        $valido = $this->generalexc('con84', $sql1, $datos,true);
        if ($valido) {
            header('Location: '.$url[0].'?mensaje='.base64_encode('success'));
        } else {
            header('Location: '.$url[0].'?mensaje='.base64_encode('danger'));
        }

    }

    public function post_delentes(){
        //print_r($_POST);
        //Array ( [identes] => 11 [logcedula] => 17143819 [user] => HEBER [ape] => BASTIDAS ) null
        $url = explode('?', $_SERVER['HTTP_REFERER']);
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql1 = 'UPDATE directoriointerinstitucional.institucion
        SET auditoria=:auditoria
        WHERE codentes=:codentes;';
        $datos = array(':auditoria'=> $_POST['user']." ".$_POST['ape']." ".$_POST['logcedula']." ".date("Y-m-d H:i") ,':codentes' => $_POST['identes']);
        $valido = $this->generalexc('con84', $sql1, $datos,true);
        if ($valido) {
            header('Location: '.$url[0].'?mensaje='.base64_encode('success'));
        } else {
            header('Location: '.$url[0].'?mensaje='.base64_encode('danger'));
        }
    }

    public function post_delcontacto(){
        //print_r($_POST);
        $url = explode('?', $_SERVER['HTTP_REFERER']);
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql1 = 'UPDATE directoriointerinstitucional.contacto
        SET auditoria=:auditoria
        WHERE codcontacto=:codcontacto;';
        $datos = array(':auditoria'=> $_POST['user']." ".$_POST['ape']." ".$_POST['logcedula']." ".date("Y-m-d H:i") ,':codcontacto'=> $_POST['codcontacto']);
        $valido = $this->generalexc('con84', $sql1, $datos,true);
        if ($valido) {
            header('Location: '.$url[0].'?mensaje='.base64_encode('success'));
        } else {
            header('Location: '.$url[0].'?mensaje='.base64_encode('danger'));
        }
    }

    public function post_modificarcontacto(){
        /*
Array ( [nombrecontact] => dsfsdfsdf [apellidocontact] => sdfdsfsdf [instituciones] => 12 [tlf_oficina] => 545 [tlf_celular] => 5454 [cargo] => dsfsdfdsfsdf [email] => heberbastidas@gmail.com [observaciones] => sdfdsf [observaciones_equipo] => sdfsdf [pais] => 165 [estado] => 1 [municipio] => 1 [parroquia] => 1 [idcontacto] => 45 )
        */
        $url = explode('?', $_SERVER['HTTP_REFERER']);
        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql1 = 'UPDATE directoriointerinstitucional.contacto
        SET nombre=:nombrecontact, apellido=:apellidocontact, codentes=:instituciones, 
        telefono_ofi=:tlf_oficina, telefono_cel=:tlf_celular, cargo=:cargo, email=:email, observaciones_equipo=:observaciones_equipo, 
        codpai=:pais, codest=:estado, codmun=:municipio, codpar=:parroquia, observaciones=:observaciones, direccion_detallada=:direccion_detallada
        WHERE codcontacto=:codcontacto;';

        $datos = array(':codcontacto'=> $_POST['idcontacto'] , ':nombrecontact' => mb_strtoupper($_POST['nombrecontact']),':apellidocontact' => mb_strtoupper($_POST['apellidocontact']),
        ':instituciones' => $_POST['instituciones'],':tlf_oficina' => $_POST['tlf_oficina'],':tlf_celular' => $_POST['tlf_celular'],
        ':cargo' => mb_strtoupper($_POST['cargo']),':email' => $_POST['email'],':observaciones_equipo' => mb_strtoupper($_POST['observaciones_equipo']),':pais' => $_POST['pais'],
        ':estado' => $_POST['estado'],':municipio' => $_POST['municipio'],':parroquia' => $_POST['parroquia'], ':observaciones' => mb_strtoupper($_POST['observaciones']) , ':direccion_detallada' => mb_strtoupper(str_replace("–", "-", $_POST['direccion_detallada'])));
        
        /*print_r($_POST);
        print_r($datos);*/
        $valido = $this->generalexc('con84', $sql1, $datos,true);
        if ($_FILES['foto']['tmp_name'] != '') {
            $idcontacto = $this->data[0]['codcontacto'];
            list($valor, $extension) = explode('/', $_FILES['foto']['type']);
            $dir = 'resources/dirinster/img';
            $tmp_name = $_FILES['foto']['tmp_name'];
            $name = $idcontacto.'.'.$extension;
            $directorio_subida = "$dir/$name";
            move_uploaded_file($tmp_name, "$directorio_subida");
            $update = true;
        }

        if ($valido) {
            if ($update) {
                //Imagen
                $this->conectar84db('SISTEMAS', $this->appDirEntes);
                $sql1 = 'update directoriointerinstitucional.contacto SET foto=:foto WHERE codcontacto=:idcontacto';
                $condiciones1 = array(':idcontacto' => $idcontacto,':foto' => $directorio_subida);
                $this->generalexc('con84', $sql1, $condiciones1);
                //Imagen
            }
            header('Location: '.$url[0].'?mensaje='.base64_encode('success'));
        } else {
            header('Location: '.$url[0].'?mensaje='.base64_encode('danger'));
        }


    }

    public function post_guardarinstitucion()
    {

        /*ob_start();
        PR($_FILE);
        $resultados=ob_get_contents();
        ob_end_clean();*/

        $this->conectar84db('SISTEMAS', $this->appDirEntes);
        $sql = 'INSERT INTO directoriointerinstitucional.institucion(nombre_institucion, website, telefonos,observaciones)VALUES (:nombre_institucion,:website,:telefonos,:observaciones)';
        $datos = array(':nombre_institucion' => mb_strtoupper($_POST['nombreinstitucion']),':website' => trim($_POST['web']),':telefonos' => $_POST['telefonos'],':observaciones' => mb_strtoupper($_POST['observaciones']));
        $url = explode('?', $_SERVER['HTTP_REFERER']);
        if ($this->generalexc('con84', $sql, $datos)) {
            header('Location: '.$url[0].'?mensaje='.base64_encode('success'));
        } else {
            header('Location: '.$url[0].'?mensaje='.base64_encode('danger'));
        }
    }
}
