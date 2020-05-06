<?php

class ci_solicitud_usuarios extends onelogin_ci
{
    protected $modificar = false;
    protected $datos_usuario;
    
    
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
        if(!$this->modificar)
        {
            $form->ef('nombre_usuario')->set_solo_lectura();
        }
                
        
    }
    
    function evt__form_solicitud__alta($datos)
    {
        $fecha = date('d-m-y');
        $datos['timestamp'] = $fecha;        
        $datos['id_estado'] = 1;
        $datos['nombre'] = strtolower($datos['nombre']);
        $datos['apellido'] = strtolower($datos['apellido']);
        $usuario = $datos['nombre'][0].$datos['apellido'];
        $usuario_existente = consultas_instancia::get_existe_usuario($usuario);
        if($usuario_existente == 1 && $datos['nombre_usuario'] == null)
        {
            toba::notificacion()->agregar("Ingrese el nombre de usuario");
            $this->datos_usuario = $datos;
            $this->modificar = true;
            
        }
        
        else
        {
            $this->modificar = false;
            if($datos['nombre_usuario'] == null)
            {
                $datos['nombre_usuario'] = $usuario;
            }
            
            $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
            $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
            $this->dep('datos')->tabla('solicitud_usuario')->resetear();
            
            toba::notificacion()->agregar('La solicitud de usuario se ha realizado correctamente', 'info');
            
        }

    }
    
    
    //-------------------Eventos CI----------------------------
    function evt__volver()
	{
            echo toba_js::abrir();
            echo 'toba.ir_a_operacion("onelogin", "1000292", false) ';
            echo toba_js::cerrar();
                       
	}
    
}

?>