<?php
/*
 * *************   BOOTSTRAP    *******************
 */
echo "<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">";


echo '<div class="contenedor" align=\'center\'>';
echo '<div class="titulo">';
$im=toba_recurso::imagen_proyecto('oneloginTop5.png',1750,500);
echo $im;
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

echo '<div align=\'center\'>';
$proyectos = toba::instancia()->get_proyectos_accesibles();

$datos = rs_convertir_asociativo($proyectos, array(0), 1);
//ei_arbol($proyectos);                             //para mostrar datos

//ESTO PARA EL COMBO
//echo toba_form::select(apex_sesion_qs_cambio_proyecto, $actual, $datos, 'ef-combo', 'onchange="vinculador.ir_a_proyecto(this.value)"');

echo toba_js::abrir();
echo 'var url_proyectos = ' . toba_js::arreglo(toba::instancia()->get_url_proyectos(array_keys($datos)), true);
echo toba_js::cerrar();

echo '<br><br>';

$proyectosMostrados=array();
$cant = count($datos);
$i = 0;
for ($i; $i < $cant; $i++) {
    if(in_array($proyectos[$i][0], $proyectosMostrados)==FALSE)
    {
        array_push($proyectosMostrados, $proyectos[$i][0]);
        $ban=$i%2;
        echo '<div class=\'banner '.$proyectos[$i][0].' col-xs-12 col-sm-6 col-lg-4 \' id=\'pos'.$ban.'\' onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')">';
        echo '<div class=\'proyectos\'>'.$proyectos[$i][0].'</div>';
        //echo toba_form::button($proyectos[$i][0],$proyectos[$i][0],'onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')"');
        //echo '<div class=\'bgimg\' align=\'center\' onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')">Proyecto</div>';
        echo '</div>';
    }
}

echo '</div>';

echo toba_js::abrir();
echo 'function a_Operacion(){';

//    echo toba_js::abrir();
    //echo 'alert(\'hola\')';
    echo  'return toba.ir_a_operacion("onelogin", "1000301", false)';
//  echo toba_js::cerrar();
    
    //toba::vinculador()->navegar_a("", "1000301", true);
    
echo '}';    
echo toba_js::cerrar();
echo "<script src=\"js/jquery.js\"></script>";
?>