<?php

    class form_combo_perfiles extends toba_ei_formulario
    {
        function extender_objeto_js()
	{
//		$id_js = toba::escaper()->escapeJs($this->objeto_js);
//                echo "
//		
//		{$id_js}.evt__id_sistema__procesar = function(es_inicial) 
//                    {
//                    
//			if(this.ef('id_sistema').get_estado() == 'designa')
//                        { 
//                        }
//                        else
//                        {
//                            if(this.ef('id_sistema').get_estado() == 'extension')
//                            { 
//                            }
//                        }
//                    }
//                
//                
//                ";
//                $sql = "SELECT 	proyecto,
//						usuario_perfil_datos,
//						nombre,
//						descripcion						
//				FROM 	apex_usuario_perfil_datos 
//				WHERE	proyecto = $proyecto";
//                return toba::db()->consultar($sql);
        }
    }

?>