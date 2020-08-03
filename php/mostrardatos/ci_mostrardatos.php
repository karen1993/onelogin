<?php
require_once('lib/consultas_instancia.php');

class ci_mostrardatos extends onelogin_ci
{
    
    protected $s__where;
    protected $s__datos_filtro;
    protected $mostrar_solicitud = 0;
    protected $verificar = false;


    //---- Cuadro -----------------------------------------------------------------------
            
	

	

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
            //ini_set('error_reporting', E_ALL);      //Esto para que en el Server, como esta en produccion, largue errores que esten pasando..
            
            $bdtoba=toba::instancia()->get_db();
            //$sql1 = "SELECT nombre FROM desarrollo.apex_usuario WHERE usuario='".toba::usuario()->get_id()."'";
                //$nomyapp=$bdtoba->consultar($sql1)[0];
                $nomyapp=toba::usuario()->get_nombre();
                
                $separados=explode(' ',$nomyapp);
                //ei_arbol($separados);
                $arr['nombre']=$separados[0];
                $arr['apellido']=$separados[1];
                $arr['usuario']=toba::usuario()->get_id();
                
                $sql = "SELECT email FROM apex_usuario WHERE usuario='".toba::usuario()->get_id()."'";
                $mail=$bdtoba->consultar($sql);
                $mail=$mail[0];
//                echo gettype($mail);
//                echo sizeof($mail, $mode);
                if(isset($mail)){
                    $arr['email']=  implode($mail);
                }
                
