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
        foreach($perfiles_funcionales as $perfil)
        {
            
            $datos[$a]['perfil_funcional'] = $perfil['usuario_grupo_acc'];
            $datos[$a]['nombre'] = $perfil['nombre'];
            $a++;
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
        $datos['nombre'] = strtolower($datos['nombre']);
        $datos['apellido'] = strtolower($datos['apellido']);
        $usuario = $datos['nombre'][0].$datos['apellido'];
        $usuario_en_solicitud = consultas_instancia::existe_usuario_solicitud($usuario);
        $usuario_existente = consultas_instancia::get_existe_usuario($usuario);
        $num = 01;
        while($usuario_existente == 1 || $usuario_en_solicitud == 1)
        {
            $usuario = $usuario.$num;
            $usuario_existente = consultas_instancia::get_existe_usuario($usuario);
            $usuario_en_solicitud = consultas_instancia::existe_usuario_solicitud($usuario);
            $num++;
        }
        
        $datos['nombre_usuario'] = $usuario;
        $datos['clave'] = $datos['nombre'].'.'.date('Y');
            
        $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
        $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
        $this->dep('datos')->tabla('solicitud_usuario')->resetear();
           
        toba::notificacion()->agregar('La solicitud de usuario se ha realizado correctamente. En breve recibira un mail para la confirmacion de la solicitud', 'info');
            
        

    }
    
    
//    //-------------------------------------------------------------
//    //-----------------------Cuadro--------------------------------
//    //-------------------------------------------------------------
//    
//    function conf__cuadro_usuario(toba_ei_cuadro $cuadro)
//        {            
//            $datos = $this->dep('datos')->tabla('solicitud_usuario')->get_listado();
//            
//            $cuadro->set_datos($datos);
//        }
    
    
    //-------------------Eventos CI----------------------------
    function evt__volver()
	{
            echo toba_js::abrir();
            echo 'toba.ir_a_operacion("onelogin", "1000292", false) ';
            echo toba_js::cerrar();
                       
	}
    
}

?>