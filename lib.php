<?php
define('NUM_COLUMNAS_ROTATIVO', 3);

date_default_timezone_set('America/Argentina/Buenos_Aires');
include_once('./tbs/tbs_class.php'); // Load the TinyButStrong template engine
include_once('./opentbs/tbs_plugin_opentbs.php'); // Load the OpenTBS plugin


function procesarArchivoObjetivos($path_archivo_objetivos) {
  $objetivos = array();
  $archivo_objetivos = fopen($path_archivo_objetivos, "r");
  if ($archivo_objetivos) {
    while (($line = fgets($archivo_objetivos)) !== false) {
      $line_data = explode(",", $line);
      if(count($line_data) < NUM_COLUMNAS_ROTATIVO)
      {
        $line_data = array(
          'code' => trim($line_data[0]),
          'name' => trim($line_data[1]),
          'results' => array()
        );
      } else {
        $line_data = array(
          'code' => trim($line_data[0]),
          'name' => trim($line_data[1]),
          'from_date' => trim($line_data[2]),
          'from_time' => trim($line_data[3]),
          'to_date' => trim($line_data[4]),
          'to_time' => trim($line_data[5]),
          'objective' => trim($line_data[6]),
          'results' => array()
        );
      }
      $objetivos[] = $line_data;
    }
  }

  return $objetivos;
}

function procesarArchivoEmisiones($path_archivo_emisiones) {
  $emisiones = array();

  $archivo_emisiones = fopen($path_archivo_emisiones, "r");
  if ($archivo_emisiones) {
    $last_date = false;
    while (($line = fgets($archivo_emisiones)) !== false) {
      // Evitar errores con UTF8 y formatos DOS
      $line = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line);

      if(trim(substr($line, 0, 5)) === 'Reel:') { //si la línea actual es una fecha
        $line = trim($line);
        for($i = 1; $i < 11; $i++) {
          $line = str_replace(' - V' . $i, '', $line);
          $line = str_replace(' -V' . $i, '', $line);
          $line = str_replace('V' . $i, '', $line);
        }
        $line = str_replace(' - V' . $i, '', $line);

        $last_date = implode('-', array_reverse(explode('/', substr(trim($line), -10))));
        if($last_date == '2013-8-S 9') die(var_dump($line));
      } elseif (substr($line, 0, 3) === ' , ') { //si la línea actual es de contenido
        if (! ($last_date === false)) { //y ya tengo una fecha
          $line_data = explode(',', $line);
          if(trim($line_data[1]) !== 'Code') { //y no es un título
            //armo una fecha y hora con el registro actual
            $test_datetime = new DateTime($last_date  . trim($line_data[6]));

            //si es menor que la última que armé, es porque cambió de día
            if(empty($old_datetime) || $old_datetime < $test_datetime) {
              //sumo un día
              $test_datetime->add(date_interval_create_from_date_string('1 day'));

              //actualizo los valores
              $old_datetime = $test_datetime;
              $last_date = $test_datetime->format('Y-m-d');
            }

            $line_data = array(
              'code' => trim($line_data[1]),
              'name' => trim($line_data[2]),
              'client' => trim($line_data[3]),
              'agency' => trim($line_data[4]),
              'duration' => trim($line_data[5]),
              'time' => trim($line_data[6]),
              'pos' => trim($line_data[7]),
              'date' => $last_date,
              'datetime' => $last_date . ' ' . trim($line_data[6])
            );

            try {
              $datetime = new DateTime($line_data['datetime']);
            } catch( Exception $e ) { // Se formó una fecha inválida
              die(var_dump($last_date));
            }
            $mex_time = new DateTimeZone('America/Mexico_City');
            $datetime->setTimezone($mex_time);
            $line_data['mex_datetime'] = $datetime->format('Y-m-d H:i:s');
            $line_data['mex_date'] = $datetime->format('Y-m-d');
            $line_data['mex_time'] = $datetime->format('H:i:s');

            $emisiones[] = $line_data;
          }
        }
      }
    }
  }
  return $emisiones;
}

function compararObjetivosConEmisiones($objetivos, $emisiones) {
  foreach($objetivos as $key => &$objetivo) {
    foreach($emisiones as $emision) {
      if($emision['code'] === $objetivo['code']) {
        if(count($objetivo) <= NUM_COLUMNAS_ROTATIVO) { // Rotativos
          $objetivo['results'][] = $emision;
        } else {
          if(emisionEnRangoDeObjetivo($emision['mex_date'], $emision['mex_time'], $objetivo['from_date'], $objetivo['to_date'], $objetivo['from_time'], $objetivo['to_time'])) {
            $objetivo['results'][] = $emision;
          }
        }
      }
    }
  }
  return $objetivos;
}

function emisionEnRangoDeObjetivo($fecha_emision, $hora_emision, $fecha_inicio_objetivo, $fecha_fin_objetivo, $hora_inicio_objetivo, $hora_fin_objetivo) {
  if( strtotime($fecha_emision) >= strtotime($fecha_inicio_objetivo)
      && strtotime($fecha_emision) <= strtotime($fecha_fin_objetivo . ' 23:59:59')
  ) {
    if( strtotime('2013-01-01 ' . $hora_emision) >= strtotime('2013-01-01 ' . $hora_inicio_objetivo)
        && strtotime('2013-01-01 ' . $hora_emision) <= strtotime('2013-01-01 ' . $hora_fin_objetivo)
    ) {
      return true;
    }
  }
  return false;
}

function generarReporte($path_archivo_objetivos, $path_archivo_emisiones, $formato = "DOCX", $descargar = false, $datos_generales = false) {
  $objetivos = procesarArchivoObjetivos($path_archivo_objetivos);
  $emisiones = procesarArchivoEmisiones($path_archivo_emisiones);
  $resultados = compararObjetivosConEmisiones($objetivos, $emisiones);

  foreach($resultados as &$resultado) {
    if(count($resultado) > NUM_COLUMNAS_ROTATIVO) {
      $resultado['progress'] = count($resultado['results']) . '/' . $resultado['objective'];
    }
    $resultado['details'] = array();
    foreach($resultado['results'] as $detalle) {
      $resultado['details'][]= $detalle['mex_datetime'];
    }
    $resultado['details'] = "" . implode("\n", $resultado['details']);
  }

  $TBS = new clsTinyButStrong;
  $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

  if(count($resultados[0]) > (NUM_COLUMNAS_ROTATIVO + 1)) {
    $template = 'template_completo';
  } else {
    $template = 'template_rotativo';
  }

  $template .= '.' . strtolower($formato);
  $TBS->LoadTemplate($template);
  if($datos_generales) {
    $TBS->PlugIn(OPENTBS_SELECT_HEADER);
    $TBS->MergeBlock('general', array('general' => $datos_generales));
    $TBS->PlugIn(OPENTBS_SELECT_MAIN);
  }
  $TBS->MergeBlock('r', $resultados);
  $output_file_name = str_replace('.', '_'.date('Y-m-d').'.', $template);
  if($descargar) {
    $TBS->Show(OPENTBS_DOWNLOAD, $output_file_name);
  } else {
    $TBS->Show(OPENTBS_FILE, $output_file_name);
    return "El reporte generado está en $output_file_name.\n";
  }
}
