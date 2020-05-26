<?php

class dt_solicitud_usuario extends onelogin_datos_tabla 
{
    function get_datos_usuario($id)
    {
        $sql = "SELECT 
                    s_u.id_solicitud,
                    s_u.nombre_usuario,
                    s_u.id_estado,
                    s_u.id_sistema
                    
                FROM solicitud_usuario as s_u INNER JOIN estado as e ON (s_u.id_estado = e.id_estado)
                    
                
                WHERE s_u.id_estado = $id  ";
        /*
         * LEFT OUTER JOIN (SELECT p_f.proyecto, p_f.usuario_grupo_acc, p_f.nombre FROM dblink('" . $this->dblink_toba_2_7() . "','SELECT proyecto,usuario_grupo_acc,nombre FROM apex_usuario_grupo_acc') as p_f (proyecto CHARACTER VARYING(15), usuario_grupo_acc CHARACTER VARYING(30), nombre CHARACTER VARYING(80))) as p_f ON (s_u.id_perfil_funcional = p_f.usuario_grupo_acc)
                    LEFT OUTER JOIN (SELECT p_d.proyecto, p_d.usuario_perfil_datos, p_d.nombre FROM dblink('" . $this->dblink_toba_2_7() . "','SELECT proyecto,usuario_perfil_datos,nombre FROM apex_usuario_perfil_datos') as p_d (proyecto CHARACTER VARYING(15), usuario_perfil_datos BIGINT, nombre CHARACTER VARYING(80))) as p_d ON (s_u.id_perfil_funcional = p_d.usuario_perfil_datos)
                    LEFT OUTER JOIN (SELECT p.proyecto, p.descripcion FROM dblink('" . $this->dblink_toba_2_7() . "','SELECT proyecto,descripcion FROM apex_proyecto') as p (proyecto CHARACTER VARYING(15), descripcion TEXT)) as p ON (s_u.id_sistema = p.proyecto)
   
         */
        return toba::db('onelogin_solicitud')->consultar($sql);
    }
}


?>