                return $arr;
	}
        function evt__formulario__modificacion($datos)
	{
            
            $nomApp=$datos['nombre'].' '.$datos['apellido'];
            //          
            $bdtoba=toba::instancia()->get_db();
            $sql ='UPDATE desarrollo.apex_usuario SET nombre =\''.$nomApp.'\', email=\''.$datos['email'].'\' WHERE usuario=\''.toba::usuario()->get_id().'\'';
            $bdtoba->consultar($sql);
            toba::notificacion()->agregar('Modificacion realizada con Exito!', 'info');
                    
        }

	function evt__cambioClave__bot_aceptar($datos)
	{
            ini_set('error_reporting', E_ALL);      //Esto para que en el Server, como esta en produccion, largue errores que esten pasando..
            
            if($datos['claveNueva']==$datos['repiteClaveNueva'])
            {
            $usuario=toba::usuario()->get_id();;
		if (toba::manejador_sesiones()->invocar_autenticar($usuario, $datos['claveAnterior'], null)) {        //Si la clave anterior coincide    
               $proyecto = toba::proyecto()->get_id();
              //Verifico que no intenta volver a cambiarla antes del periodo permitido
              $dias_minimos = toba_parametros::get_clave_validez_minima($proyecto);
              if (! is_null($dias_minimos)) {
                  if (! toba_usuario::verificar_periodo_minimo_cambio($usuario, $dias_minimos)) {
                      toba::notificacion()->agregar();
                      return;
                  }
              }        
              //Obtengo el largo minimo de la clave            
              $largo_clave = toba_parametros::get_largo_pwd($proyecto);
              try {
                  toba_usuario::verificar_composicion_clave($datos['claveNueva'], $largo_clave);
              
                  //Obtengo los dias de validez de la nueva clave
                  $dias = toba_parametros::get_clave_validez_maxima($proyecto);
                  $ultimas_claves = toba_parametros::get_nro_claves_no_repetidas($proyecto);
                  toba_usuario::verificar_clave_no_utilizada($datos['claveNueva'], $usuario, $ultimas_claves);
                  toba_usuario::reemplazar_clave_vencida($datos['claveNueva'], $usuario, $dias);
                  $this->es_cambio_contrasenia = true;                //Bandera para el post_eventos
                  toba::notificacion()->agregar('Modificacion realizada con Exito!', 'info');
              } catch(toba_error_pwd_conformacion_invalida $e) {
                  toba::logger()->info($e->getMessage());
                  toba::notificacion()->agregar($e->getMessage(), 'error');
                  return;
              }
              } else {
                  throw new toba_error_usuario('La clave ingresada no es correcta');
              }
              $this->set_pantalla('pant_clave');
            }
            else
            {
		$this->resetear();
                toba::notificacion()->agregar('Las claves nuevas no Coinciden', 'error');
            }
	}

	function evt__cambioClave__bot_cancelar()
	{
            ini_set('error_reporting', E_ALL);      //Esto para que en el Server, como esta en produccion, largue errores que esten pasando..
            
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
	}
        
        
        //-------------------Formulario de Solicitud-----------------------------------------
        //-----------------------------------------------------------------------------------
        function conf__form_solicitud(toba_ei_formulario $form)
        {
            
        }
        
        function evt__form_solicitud__alta($datos)
        {
            $usuario = toba::usuario()->get_id();
                
            $perfil_funcional_asociado = consultas_instancia::get_lista_grupos_acceso_usuario_proyecto($usuario,$datos['id_sistema']);
            if($perfil_funcional_asociado != null) {
                toba::notificacion()->agregar(utf8_decode('El usuario ya tiene asignado un perfil funcional dentro del sistema.'), 'info');
            }
            else {
                
//                $nombre_usuario = toba::usuario()->get_nombre();
//                
//                $cadena = array(strlen($nombre_usuario));
//                $cadena = $nombre_usuario;
//                
//                $i = 0;
//                $nombre = '';
//                $apellido = '';
//                while($i < strlen($cadena) && $cadena[$i] != ' ') {
//                    
//                        $nombre = $nombre.$cadena[$i];
//                        $i++;
//                }
//                $i++;
//                while($i < strlen($cadena)) {
//                    
//                        $apellido = $apellido.$cadena[$i];
//                        $i++;
//                }
                
                $datos['nombre_usuario'] = $usuario;          
//                $datos['nombre'] = strtolower($nombre);
//                $datos['apellido'] = strtolower($apellido);

                $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
                $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
                $this->dep('datos')->tabla('solicitud_usuario')->resetear();
                
                toba::notificacion()->agregar(utf8_decode('La solicitud se ha realizado correctamente.'), 'info');
                
            }
            
        }
        
        //-------------------------------------------------
        function conf__pant_solicitudes(toba_ei_pantalla $pantalla) {
            
        }
        
        function conf__pant_edicion(toba_ei_pantalla $pantalla) {
            $perfil = toba::manejador_sesiones()->get_perfiles_funcionales()[0];
//            print_r(toba::manejador_sesiones()->get_perfiles_funcionales());            exit();
            if($perfil != 'sec_ext_central' && $perfil != 'admin') {
                $this->pantalla()->tab("pant_solicitudes")->ocultar();
            }
        }
        
        function conf__pant_clave(toba_ei_pantalla $pantalla) {
            $perfil = toba::manejador_sesiones()->get_perfiles_funcionales()[0];
            if($perfil != 'sec_ext_central' && $perfil != 'admin') {
                $this->pantalla()->tab("pant_solicitudes")->ocultar();
            }
        }
        
        function conf__pant_formulario(toba_ei_pantalla $pantalla) {
            $perfil = toba::manejador_sesiones()->get_perfiles_funcionales()[0];
            if($perfil != 'sec_ext_central' && $perfil != 'admin') {
                $this->pantalla()->tab("pant_solicitudes")->ocultar();
            }
        }
        
        //-------------------------------------------------------------------------------
        //-------------------------- FILTRO ---------------------------------------------

        function conf__filtro_solicitud(toba_ei_filtro $filtro) {
            if (isset($this->s__datos_filtro)) {
                $filtro->set_datos($this->s__datos_filtro);
            }
        }

        function evt__filtro_solicitud__filtrar($datos) {
            $this->s__datos_filtro = $datos;
            $this->s__where = $this->dep('filtro_solicitud')->get_sql_where();
        }

        function evt__filtro_solicitud__cancelar() {
            unset($this->s__datos_filtro);
            unset($this->s__where);
        }
        
        //----------------------------------------------------------------------------------
        //-------------------------Cuadro Solicitudes-Central---------------------------------------
        
        function conf__cuadro_solicitud(toba_ei_cuadro $cuadro)
        {            
            if (isset($this->s__where)) {
                $datos = $this->dep('datos')->tabla('solicitud_usuario')->get_solicitudes($this->s__where);
            }
            else {
                $datos = $this->dep('datos')->tabla('solicitud_usuario')->get_solicitudes();
            }

            $cuadro->set_datos($datos);
        }
        
        function evt__cuadro_solicitud__seleccion($datos)
        {
            $this->mostrar_solicitud = 1;
            $solicitud = $this->dep('datos')->tabla('solicitud_usuario')->get_listado($datos['id_solicitud']);
            $this->set_pantalla('pant_solicitudes');
            $this->dep('datos')->tabla('solicitud_usuario')->cargar($solicitud[0]);
            
        }
        
        //----------------------------------------------------------------------------------
        //---------------------------Formulario Central-------------------------------------
        //----------------------------------------------------------------------------------
        
        function conf__formulario_central(toba_ei_formulario $form) {
            if($this->mostrar_solicitud == 1) {
                $this->dep('formulario_central')->descolapsar();
            } else {
                $this->dep('formulario_central')->colapsar();
            }
            
            if(!$this->verificar)
            {
                $this->dep('formulario_central')->evento('modificacion')->ocultar();
                $form->ef('id_estado')->set_solo_lectura();
            }
            
            if ($this->dep('datos')->tabla('solicitud_usuario')->esta_cargada()) {
                $datos = $this->dep('datos')->tabla('solicitud_usuario')->get();
                $datos2 = $datos;
                $datos2['perfil_datos'] = consultas_instancia::get_perfil_datos($datos['id_sistema'],$datos['id_perfil_datos']);
                $datos2['perfil_funcional'] = consultas_instancia::get_grupo_acceso($datos['id_sistema'],$datos['id_perfil_funcional']);
               
                $form->set_datos($datos2);
            }
        }
        
        function evt__formulario_central__verificar($datos)
        {
            //*** Sólo se puede tener un perfil funcionl dentro de los sistemas?? O en algunos se puede tener más de 1??
            $perfil_funcional_asoc = consultas_instancia::get_lista_grupos_acceso_usuario_proyecto($datos['nombre_usuario'],$datos['id_sistema']);
            if($perfil_funcional_asoc == null)
            {
                $this->dep('formulario_central')->evento('modificacion')->mostrar();
                toba::notificacion()->agregar(utf8_decode('El usuario es apto para el sistema, no tiene asignado un perfil funcional dentro del mismo.'), 'info');
            }
            else {
                toba::notificacion()->agregar(utf8_decode('El usuario ya tiene asignado un perfil funcional dentro del sistema.'), 'info');
                
            }
            
            $solicitud = $this->dep('datos')->tabla('solicitud_usuario')->get();
            $datos2['nombre'] = $datos['nombre'];
            $datos2['apellido'] = $datos['apellido'];
            $datos2['nombre_usuario'] = $datos['nombre_usuario'];
            $datos2['id_sistema'] = $datos['id_sistema'];
            $datos2['id_estado'] = $datos['id_estado'];
            $datos2['id_perfil_datos'] = $solicitud['id_perfil_datos'];
            $datos2['id_perfil_funcional'] = $solicitud['id_perfil_funcional'];
            
            $this->set_pantalla('pant_solicitudes');
            $this->dep('datos')->tabla('solicitud_usuario')->cargar($datos2);
            
            $this->mostrar_solicitud = 1;
            $this->verificar = true;
        }
        
        function evt__formulario_central__modificacion($datos)
        {
            $usuarios_existentes = consultas_instancia::get_lista_usuarios();
            $solicitud = $this->dep('datos')->tabla('solicitud_usuario')->get();
            $datos['id_perfil_datos'] = $solicitud['id_perfil_datos'];
            $datos['id_perfil_funcional'] = $solicitud['id_perfil_funcional'];
            $es_usuario = false;
            
            foreach ($usuarios_existentes as $usuario) {
                if($usuario['usuario'] == $datos['nombre_usuario']) {
                    $es_usuario = true;
                }
            }
            
            if(!$es_usuario && $datos['id_estado'] == 'APRB') {
                $nom_usuario = $datos[nombre_usuario];
                $clave = md5($datos[clave]);
                $sql = "INSERT INTO desarrollo.apex_usuario(
                            usuario, clave, nombre, email, autentificacion, bloqueado, parametro_a,
                            parametro_b, parametro_c, solicitud_registrar, solicitud_obs_tipo_proyecto,
                            solicitud_obs_tipo, solicitud_observacion, usuario_tipodoc, pre,
                            ciu, suf, telefono, vencimiento, dias, hora_entrada, hora_salida,
                            ip_permitida, forzar_cambio_pwd)
                VALUES ('$nom_usuario','$clave', '$nom_usuario', null, 'md5',0, null,null, null, null, null,null, null, null, null,null, null, null, null, null, null, null,null, 0)";
            
                toba::db()->consultar($sql);
                
                $sql2 = "INSERT INTO desarrollo.apex_usuario_proyecto(
                            proyecto, usuario_grupo_acc, usuario, usuario_perfil_datos)
                VALUES ('$datos[id_sistema]','$datos[id_perfil_funcional]', '$nom_usuario', 'null')";
            
                toba::db()->consultar($sql2);
                
                $sql3 = "INSERT INTO desarrollo.apex_usuario_proyecto_perfil_datos(
                            proyecto, usuario_perfil_datos, usuario)
                VALUES ('$datos[id_sistema]','$datos[id_perfil_datos]', '$nom_usuario')";
            
                toba::db()->consultar($sql3);
                
                toba::notificacion()->agregar(utf8_decode('El usuario se creó correctamente.'), 'info');
            }
            
            $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
            $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
            
            $this->verificar = true;
        }
        
        function evt__formulario_central__cancelar()
        {
            $this->dep('datos')->tabla('solicitud_usuario')->resetear();
            $this->set_pantalla('pant_solicitudes');
            $this->mostrar_solicitud = 0;
            $this->verificar = false;
        }

        //-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__volver()
	{
            echo toba_js::abrir();
            echo 'toba.ir_a_operacion("onelogin", "2", false) ';
            echo toba_js::cerrar();
                       
	}

}
?>