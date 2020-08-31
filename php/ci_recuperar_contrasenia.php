<?php

class ci_recuperar_contrasenia extends toba_ci
{
	protected $s__usuario;
	protected $randr;
	protected $s__email;
	private $pregunta;
        private $contador = 0;
	
	function ini()
	{
		//Preguntar en toba::memoria si vienen los parametros
		if (! isset($this->s__usuario)) {
			$this->s__usuario = toba::memoria()->get_parametro('usuario');
			$this->randr = toba::memoria()->get_parametro('randr');        //Esto hara las veces de unique para la renovacion
		}

		//Esto es por si se trata de entrar al item directamente
		$item = toba::memoria()->get_item_solicitado();
		$tms = toba_manejador_sesiones::instancia();
		if ($item[0] == 'toba_editor' && !$tms->existe_usuario_activo()) {
			throw new toba_error_ini_sesion('No se puede correr este item fuera del editor');
		}
                
	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_usuario(toba_ei_formulario $form)
	{
		//Probablemente esto vaya vacio a excepcion del usuario si es que se pasa
		if (isset($this->s__usuario) && (!is_null($this->s__usuario))) {
			$form->set_datos_defecto(array('usuario' => $this->s__usuario));
			$form->set_solo_lectura(array('usuario'));
		}
	}

	function evt__form_usuario__enviar($datos)
	{
		//Miro que vengan los datos que necesito
		if (! isset($datos['email'])) {
			throw new toba_error_autenticacion(utf8_decode('No se suministro un mail válido'));
		}
                $this->s__usuario = $this->recuperar_usuario_con_email($datos['email']);
		//Si el usuario existe, entonces disparo el envio de mail 
		if (! $this->verificar_usuario_activo($this->s__usuario)) {
			throw new toba_error_autenticacion(utf8_decode('No se suministro un usuario válido'));
		} 
                
		$this->set_pantalla('pant_pregunta');
		$this->s__email = $datos['email'];
	}

        //-----------------------------------------------------------------------------------
	//---- form_token ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_token(toba_ei_formulario $form)
	{
		if($this->contador != 0) {
                    $form->ef('token')->set_estado('');
                }
	}

	function evt__form_token__confirmar($datos)
	{
            $datos_usu = $this->recuperar_datos_solicitud($this->s__usuario);
            if($datos_usu[0]['random'] == $datos['token']) {
                $this->contador = 0;
                $this->randr = $datos_usu[0]['random'];
                $this->set_pantalla('pant_inicial');
            }
            else {
                
                $this->contador=1;
                print_r($this->contador);
                throw new toba_error_autenticacion(utf8_decode('No se suministro un token válido, vuelva a ingresar o presione el botón REINICIAR CAMBIO '
                        . 'para volver a solicitar la nueva contraseña'));
            }
            
            
	}
        
	//-----------------------------------------------------------------------------------
	//---- form_pregunta ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_pregunta(toba_ei_formulario $form)
	{
		//$datos = $this->recuperar_pregunta_secreta($this->s__usuario);
		if (! is_null($this->pregunta)) {
			unset($this->pregunta['respuesta']);
		}
		$form->set_datos($this->pregunta);
	}

	function evt__form_pregunta__modificacion($datos)
	{
		$this->verificar_desafio_secreto($datos);    
	}
        
        //-----------------------------------------------------------------------------------
	//---- form_cambio_clave ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__form_cambio_clave__aceptar($datos)
        {
//            ini_set('error_reporting', E_ALL);      //Esto para que en el Server, como esta en produccion, largue errores que esten pasando..
            
            if($datos['clave_nueva']==$datos['repite_clave'])
            {
                $datos_rs = $this->recuperar_datos_solicitud_usuario($this->s__usuario);
		if (empty($datos_rs)) {
			toba::logger()->debug(utf8_decode('Proceso de cambio de contraseña en base: El usuario o el token no coinciden' ));
			toba::logger()->var_dump(array('rnd' => $this->randr));
			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');            
		} else {
                        if(count($datos_rs) == 1) {
                            $datos_orig = current($datos_rs);
                        }
                        else {
                            $datos_orig = end( $datos_rs );
                        }
			
		}
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
                  toba_usuario::verificar_composicion_clave($datos['clave_nueva'], $largo_clave);
              
                  //Obtengo los dias de validez de la nueva clave
                  $dias = toba_parametros::get_clave_validez_maxima($proyecto);
                  $ultimas_claves = toba_parametros::get_nro_claves_no_repetidas($proyecto);
                  toba_usuario::verificar_clave_no_utilizada($datos['clave_nueva'], $datos_orig['usuario'], $ultimas_claves);
                  toba_usuario::reemplazar_clave_vencida($datos['clave_nueva'], $datos_orig['usuario'], $dias);
                  $this->es_cambio_contrasenia = true;                //Bandera para el post_eventos
                  toba::notificacion()->agregar('Modificacion realizada con Exito!', 'info');
                  echo toba_js::abrir();
                  echo 'toba.ir_a_operacion("onelogin", "1000292", false) ';
                  echo toba_js::cerrar();
              } catch(toba_error_pwd_conformacion_invalida $e) {
                  toba::logger()->info($e->getMessage());
                  toba::notificacion()->agregar($e->getMessage(), 'error');
                  return;
                  
              }

            }
            else
            {
		$this->set_pantalla('cambio_clave');
                toba::notificacion()->agregar('Las claves nuevas no Coinciden', 'error');
            }
        }
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__recordame()
	{
		//Primero verifico que se haya cumplido con el periodo minimo de vida de la contraseña
		$dias = toba_parametros::get_clave_validez_minima(toba::proyecto()->get_id());
		if (! is_null($dias)) {
			if (! toba_usuario::verificar_periodo_minimo_cambio($this->s__usuario, $dias)) {
				toba::notificacion()->agregar(utf8_decode('No transcurrio el período minimo para poder volver a cambiar su contraseña. Intentelo en otra ocasión'));
				return;
			}
		}
		
		//Si llego hasta aca es porque la respuesta es correcta, sino explota en la modificacion del form        
		$this->enviar_mail_aviso_cambio();
		toba::notificacion()->agregar('Se ha enviado un mail a la cuenta especificada, por favor verifiquela', 'info');
		$this->set_pantalla('pant_token');
	}
        
