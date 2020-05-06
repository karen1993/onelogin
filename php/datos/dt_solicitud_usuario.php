<?php

class dt_solicitud_usuario extends onelogin_datos_tabla 
{
    function get_datos_usuario()
    {
        $sql = "SELECT 
                    s_u.id_solicitud,
                    s_u.nombre,
                    s_u.apellido,
                    s_u.email,
                    s_u.id_perfil_datos,
                    s_u.id_perfil_funcional,
                    s_u.nombre_usuario,
                    s_u.id_estado,
                    s_u.id_sistema,
                    s_u.timestamp
                    
                FROM solicitud_usuario as s_u INNER JOIN estado as e ON (s_u.id_estado == e.id_estado)
               ";
        return toba::db('onelogin_solicitud')->consultar($sql);
    }
}


?>
