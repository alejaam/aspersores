<?php
//  Q = Caudal
$caudal2 = 0;
$caudal3 = 0;
$caudal4 = 0;
$caudal5 = 0;
$caudal6 = 0;
$caudal2Final = 0;
$caudal3Final = 0;
$caudal4Final = 0;
$caudal5Final = 0;
$caudal6Final = 0;
if (isset($_POST)) {
 // CONSTANTES
 $densidadDiseno = 5;

 $coberturaReal = isset($_POST['cobertura_real']) ? $_POST['cobertura_real'] : 0;
 $areaOperacion = isset($_POST['area_operacion']) ? $_POST['area_operacion'] : 0;

 $numRoseadores = round($areaOperacion / $coberturaReal);

 $caudalReal = $coberturaReal * $densidadDiseno;
 $caudalTotalAproximado = $caudalReal * $numRoseadores;

 $presionFinalTramo1 = getTramo1($caudalReal, 0);
 $presionFinalTramo4 = getTramo1($caudalReal, 0);
 $presionFinalTramo8 = getTramo1($caudalReal, 0);
 $presionFinalTramo12 = getTramo1($caudalReal, 0);
 $presionFinalTramo16 = getTramo1($caudalReal, 0);
 $presionFinalTramo2 = getTramo2($presionFinalTramo1, $caudalReal);
 $presionFinalTramo5 = getTramo2($presionFinalTramo4, $caudalReal);
 $presionFinalTramo9 = getTramo2($presionFinalTramo8, $caudalReal);
 $presionFinalTramo13 = getTramo2($presionFinalTramo12, $caudalReal);
 $presionFinalTramo17 = getTramo2($presionFinalTramo16, $caudalReal);
 $presionFinalTramo6 = getTramo6($presionFinalTramo5, $caudal2Final);
 $presionFinalTramo10 = getTramo6($presionFinalTramo9, $caudal2Final);
 $presionFinalTramo14 = getTramo6($presionFinalTramo13, $caudal2Final);
 $presionFinalTramo18 = getTramo6($presionFinalTramo17, $caudal2Final);
 $presionFinalTramo3 = getTramo4($presionFinalTramo2, $caudal2Final);
 $presionFinalTramo7 = _getTramo3($presionFinalTramo6 + $presionFinalTramo3, $caudal6Final * 2);
 $presionFinalTramo11 = _getTramo3($presionFinalTramo10 + $presionFinalTramo7, $caudal6Final * 3);
 $presionFinalTramo15 = _getTramo3($presionFinalTramo14 + $presionFinalTramo11, $caudal6Final * 4);
 $presionFinalTramo19 = _getTramo5($presionFinalTramo15 + $presionFinalTramo18, $caudal6Final * 5);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <?php
buildCircuitoA($caudalReal, $caudal2, $caudal3);
buildCircuitoB($caudalReal, $caudal2, $caudal3);
buildCircuitoC($caudalReal, $caudal2, $caudal3);
buildCircuitoD($caudalReal, $caudal2, $caudal3);
buildCircuitoE($caudalReal, $caudal2, $caudal3);

?>
            </div>
            <div class="col-2"></div>
            <div class="col-6">
                <?php
buildCaudalDemanda($caudalReal + $caudal2 + $caudal3);
echo '<br>';
buildPresion($presionFinalTramo19);
$V1 = velocidadSuccion();
$V2 = velocidadDescarga();

$perdidaPrimariaSuccion = perdidadPrimariaSuccion($V1);
$valvula = perdidadSecundariaSuccion(420, $V1, $perdidaPrimariaSuccion[2]);
$codo90 = perdidadSecundariaSuccion(30, $V1, $perdidaPrimariaSuccion[2]);
$compuerta = perdidadSecundariaSuccion(8, $V1, $perdidaPrimariaSuccion[2]);

$perdidaPrimariaDescarga = perdidadPrimariaDescarga($V2);
$y = perdidadSecundariaSuccion(60, $V2, $perdidaPrimariaDescarga[2]);
$codo90_ = 4 * perdidadSecundariaSuccion(30, $V2, $perdidaPrimariaDescarga[2]);
$codo45 = perdidadSecundariaSuccion(16, $V2, $perdidaPrimariaDescarga[2]);
$valvulaCompuerta = perdidadSecundariaSuccion(8, $V2, $perdidaPrimariaDescarga[2]);
$valvulaCheck = perdidadSecundariaSuccion(100, $V2, $perdidaPrimariaDescarga[2]);

$result[] = $perdidaPrimariaSuccion[3];
$result[] = $valvula;
$result[] = $codo90;
$result[] = $compuerta;
$result[] = $perdidaPrimariaDescarga[3];
$result[] = $y;
$result[] = $codo90_;
$result[] = $codo45;
$result[] = $valvulaCompuerta;
$result[] = $valvulaCheck;

buildResult($result, $presionFinalTramo19);
exit();
?>
            </div>
        </div>
    </div>
</body>

</html>

<?php
function getTramo1($caudalReal, $diametro) {
 // Calcular presión inicial
 $k = 80;
 $c = pow(120, 1.85);
 $d = pow(27.2, 4.87);
 $longTotal1 = 5.12;
 $presionInicial = pow($caudalReal, 2) / pow($k, 2);
 // Perdida de carga por formula de Hazen Williams
 $perdidaCarga = floatval(bcdiv((pow($caudalReal, 1.85) * 605000) / ($c * $d), 1, 4));
 // Calcular presión final
 $presionFinal1 = floatval(bcdiv($presionInicial, 1, 3)) + floatval(bcdiv($perdidaCarga, 1, 3)) * $longTotal1;
 return floatval(bcdiv($presionFinal1, 1, 4));
 // getTramo2(floatval(bcdiv($presionFinal1, 1, 4)), $caudalReal);

 // return floatval(bcdiv($presionFinal1, 1, 4));
}

function getTramo2($presionFinal, $caudalAnterior) {
 global $caudal2, $caudal2Final;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(27.2, 4.87), 1, 2));
 $longTotal2 = 4.79;
 $caudal2 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 $caudal2Final = floatval(bcdiv($caudal2, 1, 2)) + $caudalAnterior;
 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga2 = floatval(bcdiv((pow($caudal2Final, 1.85) * 605000) / ($c * $d), 1, 4));

 // Presión final
 $presionFinal2 = floatval(bcdiv($presionFinal, 1, 4)) + floatval(bcdiv($perdidaCarga2 * $longTotal2, 1, 4));
 return $presionFinal2;
 // getTramo3($presionFinal2, $caudal2Final);
}