        function evt__nueva_clave()
        {
            $this->set_pantalla('cambio_clave');
        }
       
	
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
		//Si viene con el random seteado es que esta confirmando el cambio de contraseña
		if (isset($this->randr) && ! is_null($this->randr)) {
			$pantalla->eliminar_dep('form_usuario');
			$this->disparar_confirmacion_cambio();
                        $this->evento('nueva_clave')->mostrar();
                        $pantalla->set_descripcion(utf8_decode('Presione el botón para ingresar la nueva contraseña'));
		}
                else {
                    $this->evento('nueva_clave')->ocultar();
                }
	}
	
	function conf__pant_pregunta(toba_ei_pantalla $pantalla)
	{

            $pantalla->eliminar_dep('form_pregunta');
            $pantalla->set_descripcion(utf8_decode('Presione el botón para continuar con el proceso'));

	}
        
        function conf__pant_token(toba_ei_pantalla $pantalla)
	{
            $pantalla->set_descripcion(utf8_decode('Ingrese el token enviado a su correo electrónico'));
//            if($this->contador == 0){
//                $this->evento('reiniciar')->ocultar();
//            }
//            else {
//                $this->evento('reiniciar')->mostrar();
//            }
	}
        
        function conf__cambio_clave(toba_ei_pantalla $pantalla)
	{
            $pantalla->set_descripcion(utf8_decode('Cambio de contraseña'));
	}
	
	//----------------------------------------------------------------------------------------
	//-------- Procesamiento del pedido ------------------------------------------
	//----------------------------------------------------------------------------------------
	/*
		* Verifico que el usuario existe a traves de la API de toba_usuario
		*/
	function verificar_usuario_activo($usuario)
	{
		try {
			toba::instancia()->get_info_usuario($usuario);        //Tengo que verificar que el negro existe
		} catch (toba_error_db $e) {                        //Ni true ni false... revienta... el mono no existe
			toba::logger()->error(utf8_decode('Se intento modificar la clave del usuario:' . $usuario));
			return false;
		}
		return true;
	}

        /**
		* Recupera el usuario a partir el email
		* @param string $email
		* @return string 
		*/
        function recuperar_usuario_con_email($email)
	{
		try {
                        $email = quote($email);
                        $sql = "SELECT usuario, email
                                FROM apex_usuario
                                WHERE email = $email ";
                        $datos = toba::db()->consultar($sql);
			return $datos[0]['usuario'];
		} catch (toba_error $e) {                        
			toba::logger()->error('Se intento modificar la clave del usuario:' . $usuario);
			return null;
		}
	}
	

	/*
		* Aca envio un primer mail con un link para confirmar el cambio
		*/
	function enviar_mail_aviso_cambio()
	{
		//Genero un pseudorandom unico... 
		$tmp_rand = $this->get_random_temporal();
                
		//Se envia el mail a la direccion especificada por el usuario.
		$asunto =utf8_decode('Solicitud de cambio de contraseña');
		$cuerpo_mail = utf8_decode('<p>Este mail fue enviado a esta cuenta porque se <strong>solicitó un cambio de contraseña</strong> para el usuario: <strong>'.$this->s__usuario. '.</strong>'
                        . ' Si usted solicitó dicho cambio copie el siguiente token: <br><br>'.$tmp_rand.' <br><br>en el campo actual.</p>');

		//Guardo el random asociado al usuario y envio el mail
		toba::instancia()->get_db()->abrir_transaccion();
                
		try {
			$this->guardar_datos_solicitud_cambio($tmp_rand, $this->s__email);
			$mail = new toba_mail($this->s__email, $asunto, $cuerpo_mail);
			$mail->set_html(true);
			$mail->enviar();
			toba::instancia()->get_db()->cerrar_transaccion();
		} catch (toba_error $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			toba::logger()->debug('Proceso de envio de random a cuenta: '. $e->getMessage());
			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');
		}
	}

	
	function get_random_temporal()
	{
		$uuid = uniqid(rand(), true);
		$rnd = sha1(microtime() . $uuid . rand());
		return $rnd;
	}

	
	/*
	* Impacta en la base para cambiar la contrase�a del usuario
	*/
	function disparar_confirmacion_cambio()
	{
		//Recupero mail del usuario junto con el hash de confirmacion
//		$datos_rs = $this->recuperar_datos_solicitud_cambio($this->s__usuario, $this->randr);
//		if (empty($datos_rs)) {
//			toba::logger()->debug(utf8_decode('Proceso de cambio de contraseña en base: El usuario o el token no coinciden' ));
//			toba::logger()->var_dump(array('rnd' => $this->randr));
//			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');            
//		} else {
//			$datos_orig = current($datos_rs);
//		}
				
		//bloqueo el random
		toba::instancia()->get_db()->abrir_transaccion();
		try {
			$this->bloquear_random_utilizado($this->s__usuario, $this->randr);
                        
			toba::instancia()->get_db()->cerrar_transaccion();
		} catch (toba_error $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			toba::logger()->debug('Proceso de cambio de contrase�a en base: ' . $e->getMessage());
			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');
		}
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//                                        METODOS PARA SQLs
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function guardar_datos_solicitud_cambio($random, $mail)
	{
		$sql = 'UPDATE apex_usuario_pwd_reset SET bloqueado = 1 WHERE usuario = :usuario;';
		//toba::instancia()->get_db()->set_modo_debug(true, true);
		$up_sql = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_ejecutar($up_sql, array('usuario'=>$this->s__usuario));

		$sql = 'INSERT INTO apex_usuario_pwd_reset (usuario, random, email) VALUES (:usuario, :random, :mail);';
		//toba::logger()->debug(array('usuario'=>$this->usuario, 'random' => $random, 'mail' => $mail));
		$in_sql = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_ejecutar($in_sql, array('usuario'=>$this->s__usuario, 'random' => $random, 'mail' => $mail));
	}
	
	function recuperar_datos_solicitud_cambio($usuario, $random)
	{
		$sql = "SELECT  usuario as id_usuario,
										email
						FROM apex_usuario_pwd_reset
						WHERE    usuario = :usuario
						AND random = :random
						AND age(now() , validez)  < interval '1 day'
						AND bloqueado = 0;";

		//toba::instancia()->get_db()->set_modo_debug(true, true);
		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_consultar($id, array('usuario'=>$usuario, 'random' => $random));
		return $rs;
	}
        
        function recuperar_datos_solicitud($usuario)
	{
                $usuario = quote($usuario);
		$sql = "SELECT  usuario,
                                random,
                                email
				FROM apex_usuario_pwd_reset
				WHERE    usuario = $usuario
				AND age(now() , validez)  < interval '1 day'
				AND bloqueado = 0";

		//toba::instancia()->get_db()->set_modo_debug(true, true);
//		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
//		$rs = toba::instancia()->get_db()->sentencia_consultar($id, array('usuario'=>$usuario, 'random' => $random));
		return toba::db()->consultar($sql);
	}
        
        function recuperar_datos_solicitud_usuario($usuario)
	{
                $usuario = quote($usuario);
		$sql = "SELECT  usuario,
                                random,
                                email
				FROM apex_usuario_pwd_reset
				WHERE    usuario = $usuario
				AND age(now() , validez)  < interval '1 day'
				AND bloqueado = 1";

		//toba::instancia()->get_db()->set_modo_debug(true, true);
//		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
//		$rs = toba::instancia()->get_db()->sentencia_consultar($id, array('usuario'=>$usuario, 'random' => $random));
		return toba::db()->consultar($sql);
	}

	function bloquear_random_utilizado($usuario, $random)
	{
		$sql = 'UPDATE apex_usuario_pwd_reset  SET bloqueado = 1
						WHERE     usuario = :usuario
						AND random = :random';
		//toba::instancia()->get_db()->set_modo_debug(true, true);
		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_ejecutar($id, array('usuario'=>$usuario, 'random' => $random));
	}
        
        
}
?>

