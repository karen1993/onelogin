<?php

class ci_solicitudes_existentes extends onelogin_ci
{
    
    function conf__cuadro_usuarios(toba_ei_cuadro $cuadro) 
    {
        $solicitudes = $this->dep('datos')->tabla('solicitud_usuario')->get_datos_usuario(1);
        $cuadro->set_datos($solicitudes);
    }
    
}

?>