function getTramo3($presionFinal, $caudalAnterior) {
 global $caudal3, $caudal3Final;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(35.9, 4.87), 1, 2));
 $longTotal3 = 2.10;
 $caudal3 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 $caudal3Final += floatval(bcdiv($caudal3, 1, 2)) + $caudalAnterior;
 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga3 = floatval(bcdiv((pow($caudal3Final, 1.85) * 605000) / ($c * $d), 1, 4));

 // Presión final
 $presionFinal3 = floatval(bcdiv($presionFinal + $perdidaCarga3 * $longTotal3, 1, 4));
 return $presionFinal3;
}

function _getTramo3($presionFinal, $caudalAnterior) {

 global $caudal3;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(42.7, 4.87), 1, 2));
 $longTotal3 = 2.10;
 // $caudal3 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 // $caudal3Final = floatval(bcdiv($caudal3, 1, 2)) + $caudalAnterior;
 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga3 = floatval(bcdiv((pow($caudalAnterior, 1.85) * 605000) / ($c * $d), 1, 4));
 // Presión final
 $presionFinal3 = floatval(bcdiv($presionFinal, 1, 4)) + floatval(bcdiv($perdidaCarga3 * $longTotal3, 1, 4));
 return $presionFinal3;
}

function getTramo4($presionFinal, $caudalAnterior) {

 global $caudal3, $caudal4Final;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(35.9, 4.87), 1, 2));
 $longTotal3 = 7.5;
 $caudal3 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 $caudal4Final = floatval(bcdiv($caudal3, 1, 2)) + $caudalAnterior;

 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga3 = floatval(bcdiv((pow($caudal4Final, 1.85) * 605000) / ($c * $d), 1, 4));

 // Presión final
 $presionFinal3 = floatval(bcdiv($presionFinal + $perdidaCarga3 * $longTotal3, 1, 4));

 return $presionFinal3;
}

function getTramo5($presionFinal, $caudalAnterior) {
 global $caudal5, $caudal5Final;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(35.9, 4.87), 1, 2));
 $longTotal3 = 4.05;
 $caudal5 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 $caudal5Final = floatval(bcdiv($caudal5, 1, 2)) + $caudalAnterior;

 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga3 = floatval(bcdiv((pow($caudal5Final, 1.85) * 605000) / ($c * $d), 1, 4));

 // Presión final
 $presionFinal3 = floatval(bcdiv($presionFinal + $perdidaCarga3 * $longTotal3, 1, 4));

 return $presionFinal3;
}

