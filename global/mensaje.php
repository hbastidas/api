<?php

class mensajeserver
{
    private $mensaje;

    public function __construct()
    {
    }

    public function decripta($mensaje)
    {
        $mensaje = base64_decode($mensaje);
        $this->mensaje = $this->$mensaje();
    }

    public function __destruct()
    {
    }

    public function display()
    {
        echo $this->mensaje;
    }

    public function get()
    {
        return $this->mensaje;
    }

    private function success()
    {


        return '<div class="alert alert-success" role="alert">
                <strong>Exito!</strong> Se completo con exito la operacion.
              </div>
              <script>setTimeout(function(){ $(".alert-success").fadeOut(); }, 1000);</script>';
    }

    private function danger()
    {
        return '<div class="alert alert-danger" role="alert">
                        <strong>Ocurrio algo Inesperado!</strong> No se pudo completar con exito la operacion.
                </div>
                <script>setTimeout(function(){ $(".alert-danger").fadeOut(); }, 3000);</script>';
    }
}
