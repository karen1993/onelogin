<?php
require_once('lib/consultas_instancia.php');

class ci_solicitud_usuarios extends onelogin_ci
{

    public $verificar = false;
    
    //----------------------------------------------------------------------------------
    //---------------Formulario Solicitud----------------------------------------------
    
    function conf__form_solicitud(toba_ei_formulario $form)
    {
      
    }
    
    function evt__form_solicitud__alta($datos)
    {
        $fecha = date('d-m-Y H:i:s');
        $datos['timestamp'] = $fecha;     
        $datos['id_estado'] = 'PEND';
        $caracteres_permitidos_nom = $this->caracteres_permitidos($datos['nombre']);
        $caracteres_permitidos_ape = $this->caracteres_permitidos($datos['apellido']);
        if(!$caracteres_permitidos_nom || !$caracteres_permitidos_ape) {
            throw new toba_error(utf8_decode('El nombre y/o apellido no es válido, vuelva a ingresar'));  
        } else {
                $apellido_compuesto = $this->buscar_espacio($datos['apellido']);
                if($apellido_compuesto) {
                    $apellido = str_replace(' ', '', $datos['apellido']);
                    if(strlen($apellido) > 10)
                    {
                        $apellido = '';
                        $i = 0;
                        while($i < strlen($datos['apellido'])) {
                            if($datos['apellido'][$i] != ' ') {
                                $apellido = $apellido.$datos['apellido'][$i];
                            } else {
                                $i = strlen($datos['apellido']);
                            }
                            $i++;
                        }
                    }
                } else {
                    $apellido = $datos['apellido'];
                }
                $apellido = $this->convertir($apellido);
                $nombre_compuesto = $this->buscar_espacio($datos['nombre']);
                if($nombre_compuesto) {
                    $i = 0;
                    while($i < strlen($datos['nombre'])) {
                        if($datos['nombre'][$i] != ' ') {
                            $nombre = $nombre.$datos['nombre'][$i];
                        } else {
                            $i = strlen($datos['nombre']);
                        }
                        $i++;
                    }                    
                } else {
                    $nombre = $datos['nombre'];
                }
                $nombre = $this->convertir($nombre);
                $usuario = strtolower($nombre[0].$apellido);
                $solicitud_existente = consultas_instancia::existe_solicitud($usuario,$datos['correo'],$datos['id_sistema']);
//                print_r($datos['id_sistema']);                exit();
                if($solicitud_existente != null) {
                    echo utf8_decode('<script language="javascript">alert("Usted ya tiene una solicitud pendiente, aguarde a que la misma sea atendida y recibirá un email con la información correspondiente");window.location.href="?ai=onelogin||1000292&tcm=previsualizacion&tm=1"</script>');
                } else {
                    $es_usuario = consultas_instancia::get_es_usuario($datos['correo']);
                    $usuario_en_solicitud = consultas_instancia::existe_usuario_solicitud($usuario);
                    $nomb_usuario_existente = consultas_instancia::get_existe_usuario($usuario);
                    $num = 01;
                    if(!$es_usuario) {
                        while($nomb_usuario_existente == 1 || $usuario_en_solicitud == 1)
                        {
                            $usuario = $usuario.$num;
                            $nomb_usuario_existente = consultas_instancia::get_existe_usuario($usuario);
                            $usuario_en_solicitud = consultas_instancia::existe_usuario_solicitud($usuario);
                            $num++;
                        }
                        $datos['nombre_usuario'] = $usuario;
                    
                        $datos['clave'] = $nombre.'.'.date('Y');
                        $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
                        $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
                        $this->dep('datos')->tabla('solicitud_usuario')->resetear();
                    
                        echo '<script language="javascript">alert("La solicitud de usuario se ha realizado correctamente. En breve recibira un mail para la confirmacion de la solicitud");window.location.href="?ai=onelogin||1000292&tcm=previsualizacion&tm=1"</script>';
                    } else {
                        echo '<script language="javascript">alert("Usted ya tiene un usuario, ingrese al sistema y complete el formulario de solicitud correspondiente");window.location.href="?ai=onelogin||1000292&tcm=previsualizacion&tm=1"</script>';
                    }
                }              
        } 
           

    }
    
    
    //-------------------Eventos CI----------------------------
    //---------------------------------------------------------
    function evt__volver()
	{
            echo toba_js::abrir();
            echo 'toba.ir_a_operacion("onelogin", "1000292", false) ';
            echo toba_js::cerrar();
                       
	}
    
    //---------------------------------------------------------------------
    //---------------------Funciones Extras--------------------------------
        
    function get_lista_perfil_datos($proyecto)
    {
                $proyecto = quote($proyecto);
		$sql = "SELECT 	proyecto,
						usuario_perfil_datos,
						nombre,
						descripcion						
				FROM 	apex_usuario_perfil_datos 
				WHERE	proyecto = $proyecto";
		return toba::db()->consultar($sql);

        
    }
    
    function get_perfiles_funcionales($proyecto)
    {
        $perfiles_funcionales = consultas_instancia::get_lista_grupos_acceso_proyecto($proyecto);
        $datos = array();
        $a = 0;
        if($proyecto == 'designa') {
            $datos[0]['nombre'] = 'InvestigacionDirector';
            $datos[0]['perfil_funcional'] = 'investigacion_director';
        } else {
            foreach($perfiles_funcionales as $perfil)
            {
                if($perfil['usuario_grupo_acc'] != 'admin') {
                    $datos[$a]['perfil_funcional'] = $perfil['usuario_grupo_acc'];
                    $datos[$a]['nombre'] = $perfil['nombre'];
                    $a++;
                }
            }
        }
        
        return $datos;
    }
    
    
    function caracteres_permitidos($cadena)
    {
        $permitidos = utf8_decode("áéíóúÁÉÍÓÚabcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ ");
        for ($i=0; $i<strlen($cadena); $i++){
            if (strpos($permitidos, substr($cadena,$i,1))===false){
                return false;
            }
        }
        
       return true;
    }
    
    function buscar_espacio($nombre)
    {
        for ($i = 0;$i<strlen($nombre);$i++) {
            if($nombre[$i] == ' ') {
                return true;
            }
        }
        return false;
    }
    
    function convertir($cadena) {
        for($i = 0;$i < strlen($cadena);$i++) {
            switch ($cadena[$i]) {
                case utf8_decode('á');
                    $cadena[$i] = 'a';
                    break;
                case utf8_decode('é');
                    $cadena[$i] = 'e';
                    break;
                case utf8_decode('í');
                    $cadena[$i] = 'i';
                    break;
                case utf8_decode('ó');
                    $cadena[$i] = 'o';
                    break;
                case utf8_decode('ú');
                    $cadena[$i] = 'u';
                    break;
                case utf8_decode('Á');
                    $cadena[$i] = 'A';
                    break;
                case utf8_decode('É');
                    $cadena[$i] = 'E';
                    break;
                case utf8_decode('Í');
                    $cadena[$i] = 'I';
                    break;
                case utf8_decode('Ó');
                    $cadena[$i] = 'O';
                    break;
                case utf8_decode('Ú');
                    $cadena[$i] = 'U';
                    break;
                case utf8_decode('ñ');
                    $cadena[$i] = 'n';
                    break;
                case utf8_decode('Ñ');
                    $cadena[$i] = 'N';
                    break;
            }
        }
        $cadena = strtolower($cadena);
        return $cadena;

    }
}

?>