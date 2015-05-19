#Generación de Reportes de Cumplimiento de Pautas Comerciales

Versión 0.0.1

##Descripción

El programa lee una pauta comercial en formato CSV junto con el archivo proporcionado por el telepuerto (ASRUN), los procesa y los compara para determinar el cumplimiento de las pautas comerciales.

Devuelve el resultado en un archivo nuevo, usando como base los archivos Template ("template\_completo.odt" o "template\_completo.docx").

##Instalación

Requerimientos:

  * PHP 5.5 o posterior
  * Extensión Zlib
  * Opcional: Servidor web (para usar la interfaz visual)

Si se va a usar sólo desde la línea de comandos, descargar todos los archivos del proyecto a una carpeta y usar.

Si se va a usar la interfaz web, descargar todos los archivos a una ruta accesible desde el servidor. Asegurarse que los límites para carga de archivos permitan subir los archivos a procesar.

##Uso

Se puede usar desde la línea de comandos:

    php cli.php ASRUN.txt PAUTA.csv [ODT|DOCX]

O accediendo desde un navegador (http://localhost/reportes_comerciales/index.php) y cargando los archivos.

##Detalles

###Pautas

Las pautas comerciales deben estar en formato CSV (texto separado por comas), sin encabezados.
En el caso de pautas completas deben completarse los siguientes campos:

    CODIGO (Ej: MBMP02285S, código del material pautado)
    NOMBRE (Ej: IDEAS EXCHANGE HSBC, nombre del material pautado)
    FECHA_DESDE (Ej: 2013-08-05, fecha de inicio de la pauta en formato AAAA-MM-DD)
    HORA_DESDE (Ej: 21:00:00, hora de inicio de la pauta en formato HH:MM:SS)
    FECHA_HASTA (Ej: 2013-08-05, fecha de finalización de la pauta en formato AAAA-MM-DD)
    HORA_HASTA (Ej: 23:59:59, hora de finalización de la pauta en formato HH:MM:SS)
    OBJETIVO (Ej: 2, cantidad de veces pautadas en las que el material debería emitirse)

Los horarios corresponden a la hora de Argentina.

###ASRUN

El formato del archivo ASRUN debe coincidir con el ejemplo (ASRUN WOBI AGOSTO.txt), tal cual es enviado por el telepuerto.

###Proceso

El programa verifica por cada material si fue emitido o no dentro de las fechas y horas pautadas.
Para ello la pauta debe indicar claramente el objetivo. Por ejemplo, si se indica:

    MBMP02285S,IDEAS EXCHANGE HSBC,2013-08-01,21:00:00,2013-08-30,23:59:59,30

Se entiende que el material MBMP02285S debería emitirse 30 veces a lo largo del mes, entre las 21:00 y las 23:59 (hora argentina).

###Reportes

Los reportes se emiten en un archivo ODT (OpenOffice) o DOCX (Microsoft Word) tomando como base los archivos template_* y combinándolos con los resultados de los cálculos. En este caso los horarios son de México.

## Licencia

### Créditos

Estilos para formularios: http://www.sanwebe.com/2013/10/css-html-form-styles
Generación de archivos ODT/DOCX: http://www.tinybutstrong.com/opentbs.php

### Licencia

Copyright (c) 2015 Facundo Manuel Farias <contacto@facundofarias.com.ar>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