function _getTramo5($presionFinal, $caudalAnterior) {
 global $caudal3;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(64, 4.87), 1, 2));
 $longTotal3 = 4.05;
 // $caudal3 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 // $caudal3Final = floatval(bcdiv($caudal3, 1, 2)) + $caudalAnterior;
 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga3 = floatval(bcdiv((pow($caudalAnterior, 1.85) * 605000) / ($c * $d), 1, 4));
 // Presión final
 $presionFinal3 = floatval(bcdiv($presionFinal, 1, 3)) + floatval(bcdiv($perdidaCarga3 * $longTotal3, 1, 4));
 return $presionFinal3;
}

function getTramo6($presionFinal, $caudalAnterior) {
 global $caudal6, $caudal6Final;
 $k = 80;
 $c = floatval(bcdiv(pow(120, 1.85), 1, 2));
 $d = floatval(bcdiv(pow(35.9, 4.87), 1, 2));
 $longTotal3 = 4.92;
 $caudal6 = floatval(bcdiv($k * sqrt($presionFinal), 1, 2));
 $caudal6Final = floatval(bcdiv($caudal6, 1, 2)) + $caudalAnterior;

 // Perdida de carga por la formula de HAzen Williams
 $perdidaCarga3 = floatval(bcdiv((pow($caudal6Final, 1.85) * 605000) / ($c * $d), 1, 4));

 // Presión final
 $presionFinal3 = floatval(bcdiv($presionFinal + $perdidaCarga3 * $longTotal3, 1, 4));

 return $presionFinal3;
}

function buildCircuitoA($q1, $q2, $q3) {
 echo '<table class="table table-hover text-center" width="80%">
    <thead>
      <tr>
        <th scope="col" colspan="2">Circuito A</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">Rociador 1</th>
        <td> Q = ' . $q1 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 2</th>
        <td> Q = ' . $q2 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 3</th>
        <td> Q = ' . $q3 . '</td>
      </tr>
    </tbody>
  </table>';
}
function buildCircuitoB($q1, $q2, $q3) {
 echo '<table class="table table-hover text-center" width="80%">
    <thead>
      <tr>
        <th scope="col" colspan="2">Circuito B</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">Rociador 4</th>
        <td> Q = ' . $q1 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 5</th>
        <td> Q = ' . $q2 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 6</th>
        <td> Q = ' . $q3 . '</td>
      </tr>
    </tbody>
  </table>';
}
function buildCircuitoC($q1, $q2, $q3) {
 echo '<table class="table table-hover text-center" width="80%">
    <thead>
      <tr>
        <th scope="col" colspan="2">Circuito C</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">Rociador 7</th>
        <td> Q = ' . $q1 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 8</th>
        <td> Q = ' . $q2 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 9</th>
        <td> Q = ' . $q3 . '</td>
      </tr>
    </tbody>
  </table>';
}
function buildCircuitoD($q1, $q2, $q3) {
 echo '<table class="table table-hover text-center" width="80%">
    <thead>
      <tr>
        <th scope="col" colspan="2">Circuito D</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">Rociador 10</th>
        <td> Q = ' . $q1 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 11</th>
        <td> Q = ' . $q2 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 12</th>
        <td> Q = ' . $q3 . '</td>
      </tr>
    </tbody>
  </table>';
}
function buildCircuitoE($q1, $q2, $q3) {
 echo '<table class="table table-hover text-center" width="80%">
    <thead>
      <tr>
        <th scope="col" colspan="2">Circuito E</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">Rociador 13</th>
        <td> Q = ' . $q1 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 14</th>
        <td> Q = ' . $q2 . '</td>
      </tr>
      <tr>
        <th scope="row">Rociador 15</th>
        <td> Q = ' . $q3 . '</td>
      </tr>
    </tbody>
  </table>';
}

function buildCaudalDemanda($caudalTotal) {
 echo '<table class="table table-hover text-center" width="80%">
    <thead>
      <tr>
        <th scope="col">Circuito</th>
        <th scope="col">∑ Caudal de demanda por aspersores    Q= (lpm)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">A</th>
        <td> ' . $caudalTotal . '</td>
      </tr>
      <tr>
        <th scope="row">B</th>
        <td> ' . $caudalTotal . '</td>
      </tr>
      <tr>
        <th scope="row">C</th>
        <td> ' . $caudalTotal . '</td>
      </tr>
      <tr>
        <th scope="row">D</th>
        <td> ' . $caudalTotal . '</td>
      </tr>
      <tr>
        <th scope="row">E</th>
        <td> ' . $caudalTotal . '</td>
      </tr>
      <tr>
        <th scope="row">Demanda Total</th>
        <td> ' . $caudalTotal * 5 . '</td>
      </tr>
    </tbody>
  </table>
  ';
}

function buildPresion($presion) {
 echo '<h5>Obteniendo así la Presión mínima en el punto G:</h5>
 <table class="table table-hover text-center" width="80%">
    <tbody>
      <tr>
        <td scope="row"> Presión (bar) </td>
        <td> ' . $presion  . '</td>
      </tr>
      <tr>
        <td scope="row"> Presión (psi) </td>
        <td> ' . $presion * 14.5038 . '</td>
      </tr>
    </tbody>
  </table>';
}

