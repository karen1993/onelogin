<?php
require_once('lib/consultas_instancia.php');

class ci_mostrardatos extends onelogin_ci
{
    
    protected $s__where;
    protected $s__datos_filtro;
    protected $mostrar_solicitud = 0;
    protected $verificar = false;
    protected $crear_usuario = false;
    protected $evento_crear = false;


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
            $email = toba::instancia()->get_info_usuario($usuario)['email'];  
            
            $perfil_funcional_asociado = consultas_instancia::get_lista_grupos_acceso_usuario_proyecto($usuario,$datos['id_sistema']);
            if($perfil_funcional_asociado != null) {
                $this->set_pantalla('pant_edicion');
                throw new toba_error(utf8_decode('El usuario ya tiene asignado un perfil funcional dentro del sistema.'), 'info');
            }
            else {
                $nombre_usuario = toba::usuario()->get_nombre();
                
                $cadena = array(strlen($nombre_usuario));
                $cadena = $nombre_usuario;
                
                $i = 0;
                $nombre = '';
                $apellido = '';
                while($i < strlen($cadena) && $cadena[$i] != ' ') {
                    
                        $nombre = $nombre.$cadena[$i];
                        $i++;
                }
                $i++;
                while($i < strlen($cadena)) {
                    
                        $apellido = $apellido.$cadena[$i];
                        $i++;
                }
                $solicitud_existente = consultas_instancia::existe_solicitud($email,$datos['id_sistema']);
                if($solicitud_existente != null) {
                    $this->set_pantalla('pant_edicion');
                    throw new toba_error(utf8_decode('Usted ya tiene una solicitud pendiente, aguarde a que la misma sea atendida y recibirá un email con la información correspondiente'));
                } else {
                    $datos['nombre_usuario'] = $usuario;          
                    $datos['nombre'] = strtolower($nombre);
                    $datos['apellido'] = strtolower($apellido);
                    $datos['id_estado'] = 'PEND';
                    $datos['correo'] = $email;
                    $fecha = date('d-m-Y H:i:s');
                    $datos['timestamp'] = $fecha;
        
                    $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
                    $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
                    $this->dep('datos')->tabla('solicitud_usuario')->resetear();
                
                    toba::notificacion()->agregar(utf8_decode('La solicitud se ha realizado correctamente.'), 'info');
                }
            }
            
        }
        
        //-------------------------------------------------
        function conf__pant_solicitudes(toba_ei_pantalla $pantalla) {
//            $perfil = toba::manejador_sesiones()->get_perfiles_funcionales()[0];
//            if($perfil == 'gestor') {
//                $this->pantalla()->tab("pant_formulario")->ocultar();
//            }
        }
        
        function conf__pant_edicion(toba_ei_pantalla $pantalla) {
            $pf = toba::manejador_sesiones()->get_perfiles_funcionales();
            if(count($pf)>0){
                $perfil=$pf[0];
            }else{
                $perfil=null;
            }

            if($perfil != null && ($perfil == 'gestor' || $perfil == 'gestor_extension')) {
                $this->pantalla()->tab("pant_solicitudes")->mostrar();
            }
            else {
                $this->pantalla()->tab("pant_solicitudes")->ocultar();
            }
        }
        
        function conf__pant_clave(toba_ei_pantalla $pantalla) {
            $pf = toba::manejador_sesiones()->get_perfiles_funcionales();
            if(count($pf)>0){
                $perfil=$pf[0];
            }else{
                $perfil=null;
            }
            
            if($perfil != null && ($perfil == 'gestor' || $perfil == 'gestor_extension')) {
                $this->pantalla()->tab("pant_solicitudes")->mostrar();
            }
            else {
                $this->pantalla()->tab("pant_solicitudes")->ocultar();
            }
        }
        
        function conf__pant_formulario(toba_ei_pantalla $pantalla) {
            $pf = toba::manejador_sesiones()->get_perfiles_funcionales();
            if(count($pf)>0){
                $perfil=$pf[0];
            }else{
                $perfil=null;
            }
            
            if($perfil != null && ($perfil == 'gestor' || $perfil == 'gestor_extension')) {
                $this->pantalla()->tab("pant_solicitudes")->mostrar();
            }
            else {
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
        
        //--------------------------------------------------------------------------------------------
        //----------------------Métodos Extra----------------------------------------------------------
        
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
        
        //----------------------------------------------------------------------------------
        //---------------------------Formulario Central-------------------------------------
        //----------------------------------------------------------------------------------
        
        function conf__formulario_central(toba_ei_formulario $form) {
            if($this->mostrar_solicitud == 1) {
                $this->dep('formulario_central')->descolapsar();
            } else {
                $this->dep('formulario_central')->colapsar();
            }
            
            if ($this->dep('datos')->tabla('solicitud_usuario')->esta_cargada()) {
                $datos = $this->dep('datos')->tabla('solicitud_usuario')->get();
                if($datos['id_estado'] == 'APRB') {
                    $this->verificar = true;
                    $this->crear_usuario = true;
                } else {
                    if($datos['id_estado'] == 'ATEN' || $datos['id_estado'] == 'RECH') {
                        $this->verificar = true;
                        $this->dep('formulario_central')->evento('modificacion')->ocultar();
                        $form->ef('id_estado')->set_solo_lectura();
                    }
                }
                $form->set_datos($datos);
            }
            
            if(!$this->verificar)
            {
                $this->dep('formulario_central')->evento('modificacion')->ocultar();
                $form->ef('id_estado')->set_solo_lectura();
            }
            else
            {
                $this->dep('formulario_central')->evento('verificar')->ocultar();
            }
            
            if(!$this->crear_usuario) 
            {
                    $this->dep('formulario_central')->evento('crear')->ocultar();
            }
            else {
                $this->dep('formulario_central')->evento('modificacion')->ocultar();
                $form->ef('id_estado')->set_solo_lectura();
                $form->ef('id_sistema')->set_solo_lectura();
                $form->ef('id_perfil_funcional')->set_solo_lectura();
                $form->ef('id_perfil_datos')->set_solo_lectura();
            }
                
            
        }
        
        function evt__formulario_central__verificar($datos)
        {
            $perfil_funcional_asoc = consultas_instancia::get_lista_grupos_acceso_usuario_proyecto($datos['nombre_usuario'],$datos['id_sistema']);
            if($perfil_funcional_asoc == null)
            {
                $this->dep('formulario_central')->evento('modificacion')->mostrar();
                toba::notificacion()->agregar(utf8_decode('El usuario es apto para el sistema, no tiene asignado un perfil funcional dentro del mismo.'), 'info');
//                $this->crear_usuario = true;
            }
            else {
                toba::notificacion()->agregar(utf8_decode('El usuario ya tiene asignado un perfil funcional dentro del sistema.'), 'info');
//                $this->crear_usuario = false;
            }

            $this->set_pantalla('pant_solicitudes');
            $this->dep('datos')->tabla('solicitud_usuario')->cargar($datos);
            
            $this->mostrar_solicitud = 1;
            $this->verificar = true;
        }
        
        function evt__formulario_central__crear($datos) 
        {
            $perfil_funcional_asoc = consultas_instancia::get_lista_grupos_acceso_usuario_proyecto($datos['nombre_usuario'],$datos['id_sistema']);
            $usuarios_existentes = consultas_instancia::get_lista_usuarios();
            $solicitud = $this->dep('datos')->tabla('solicitud_usuario')->get();
            $es_usuario = false;
            $nom_usuario = $datos[nombre_usuario];
            $usuario_creado = consultas_instancia::get_es_usuario($datos['correo']);
            if($solicitud['clave'] != null) {
                $datos['clave'] = $solicitud['clave'];
                $clave = md5($datos[clave]);
            }
            
            foreach ($usuarios_existentes as $usuario) {
                if($usuario['usuario'] == $datos['nombre_usuario']) {
                    $es_usuario = true;
                }
            }
            
            if($datos['id_estado'] == 'APRB' && $perfil_funcional_asoc == null) {
                
                if(!$es_usuario && !$usuario_creado) {
                    
                    $nombre = $datos['nombre'].' '.$datos['apellido'];
                    
                    $sql = "INSERT INTO apex_usuario(
                            usuario, clave, nombre, email, autentificacion, bloqueado, parametro_a,
                            parametro_b, parametro_c, solicitud_registrar, solicitud_obs_tipo_proyecto,
                            solicitud_obs_tipo, solicitud_observacion, usuario_tipodoc, pre,
                            ciu, suf, telefono, vencimiento, dias, hora_entrada, hora_salida,
                            ip_permitida, forzar_cambio_pwd)
                    VALUES ('$nom_usuario','$clave', '$nombre', '$solicitud[correo]', 'md5',0, null,null, null, null, null,null, null, null, null,null, null, null, null, null, null, null,null, 1)";
            
                    toba::db()->consultar($sql);
                
                    $sql2 = "INSERT INTO apex_usuario_proyecto(
                                proyecto, usuario_grupo_acc, usuario, usuario_perfil_datos)
                            VALUES ('$datos[id_sistema]','$datos[id_perfil_funcional]', '$nom_usuario', null)";
            
                    toba::db()->consultar($sql2);
                
                    $sql3 = "INSERT INTO apex_usuario_proyecto_perfil_datos(
                                proyecto, usuario_perfil_datos, usuario)
                            VALUES ('$datos[id_sistema]','$datos[id_perfil_datos]', '$nom_usuario')";
            
                    toba::db()->consultar($sql3);
                
                    toba::notificacion()->agregar(utf8_decode('El usuario se creó correctamente.'), 'info');
                    $link = '<a href= http://mocovi.uncoma.edu.ar/>MOCOVI</a>';
                    $cuerpo_mail = utf8_decode('<p>Se ha generado el usuario solicitado con los siguientes datos <strong>Usuario: '.
                            $nom_usuario.' Contraseña: '.$datos['clave'].'</strong>. Al ingresar al sistema la primera vez, el mismo '
                            . 'forzará un cambio de clave, la misma debe tener al menos 8 caracteres, entre letras mayúsculas, minúsculas, números y símbolos, no pudiendo repetir caracteres adyacentes. '
                            . '<div> Puede ingresar accediendo al siguiente link: '.$link.'</br></br></div>'
                            . ' <div><br><br>Saludos cordiales.</div></p>');
        
                }
                else {
                    
                    $sql = "INSERT INTO apex_usuario_proyecto(
                                proyecto, usuario_grupo_acc, usuario, usuario_perfil_datos)
                            VALUES ('$datos[id_sistema]','$datos[id_perfil_funcional]', '$nom_usuario', null)";
            
                    toba::db()->consultar($sql);
                
                    $sql2 = "INSERT INTO apex_usuario_proyecto_perfil_datos(
                                proyecto, usuario_perfil_datos, usuario)
                            VALUES ('$datos[id_sistema]','$datos[id_perfil_datos]', '$nom_usuario')";
            
                    toba::db()->consultar($sql2);
                
                    toba::notificacion()->agregar(utf8_decode('El usuario se creó correctamente.'), 'info');
                    
                    $cuerpo_mail = utf8_decode('<p>La solicitud de usuario ya ha sido atendida, puede ingresar al modulo correspondiente '
                            . 'con el usuario y contraseña de Mocovi.'
                            . '<br><br>Saludos cordiales. </p>');
                }
                $datos['id_estado'] = 'ATEN';
            }
            
            
            $this->dep('datos')->tabla('solicitud_usuario')->set($datos);
            $this->dep('datos')->tabla('solicitud_usuario')->sincronizar();
            $this->dep('datos')->tabla('solicitud_usuario')->cargar($datos);
            
           
            $info_usuario = toba::instancia()->get_info_usuario($nom_usuario); 
            $mail_usuario = $info_usuario['email'];
            
            $asunto = 'Solicitud de Usuario';
            
            toba::instancia()->get_db()->abrir_transaccion();
                
            try {
                    $mail = new toba_mail($mail_usuario, $asunto, $cuerpo_mail);
                    $mail->set_html(true);
                    $mail->enviar();
                    toba::notificacion()->agregar(utf8_decode('Se ha enviado un mail al usuario.'), 'info');
                    toba::instancia()->get_db()->cerrar_transaccion();
		} catch (toba_error $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			
			throw new toba_error('Se produjo un error en el proceso de respuesta a la solicitud, por favor contactese con un administrador del sistema.');
		}
            
        }
        
        function evt__formulario_central__modificacion($datos)
        {
            $perfil_funcional_asoc = consultas_instancia::get_lista_grupos_acceso_usuario_proyecto($datos['nombre_usuario'],$datos['id_sistema']);
            if($datos['id_estado'] == 'APRB' && $perfil_funcional_asoc == null) {
                $this->crear_usuario = true;
                $this->mostrar_solicitud = 1;
            }
            else {
                $this->mostrar_solicitud = 0;
                $this->crear_usuario = false;
                if($datos['id_estado'] == 'RECH') {
                    $datos['id_estado'] = 'ATEN';
                }
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