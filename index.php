<?php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  if(isset($_POST['submit']) && $_POST['submit'] == 'Generar reporte') {
    include_once('./lib.php');

    // Upload y procesar POST
    $temp_asrun = tempnam(sys_get_temp_dir(), 'ASRUN');
    $temp_pauta = tempnam(sys_get_temp_dir(), 'PAUTA');
    $error = false;

    if(! move_uploaded_file($_FILES["asrun"]["tmp_name"], $temp_asrun)) {
      echo '<div class="error">Error cargando ASRUN</div>';
      $error = true;
    }
    if(! move_uploaded_file($_FILES["pauta"]["tmp_name"], $temp_pauta)) {
      echo '<div class="error">Error cargando PAUTA</div>';
      $error = true;
    }
    $formato = 'DOCX';
    /*if(isset($_POST['formato'])) {
      $formato = $_POST['formato'];
    }*/

    $datos_generales = array(
      'cliente' => $_POST['cliente'],
      'desde' => $_POST['desde'],
      'hasta' => $_POST['hasta'],
      'pauta' => $_POST['nombre_pauta']
    );

    // Descargar el reporte
    if(! $error) {
      generarReporte($temp_pauta, $temp_asrun, $formato, TRUE, $datos_generales);
    }
  }

  $html = file_get_contents('upload.html');
  echo $html;
