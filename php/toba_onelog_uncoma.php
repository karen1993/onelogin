<?php
/**
 * Tipo de p�gina pensado para pantallas de login, presenta un logo y un pie de p�gina b�sico
 * 
 * @package SalidaGrafica
 */
class toba_onelog_uncoma extends toba_tp_basico
{
	function barra_superior()
	{
		echo "
			<style type='text/css'>
				.cuerpo {
					    margin-top: 10%;
				}
                                body { 
                                    //top:20%;
                                    align-text:center;
                                    //font-size:50px;
                                    //font-size: 1rem;
                                    //font-family: Helvetica, Arial, sans-serif;
                                    //text-rendering: optimizeLegibility;
                                    color: #FFFFFF  ; 
                                     
                                    background-image: url(\"img/unco.png\");
                                    background-repeat:no-repeat;
                                    background-attachment: fixed;
                                    background-position: center;
                                    background-color: #a3a3c2;
                                }
                                #formulario_toba
                                {
                                    background-image: url(\"img/fondobox.jpg\");
                                    width: 400px;
                                    padding: 25px;
                                    border-radius: 100px 49px 100px 49px;
                                    box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
                                    opacity: 0.9;
                                    filter: alpha(opacity=90);
                                    text-align:center;
                                    float:center;
                                }
                                input
                                {
                                    padding: 11px 20px;
                                    margin: 7px 0;
                                    width: 200px;
                                    border-radius: 10px 10px 10px 10px;
                                }
                                input:focus 
                                {
                                    background-color: lightblue;
                                }

                                button 
                                {
                                    display: inline-block;
                                    border-radius: 4px;
                                    background-color: #6699ff;
                                    border: none;
                                    color: #FFFFFF;
                                    text-align: center;
                                    font-size: 20px;
                                    padding: 20px;
                                    width: 150px;
                                    transition: all 0.5s;
                                    cursor: pointer;
                                    margin: 5px;
                                }
                                button span 
                                {
                                    cursor: pointer;
                                    display: inline-block;
                                    position: relative;
                                    transition: 0.5s;
                                }
                                .ei-base{  border:none;
                                }
                                #js_ci_1000700_cont
                                {   background-color: transparent;  //color tabla
                                    
                                }
                                .ei-barra-sup
                                {   display:none;       //cabecera tabla
                                }
                                .ei-form-fila
                                {   color: #FFFFFF;
                                }
                                .ei-form-etiq, .ei-form-etiq-oblig
                                {   padding:15px;
                                font-size: 20px;
                                }
                                button span:after 
                                {
                                    content:'>>';
                                    position: absolute;
                                    opacity: 0;
                                    top: 0;
                                    right: -20px;
                                    transition: 0.5s;
                                }
                                button:hover span 
                                {
                                    padding-right: 25px;
                                }
                                button:hover span:after 
                                {
                                    opacity: 1;
                                    right: 0;
                                }
			</style>
		";
		echo "<div id='barra-superior' class='barra-superior-login'>\n";		
	}	

	function pre_contenido()
	{
		//echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo.gif",true);
		//echo "<div>versi�n ".toba::proyecto()->get_version()."</div>";
		//echo "</div>";
		echo "\n<div align='center' class='cuerpo'>\n";		
	}

	function post_contenido()
	{
		echo "</div>";		
		echo "<div class='login-pie'>";
		echo "<div>Desarrollado por <strong><a href='http://www.siu.edu.ar' style='text-decoration: none' target='_blank'>SIU</a></strong></div>
			<div>2002-".date('Y')."</div>";
		echo "</div>";
	}
}
?>