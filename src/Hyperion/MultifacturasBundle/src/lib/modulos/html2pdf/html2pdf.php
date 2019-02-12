<?php
error_reporting(0);
// reference the Dompdf namespace
use Dompdf\Dompdf;
function ___html2pdf($datos)
{
    //date_default_timezone_set('America/Mexico_City');
    $html=$datos['html'];
    $archivo_html=$datos['archivo_html'];
    if($archivo_html!='')
    {
        if(file_exists($archivo_html))
            $html=file_get_contents($archivo_html);
        else
            $html="No se encuentra el archivo $archivo_html";
    }
    $archivo_pdf=$datos['archivo_pdf'];

    // include autoloader
    require_once 'dompdf/autoload.inc.php';

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();
    $html=utf8_encode($html);
    $dompdf->load_html($html);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents($archivo_pdf, $output);  //GUARDA EL PDF
    //$dompdf->stream($archivo_pdf); //DESCARGA EL PDF 

    //============================================================+
    // END OF FILE
    //============================================================+
    return $res['resultado']='OK';
}
?>