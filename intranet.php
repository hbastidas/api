<?php

//manejador de base de datos
require 'database.php';


function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

class Intranet
{
    public $con82 = null;
    public $con84 = null;
    public $sigesp= null;

    public function __construct()
    {
    }

    public function conectar82($ambiente=true)
    {
        if($ambiente){
            $this->con82 = new Database('172.16.8.224', 'INTRANET', 'postgres', '123456', '5433', 'pgsql');
            $this->con82->exec("SET NAMES 'UTF8'");
        }else{
           $this->con84 = new Database('172.16.8.223', 'INTRANET', 'postgres', '123456', '5433', 'pgsql');
           $this->con84->exec("SET NAMES 'UTF8'");  
        }
    }

    public function conectar84($ambiente=true)
    {
	if($ambiente){
		$this->con84 = new Database('172.16.8.224', 'INTRANET', 'postgres', '123456', '5431', 'pgsql');
        $this->con82->exec("SET NAMES 'UTF8'");	
	}else{		
		$this->con84 = new Database('172.16.8.223', 'INTRANET', 'postgres', 'postgres', '5431', 'pgsql');
        $this->con84->exec("SET NAMES 'UTF8'");	
	}

    }

    public function conectar84db($basedata, $ambiente=true){
        if($ambiente){
            $this->con84 = new Database('172.16.8.224', $basedata, 'postgres', '123456', '5431', 'pgsql');
            $this->con84->exec("SET NAMES 'UTF8'"); 
            //$this->con84->exec("SET client_encoding = 'UTF8'");
        }else{
            $this->con84 = new Database('172.16.8.223', $basedata, 'postgres', 'postgres', '5431', 'pgsql');
            $this->con84->exec("SET NAMES 'UTF8'"); 
        }
    }

    public function conectarsigesp(){
        $this->sigesp = new Database('172.16.8.224', 'VTV_2015', 'INTRANET', 'INTRANET', '5430', 'pgsql');
        $this->sigesp->exec("SET NAMES 'UTF8'");
    }

    public function __destruct()
    {
        if ($this->con82 != null) {
            $this->con82->close_con();
        }

        if ($this->con84 != null) {
            $this->con84->close_con();
        }

        if ($this->sigesp != null) {
            $this->sigesp->close_con();
        }

    }
}
