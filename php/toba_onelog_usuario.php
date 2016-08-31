<?php
/**
 * Tipo de p�gina pensado para pantallas de login, presenta un logo y un pie de p�gina b�sico
 * 
 * @package SalidaGrafica
 */
class toba_onelog_usuario extends toba_tp_basico
{
	function barra_superior()
	{
		echo "
			<style type='text/css'>
				.cuerpo {
					
				}
                                #js_ci_1000722_cont
                                {
                                    margin-top: 5%;
                                }
                                .en-pagina
                                {
                                    color: white;
                                }
                            .div-editor
                            
			</style>
		";
                echo "<div id='titulo'align=center>";
                $im=toba_recurso::imagen_proyecto('oneloginTop5.png',1200,400);
                echo $im;
                
                echo "ME CARGO SOLO EL TIPO PAGINA?";
                echo "</div>";
		echo "<div id='barra-superior' class='barra-superior-login'>\n";
                
	}	

	function pre_contenido()
	{
            
//		echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo.gif",true);
//		echo "<div>versi�n ".toba::proyecto()->get_version()."</div>";
//		echo "</div>";
		//echo "\n<div align='center' class='cuerpo'>\n";		
                echo "\n<div align='center' class='wrapper'>\n";
	}

	function post_contenido()
	{
		echo "</div>";		
		
                //echo '<footer>';
                echo '<div class="footer">';
                echo "<div class='login-pie'>";
		echo "<div>Desarrollado por <strong><a href='http://euclides.uncoma.edu.ar/' style='text-decoration: none; color:#337ab7' target='_blank'>EUCLIDES</a></strong></div>
			<div>2002-".date('Y')."</div>";
		echo "</div>";
                
                echo '</div>';
                //echo '</footer>';
	}
}
?>