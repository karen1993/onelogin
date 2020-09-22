<?php

class dt_solicitud_usuario extends onelogin_datos_tabla 
{
   
    
    function get_listado($solicitud = null)
    {
        $sql = "SELECT 
                    s.id_solicitud,
                    s.nombre,
                    s.apellido,
                    s.nombre_usuario,
                    s.id_estado,
                    s.id_perfil_datos,
                    s.id_perfil_funcional,
                    s.id_sistema,
                    s.correo
                    
                FROM solicitud_usuario  as s INNER JOIN estado as e ON (s.id_estado = e.id_estado)
                WHERE s.id_solicitud = $solicitud ";
        
        return toba::db('onelogin_solicitud')->consultar($sql);
    }
    
    function get_solicitudes($where = null) 
    {
        $pf = toba::manejador_sesiones()->get_perfiles_funcionales();
        if(count($pf)>0){
            $perfil=$pf[0];
        }else{
            $perfil=null;
        }
        
        if (!is_null($where)) {
            if(strpos($where, '(') || strpos($where, 'id_sistema')) {
                if(strpos($where, '(')) {
                    $where = str_replace('id_estado', 's.id_estado', $where);
                }
                $where = "WHERE " . $where;
            } else {
                
                $where = "WHERE s." . $where;
            }
            
        }
        if($perfil != null && $perfil == 'gestor_extension') {
            $sql = "SELECT  s.id_solicitud, s.nombre_usuario, s.id_sistema, est.estado, timestamp 
                    FROM solicitud_usuario as s INNER JOIN estado as est ON (s.id_estado = est.id_estado)
                    WHERE s.id_sistema = 'extension' AND s.id_estado <> 'ATEN'
                    " . $where;
        } else {
            $sql = "SELECT  s.id_solicitud, s.nombre_usuario, s.id_sistema, est.estado, timestamp 
                    FROM solicitud_usuario as s INNER JOIN estado as est ON (s.id_estado = est.id_estado)
                    s.id_estado <> 'ATEN'
                    " . $where;
        }
        return toba::db('onelogin_solicitud')->consultar($sql);
    }
}


?>