function velocidadSuccion(){
    $demandaTotal = 0.01541;
    $V = ($demandaTotal * 4)/(pi() * pow(0.1524, 2));

    return floatval(bcdiv($V, 1, 4));
}   
function velocidadDescarga(){
    $demandaTotal = 0.01541;
    $V = ($demandaTotal * 4)/(pi() * pow(0.0762, 2));

    return floatval(bcdiv($V, 1, 4));
}   
function perdidadPrimariaSuccion($V){
    $Re = floatval(($V * 0.1524)/ 0.000001106);
    $De = (0.1524)/(0.00015);
    $f = floatval(bcdiv((0.25)/(pow(log(((1)/(3.7*$De))+((5.74)/(pow($Re, 0.9))), 10), 2)), 1, 4));
    $hl = floatval(bcdiv(($f) * ((2.9)/(0.1524)) * ((pow(0.8447,2))/(2*9.81)), 1, 4));
    $perdidadPrimariaSuccion[] = $Re;
    $perdidadPrimariaSuccion[] = $De;
    $perdidadPrimariaSuccion[] = $f;
    $perdidadPrimariaSuccion[] = $hl;
    return $perdidadPrimariaSuccion;

}
function perdidadSecundariaSuccion($K, $V , $f){
    $Hl = floatval(bcdiv(($K * $f) * ((pow($V,2))/(2*9.81)), 1, 4));
    return $Hl;
}
function perdidadPrimariaDescarga($V){
    $Re = floatval(($V * 0.0762)/ 0.000001106);
    $De = (0.0762)/(0.00015);
    $f = floatval(bcdiv((0.25)/(pow(log(((1)/(3.7*$De))+((5.74)/(pow($Re, 0.9))), 10), 2)), 1, 4));
    $hl = floatval(bcdiv(($f) * ((25.2)/(0.0762)) * ((pow(3.3791,2))/(2*9.81)), 1, 4));
    $perdidadPrimariaSuccion[] = $Re;
    $perdidadPrimariaSuccion[] = $De;
    $perdidadPrimariaSuccion[] = $f;
    $perdidadPrimariaSuccion[] = $hl;
    return $perdidadPrimariaSuccion;
}
function perdidadSecundariaDescarga($K, $V){
    $Hl = floatval(bcdiv(($K * 0.02426) * ((pow($V,2))/(2*9.81)), 1, 4));
    return $Hl;
}

function cargaDinamicaTotal($presion){
    return $hb = floatval(bcdiv((($presion)/(998.9 * 9.81)) + (3.80) + ((pow(3.3791, 2) - pow(0.8447,2))/(2 * 9.81)) + (9.333), 1, 4));
}


function buildResult($result, $presion){
    echo '
 <table class="table table-hover text-center table-bordered" width="80%">
 <thead>
      <tr>
        <th scope="col" colspan="2">Perdidas</th>
      </tr>
    </thead> 
 <tbody>
      <tr>
        <td scope="row" rowspan="4" style="vertical-align : middle;text-align:center;"> Succión </td>
        <td>' . $result[0] .'m</td>
      </tr>
      <tr>
        <td>' . $result[1] .'m</td>
      </tr>
      <tr>
        <td>' . $result[2] .'m</td>
      </tr>
      <tr>
        <td>' . $result[3] .'m</td>
      </tr>
      <tr>
      <td scope="col">Subtotal 1</td>
        <td>' . array_sum(array_slice($result, 0, 4)) .'m</td>
      </tr>
      <tr>
        <td scope="row" rowspan="6" style="vertical-align : middle;text-align:center;"> Descarga </td>
        <td>' . $result[4] .'m</td>
      </tr>
      <tr>
        <td>' . $result[5] .'m</td>
      </tr>
      <tr>
        <td>' . $result[6] .'m</td>
      </tr>
      <tr>
        <td>' . $result[7] .'m</td>
      </tr>
      <tr>
        <td>' . $result[8] .'m</td>
      </tr>
      <tr>
        <td>' . $result[9] .'m</td>
      </tr>
      <tr>
      <td scope="col">Subtotal 2</td>
        <td>' . array_sum(array_slice($result, 4)) .'m</td>
      </tr>
      <td scope="col">Total</td>
        <td>' . array_sum($result) .'m</td>
      </tr>
    </tbody>
  </table>
  <h5>Por lo tanto tenemos que la carga dinamica total es: ' .  cargaDinamicaTotal($presion * 14.5038 ) . '</h5>
  ';
}
?>