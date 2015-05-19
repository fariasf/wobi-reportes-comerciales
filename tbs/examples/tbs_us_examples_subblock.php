<?php
error_reporting(0);
include_once('tbs_class.php');
include_once('../../opentbs/tbs_plugin_opentbs.php'); // Load the OpenTBS plugin
include_once('../../conexion.php');
// Create data
$TeamList[0] = array('nombre'=>'Eagle'  ,'electores'=>'458');
$TeamList[0]['municipios'][] = array('nombre'=>'London','score'=>'253','date'=>'1999-11-30');
$TeamList[0]['municipios'][] = array('nombre'=>'Paris' ,'score'=>'145','date'=>'2002-07-24');
$TeamList[1] = array('nombre'=>'Goonies','electores'=>'281');
$TeamList[1]['municipios'][] = array('nombre'=>'New-York','score'=>'365','date'=>'2001-12-25');
$TeamList[1]['municipios'][] = array('nombre'=>'Madrid'  ,'score'=>'521','date'=>'2004-01-14');
$TeamList[2] = array('nombre'=>'MIB'    ,'electores'=>'615');
$TeamList[2]['municipios'][] = array('nombre'=>'Dallas'    ,'score'=>'362','date'=>'2001-01-02');
$TeamList[2]['municipios'][] = array('nombre'=>'Lyon'      ,'score'=>'321','date'=>'2002-11-17');
$TeamList[2]['municipios'][] = array('nombre'=>'Washington','score'=>'245','date'=>'2003-08-24');

$sqlSecciones = "SELECT * FROM secciones ORDER BY id";
  $qSecciones = $db->query($sqlSecciones);
  while($seccion = $qSecciones->fetch_assoc()) {
    $nueva_seccion = $seccion;

    $nueva_seccion['electores'] = 0;
    $nueva_seccion['mesas'] = 0;
    $nueva_seccion['activos'] = 0;
    $nueva_seccion['fiscales'] = 0;
    $nueva_seccion['municipios'] = array();
    $sqlMunicipios = "SELECT * FROM municipios WHERE id_seccion = " . $seccion['id'] . " ORDER BY nombre";
    $qMunicipios = $db->query($sqlMunicipios);
    while($municipio = $qMunicipios->fetch_assoc()) {
      $municipio['seccion'] = $seccion['nombre'];
      $nueva_seccion['municipios'][] = $municipio;
      $nueva_seccion['electores'] += $municipio['electores'];
      $nueva_seccion['mesas'] += $municipio['mesas'];
      $nueva_seccion['activos'] += $municipio['activos'];
      $nueva_seccion['fiscales'] += $municipio['fiscales'];
    }
    $secciones[] = $nueva_seccion;
  }

$TBS = new clsTinyButStrong;
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin
$TBS->LoadTemplate('tbs_us_examples_subblock.odt');

// Automatic subblock
$TBS->MergeBlock('asb',$secciones);

// Subblock with a dynamic query
$TBS->MergeBlock('mb','array','TeamList');
$TBS->MergeBlock('sb','array','TeamList[%p1%][municipios]');


$TBS->Show();

?>
