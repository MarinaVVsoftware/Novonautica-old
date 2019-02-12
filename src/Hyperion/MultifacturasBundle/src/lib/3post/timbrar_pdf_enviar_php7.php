<?php

function ini_timbrar_pdf_enviar($datos, $res)
{
    
}

function mf_timbrar_pdf_enviar($datos, $respuesta_sdk)
{
    $respuesta_modulo = array(
        /*'codigo_mf_numero' => -1,
        'codigo_mf_texto' => 'Error al ejecutar modulo'*/
    );

    if($respuesta_sdk['codigo_mf_numero'] == 0)
    {
        // Nombre del modulo a usar
        $datos_pdf['modulo']='cfdi2pdf';

        // Ruta del XML a convertir (preferentemente ruta completa)
        $datos_pdf['rutaxml']=$respuesta_sdk['archivo_xml'];

        // Ruta donde se guardara el PDF (preferentemente ruta completa)
        // El directorio ya debe existir, de lo contrario no se guardara el PDF
        if(isset($datos['timbrar_pdf_enviar']['pdf']['archivo_pdf']))
        {
            $datos_pdf['archivo_pdf']=$datos['timbrar_pdf_enviar']['pdf']['archivo_pdf'];
        }
        else
        {
            $respuesta_modulo['codigo_mf_numero'] = 1;
            $respuesta_modulo['codigo_mf_texto'] = 'Especifique la ruta del pdf';
            return $respuesta_modulo;
        }

        // Titulo de la factura
        if (isset($datos['timbrar_pdf_enviar']['pdf']['titulo']))
        {
            $datos_pdf['titulo']=$datos['timbrar_pdf_enviar']['pdf']['titulo'];
        }

        // Tipo de factura (Venta, Nomina, Arrendamiento, etc)
        if (isset($datos['timbrar_pdf_enviar']['pdf']['tipo']))
        {
            $datos_pdf['tipo']=$datos['timbrar_pdf_enviar']['pdf']['tipo'];
        }

        // Ruta del logo de la empresa(Opcional, preferentemente ruta completa)
        if (isset($datos['timbrar_pdf_enviar']['pdf']['path_logo']))
        {
            $datos_pdf['path_logo']=$datos['timbrar_pdf_enviar']['pdf']['path_logo'];
        }

        // Para agregar notas al PDF
        if (isset($datos['timbrar_pdf_enviar']['pdf']['notas']))
        {
            $datos_pdf['notas']=$datos['timbrar_pdf_enviar']['pdf']['notas'];
        }

        // Color del marco de la factura
        if (isset($datos['timbrar_pdf_enviar']['pdf']['color_marco']))
        {
            $datos_pdf['color_marco']=$datos['timbrar_pdf_enviar']['pdf']['color_marco'];
        }

        // Color del texto del marco de la factura
        if (isset($datos['timbrar_pdf_enviar']['pdf']['color_marco_texto']))
        {
            $datos_pdf['color_marco_texto']=$datos['timbrar_pdf_enviar']['pdf']['color_marco_texto'];
        }

        // Color del texto en general
        if (isset($datos['timbrar_pdf_enviar']['pdf']['color_texto']))
        {
            $datos_pdf['color_texto']=$datos['timbrar_pdf_enviar']['pdf']['color_texto'];
        }

        // Fuente del texto en general
        if (isset($datos['timbrar_pdf_enviar']['pdf']['fuente_texto']))
        {
            $datos_pdf['fuente_texto']=$datos['timbrar_pdf_enviar']['pdf']['fuente_texto'];
        }
        
        // Se ejecuta el modulo
        //$datos_pdf['PAC'] = $datos['PAC'];
        $respuesta_pdf = mf_ejecuta_modulo($datos_pdf);
        $respuesta_modulo['cfdi2pdf'] = $respuesta_pdf;

        if($respuesta_pdf['codigo_mf_numero'] != 0)
        {
            return $respuesta_modulo;
        }
        else
        {
            $datos_correo['PAC'] = $datos['PAC'];
            $datos_correo['modulo'] = 'email';

            // Configuracion servidor de correo
            $datos_correo['host'] = $datos['timbrar_pdf_enviar']['correo']['host'];
            $datos_correo['puerto'] = $datos['timbrar_pdf_enviar']['correo']['puerto'];

            // Credenciales
            $datos_correo['usuario'] = $datos['timbrar_pdf_enviar']['correo']['usuario'];
            $datos_correo['clave'] = $datos['timbrar_pdf_enviar']['correo']['clave'];

            // Datos del emisor
            $datos_correo['remitente'] = $datos['timbrar_pdf_enviar']['correo']['remitente'];
            //$datos_correo['emisor_nombre'] = $datos['timbrar_pdf_enviar']['correo']['emisor_nombre'];

            // Receptores
            foreach($datos['timbrar_pdf_enviar']['correo']['receptores'] as $idx => $receptor)
            {
                $datos_correo['receptores'][] = $receptor;
            }
            
            // Archivos Adjuntos
            /*foreach($datos['timbrar_pdf_enviar']['correo']['adjuntos'] as $idx => $adjunto)
            {
                $datos_correo['adjuntos'][] = $adjunto;
            }*/
            $ruta_xml=($respuesta_sdk['archivo_xml']);
            $ruta_pdf=($datos['timbrar_pdf_enviar']['pdf']['archivo_pdf']);
            $datos_correo['adjuntos'][] = $ruta_xml;
            $datos_correo['adjuntos'][] = $ruta_pdf;

            // Contenido del mensaje
            $datos_correo['asunto'] = $datos['timbrar_pdf_enviar']['correo']['asunto'];
            
            // Indica si el mensaje contiene codigo html
            $datos_correo['mensaje_html'] = true;
            
            // Mensaje original (puede tener html)
            $datos_correo['cuerpo_html'] = $datos['timbrar_pdf_enviar']['correo']['cuerpo_html'];
            
            // Opcional, para clientes de correo que no soportan HTML
            $datos_correo['cuerpo_plano'] = $datos['timbrar_pdf_enviar']['correo']['cuerpo_plano'];

            // Se ejecuta el modulo de correo
            $respuesta_correo = mf_ejecuta_modulo($datos_correo);
            $respuesta_modulo['email'] = $respuesta_correo;
            return $respuesta_modulo;
        }
    }
}