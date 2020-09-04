<?php

class formulario_ocultar_mostrar extends toba_ei_formulario
    {
        function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
                
                echo "
		
		{$id_js}.evt__id_perfil_funcional__procesar = function(es_inicial) 
                    {
                        if(this.ef('id_sistema').get_estado()=='extension') {
                           switch(this.ef('id_perfil_funcional').get_estado()) 
                           {
                                case 'sec_ext_central':
                                    this.ef('id_perfil_datos').set_obligatorio(false);
                                    break;
                                
                                case 'admin':
                                    this.ef('id_perfil_datos').set_obligatorio(false);
                                    break;
                                    
                                default:
                                    this.ef('id_perfil_datos').set_obligatorio(true);
                                    this.ef('id_perfil_datos').mostrar();
                                    break;
                            }
                        }    			
                    } 
                   " ;
        }
    }
?>