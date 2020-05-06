<?php
/*
 * *************   BOOTSTRAP    *******************
 */
echo "<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">";


echo '<div class="contenedor" align=\'center\'>';

//echo toba::usuario()->get_nombre();
//ei_arbol(gestion_arai_usuarios::get_nombre_usuario_arai(toba::usuario()->get_id()));   //imprime datos
//$aca=toba::instancia()->get_info_usuario(toba::usuario()->get_id());
$bdtoba=toba::instancia()->get_db();
//$sql = "SELECT email FROM apex_usuario WHERE usuario='".toba::usuario()->get_id()."'";
//ei_arbol($bdtoba->consultar($sql)[0]);

echo '<div class="titulo">';
$im=toba_recurso::imagen_proyecto('oneloginTop5.png',1750,500);
echo $im;
//$im2=toba_recurso::imagen_proyecto('iconoOne4.png',70,70);
//echo $im2;
echo '</div>';
echo '<div class="cabecera" align=\'center\'>';
echo '<a href="#" class="enc-usuario" title="Usuario" onclick="a_Operacion()">';
$usu=toba_recurso::imagen_proyecto('usuario2.png',60,60);
echo $usu;
echo '</a>';
echo '<a href="#" class="enc-salir" title="Cerrar la sesion" onclick="javascript:salir()">';
$exi=toba_recurso::imagen_proyecto('salir2.png',60,60);
echo $exi;
echo '</a>';
echo '</div>';

echo '</div>';

echo '<div class="wrapper">';
echo '<div class=\'container\' align=\'center\'>';

$proyectos = toba::instancia()->get_proyectos_accesibles();

$datos = rs_convertir_asociativo($proyectos, array(0), 1);
//ei_arbol($proyectos);                             //para mostrar datos

//ESTO PARA EL COMBO
//echo toba_form::select(apex_sesion_qs_cambio_proyecto, $actual, $datos, 'ef-combo', 'onchange="vinculador.ir_a_proyecto(this.value)"');

echo toba_js::abrir();
echo 'var url_proyectos = ' . toba_js::arreglo(toba::instancia()->get_url_proyectos(array_keys($datos)), true);
echo toba_js::cerrar();

echo '<br><br>';
//ei_arbol($proyectos);
$proyectosMostrados=array();
$cant = count($proyectos);
$i = 0;
for ($i; $i < $cant; $i++) {
    if(in_array($proyectos[$i][0], $proyectosMostrados)==FALSE)
    {
        array_push($proyectosMostrados, $proyectos[$i][0]);
        $ban=$i%2;
        echo '<div class=\'banner '.$proyectos[$i][0].' col-xs-12 col-sm-6 col-lg-4 \'  onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')">';
        echo '<div class=\'proyectos\'>'.$proyectos[$i][0].'</div>';
        //echo toba_form::button($proyectos[$i][0],$proyectos[$i][0],'onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')"');
        //echo '<div class=\'bgimg\' align=\'center\' onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')">Proyecto</div>';
        echo '</div>';
    }
}
echo '<div class=\'banner resultados col-xs-12 col-sm-6 col-lg-4 \' onclick="window.location=\'http://gukena.fi.uncoma.edu.ar/resultados/\';" >';
echo '<div class=\'proyectos\'>Resultados</div>';
echo '</div>';


echo '</div>';
echo '</div>';

echo '<div class="footer">';
        
        echo "<div class='login-pie'>";
        echo "<div>Desarrollado por <strong><a href='http://euclides.uncoma.edu.ar/' style='text-decoration: none' target='_blank'>EUCLIDES</a></strong></div>
			<div>2002-".date('Y')."</div>";
        echo '</div>';
        echo '<a href="#" title="Pedco" onclick="window.location=\'http://pedco.uncoma.edu.ar/\';">';
        $pedco=toba_recurso::imagen_proyecto('logo-pedco3.png',200,100);
        echo $pedco;
        echo '</a>';
        
        
echo '</div>';

echo toba_js::abrir();
echo 'function a_Operacion(){';

    echo  'return toba.ir_a_operacion("onelogin", "1000301", false)';
    
echo '}';    
echo toba_js::cerrar();

//ei_arbol($bdtoba->get_lista_tablas());
//$sql = "SELECT * FROM apex_usuario";
//ei_arbol($bdtoba->consultar($sql));
?>