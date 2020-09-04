<?php
class dt_personas extends onelogin_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_p.id,
			t_p.apellido,
			t_p.nombre,
			t_p.estado
		FROM
			personas as t_p
		ORDER BY nombre";
		return toba::db('onelogin')->consultar($sql);
	}

}

?>