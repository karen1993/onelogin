<?php
require_once('lib/consultas_instancia.php');

class ci_solicitud_usuarios extends onelogin_ci
{
    
    
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
    
    
    function conf__form_solicitud(toba_ei_formulario $form)
    {

    }
    
    function evt__form_solicitud__alta($datos)
    {
        
        $fecha = date('d-m-y');
        $datos['timestamp'] = $fecha;        
        $datos['id_estado'] = 'PEND';
        $caracteres_permitidos_nom = ctype_alpha($datos['nombre']);
        $caracteres_permitidos_ape = ctype_alpha($datos['apellido']);
        if(!$caracteres_permitidos_nom || !$caracteres_permitidos_ape) {
            throw new toba_error('El nombre o apellido no es válido, vuelva a ingresar');  
        } else {
                $datos['nombre'] = strtolower($datos['nombre']);
                $datos['apellido'] = strtolower($datos['apellido']);
                $datos['apellido'] = str_replace(' ', '', $datos['apellido']);
                $usuario = $datos['nombre'][0].$datos['apellido'];
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
                    $datos['clave'] = $datos['nombre'].'.'.date('Y');
                    $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
                    $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
                    $this->dep('datos')->tabla('solicitud_usuario')->resetear();
           
                    toba::notificacion()->agregar('La solicitud de usuario se ha realizado correctamente. En breve recibira un mail para la confirmacion de la solicitud', 'info');

                } else {
                    throw new toba_error('Usted ya tiene un usuario, ingrese al sistema y complete el formulario de solicitud correspondiente');
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
//    function caracteres_permitidos($cadena)
//    {
//        $permitidos = 
//    }
}

?>