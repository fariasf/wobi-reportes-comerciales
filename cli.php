<?php
  if(count($argv) < 3) {
    die("Uso: php generar_reporte.php ASRUN.txt PAUTA.csv\n");
  }
  include_once('./lib.php');

  $path_asrun = $argv[1];
  $path_objetivos = $argv[2];
  $formato = "ODT";
  if(isset($argv[3])) {
    $formato = $argv[3];
  }

  exit(generarReporte($path_objetivos, $path_asrun, $formato));
