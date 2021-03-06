<?php
date_default_timezone_set('Mexico/General');

require_once 'componentes.php';
require_once 'database.php';
require 'libreria.php';

$tipo = $_REQUEST['consulta'];
$dias = $_REQUEST['dias'];
$tipoOficio = $_REQUEST['tipoOficio'];


$db = new libreria();

// $db->tipotipoAviso = $tipo;

$consulta = 'getAvisosAI';
$result = $db->{$consulta}($tipo, $tipoOficio);
$i = 1;
$data = array();

if ($result == "{}") {
     $data[] = 	 "No hay nada";
} else {
	foreach ($result as $row => $r) {
        $hoy = date("Y-m-d");

        $colchon = fechaFinal($r['fechanot'], 0);

        //$dias = 10;
        $gap = fechaFinal($colchon, $dias);


        $ban = "-"; 

        if ($r['fechanot'] == '0000-00-00' or is_null($r['fechanot'])) {
            $diferencia = "--";
            $ban = "--";
            $gap = "--";
        } else {
            if ($r['acuse'] == '0000-00-00' or is_null($r['acuse'])) {
                $diferencia = getWorkingDays($hoy, $gap);
                $plazo = "-";
            } else {
                $diferencia = "-";
        
                $plazo = getWorkingDays($r['fechanot'], $r['acuse']);

                $date1 = new DateTime($gap);
                $date2 = new DateTime($r['acuse']);
                if($date2> $date1) {
                    $ban = "no";
                } else {
                    $ban = "-"; 
                };
            }
        }
        if ( ( $diferencia > -3 and $diferencia <=5) and is_null($r['acuse']) )  { 

        $data[] = array(
            'id' => floatval($r['id']),
            'procedimiento' => $r['procedimiento'],
            'juicionulidad' => $r['ai'],
            'actor' => $r['actor'],
            'f_aviso' => ($r['fechanot'] == '0000-00-00' ? ' ' : $r['fechanot']),
            'fechanot' => $r['fechanot'],
            'tres' => $colchon,

            'dias30' => $gap,
            'diff' => round($diferencia),
            'ban' => $ban,
            'fecha_oficio' => $r['fecha_oficio'],
            'acuse' => $r['acuse'],
            'plazo' => $plazo,
            'vencido' => ($diferencia <= 0 ? 'VENCIDO ' : ' '),
            'folio' => $r['folio'],
            'volante' => $r['volante'],
            'sub' => $r['sub']
        );
        }
	}
}
echo json_encode($data);				   
Database::disconnect();
	
?>