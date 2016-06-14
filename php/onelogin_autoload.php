<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class onelogin_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'onelogin_ci' => 'extension_toba/componentes/onelogin_ci.php',
		'onelogin_cn' => 'extension_toba/componentes/onelogin_cn.php',
		'onelogin_datos_relacion' => 'extension_toba/componentes/onelogin_datos_relacion.php',
		'onelogin_datos_tabla' => 'extension_toba/componentes/onelogin_datos_tabla.php',
		'onelogin_ei_arbol' => 'extension_toba/componentes/onelogin_ei_arbol.php',
		'onelogin_ei_archivos' => 'extension_toba/componentes/onelogin_ei_archivos.php',
		'onelogin_ei_calendario' => 'extension_toba/componentes/onelogin_ei_calendario.php',
		'onelogin_ei_codigo' => 'extension_toba/componentes/onelogin_ei_codigo.php',
		'onelogin_ei_cuadro' => 'extension_toba/componentes/onelogin_ei_cuadro.php',
		'onelogin_ei_esquema' => 'extension_toba/componentes/onelogin_ei_esquema.php',
		'onelogin_ei_filtro' => 'extension_toba/componentes/onelogin_ei_filtro.php',
		'onelogin_ei_firma' => 'extension_toba/componentes/onelogin_ei_firma.php',
		'onelogin_ei_formulario' => 'extension_toba/componentes/onelogin_ei_formulario.php',
		'onelogin_ei_formulario_ml' => 'extension_toba/componentes/onelogin_ei_formulario_ml.php',
		'onelogin_ei_grafico' => 'extension_toba/componentes/onelogin_ei_grafico.php',
		'onelogin_ei_mapa' => 'extension_toba/componentes/onelogin_ei_mapa.php',
		'onelogin_servicio_web' => 'extension_toba/componentes/onelogin_servicio_web.php',
		'onelogin_comando' => 'extension_toba/onelogin_comando.php',
		'onelogin_modelo' => 'extension_toba/onelogin_modelo.php',
	);
}
?>