<?php

class dt_estado extends onelogin_datos_tabla 
{
    function get_estado()
    {
        $sql = "SELECT id_estado, estado FROM estado
                WHERE id_estado <> 'ATEN'
                ORDER BY estado";
        return toba::db('onelogin_solicitud')->consultar($sql);
    }
}


?>
