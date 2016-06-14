<?php
/*
 * *************   BOOTSTRAP    *******************
 */
echo "<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">";


echo '<div align=\'center\'>';
$im=toba_recurso::imagen_proyecto('oneloginTop.png',800,400);
echo $im;
//echo '<img src='.$im.'class=\'responsive-image\'>';

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


$cant = count($datos);
$i = 0;
for ($i; $i < $cant; $i++) {
    $ban=$i%2;
    echo '<div class=\'banner '.$proyectos[$i][0].' col-xs-12 col-sm-6 col-lg-4 \' id=\'pos'.$ban.'\' onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')">';
    echo '<div class=\'proyectos\'>'.$proyectos[$i][0].'</div>';
    //echo toba_form::button($proyectos[$i][0],$proyectos[$i][0],'onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')"');
//    echo '<div class=\'bgimg\' align=\'center\' onClick="vinculador.ir_a_proyecto(\''.$proyectos[$i][0].'\')">Proyecto</div>';
    echo '</div>';
}

echo '</div>';


/*
 * *************   BOOTSTRAP    *******************
 */
//echo "<script src=\"js/jquery.js\"></script>";
//echo "<script src=\"js/bootstrap.min.js\"></script>";
?>