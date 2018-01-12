<?php

function mf_nodo_conceptos(array &$datosTodos)
{
	global $__mf_constantes__;
    $datos = $datosTodos['conceptos'];
    $subs='';
    foreach ($datos as $idx => $concepto)
    {
        $infoaduanera = '';
        $predial = '';
        $impuestos = '';
        $nodoparte = '';
        $nodoComplemento = '';
	
		// Impuestos
		if(isset($concepto['Impuestos']))
        {
            $nodoimpuestos = $concepto['Impuestos'];
            $atrsimpuestos = mf_atributos_nodo($nodoimpuestos, 'conceptos.Impuestos');
            $impuestos .= "<cfdi:Impuestos $atrsimpuestos>";

            if(isset($nodoimpuestos['Traslados']))
            {
                $traslados = $nodoimpuestos['Traslados'];
                $atrstraslados = mf_atributos_nodo($nodoimpuestos['Traslados'], 'conceptos.Traslados');
                $impuestos .= "<cfdi:Traslados $atrstraslados>";
                foreach ($traslados as $idxx => $traslado)
                {
                    $atrstraslado = mf_atributos_nodo($traslado, 'conceptos.Impuestos.Traslados');
                    $impuestos .= "<cfdi:Traslado $atrstraslado/>";
                }
                $impuestos .= "</cfdi:Traslados>";
            }
			
			if(isset($nodoimpuestos['Retenciones']))
            {
                $retenciones = $nodoimpuestos['Retenciones'];
                $atrsretenciones = mf_atributos_nodo($nodoimpuestos['Retenciones'], 'conceptos.Retenciones');
                $impuestos .= "<cfdi:Retenciones $atrsretenciones>";
                foreach ($retenciones as $idxx => $retencion)
                {
                    $atrsretencion = mf_atributos_nodo($retencion, 'conceptos.Impuestos.Retenciones');
                    $impuestos .= "<cfdi:Retencion $atrsretencion/>";
                }
                $impuestos .= "</cfdi:Retenciones>";
            }
			
            $impuestos .="</cfdi:Impuestos>";
        }
		
		// Informacion Aduanera 3.2
        if(isset($concepto['fecha']) || isset($concepto['aduana']) || isset($concepto['numero']))
        {
            $fecha = $concepto['fecha'];
            $aduana = $concepto['aduana'];
            $numero = $concepto['numero'];
            
            unset($concepto['fecha']);
            unset($concepto['numero']);
            unset($concepto['aduana']);

            // Opcional
            $fecha = explode('::', $fecha);
            // Obligatorio
            $aduana = explode('::', $aduana);
            // Obligatorio
            $numero = explode('::', $numero);

            for($idx = 0; $idx < count($numero); $idx++)
            {
                $a = " aduana='" . $aduana[$idx] . "'";
                $f = " fecha='" . $fecha[$idx] . "'";
                $n = " numero='" . $numero[$idx] . "'";

                $infoaduanera.="<cfdi:InformacionAduanera$n$f$a/>";
            }
        }
		//try{
			// Informacion Aduanera 3.3
			if(isset($concepto['InformacionAduanera']))
			{
				$nodoinfo = $concepto['InformacionAduanera'];
				
				foreach($nodoinfo as $infoaduana)
				{
					$atrsinfo = mf_atributos_nodo($infoaduana, 'conceptos.InformacionAduanera');
					$infoaduanera.="<cfdi:InformacionAduanera $atrsinfo/>";
				}
			}
		/*}
		catch(){}*/
		
		// Cuenta Predial
        if(isset($concepto['predial']))
        {
            $predial = $concepto['predial'];
            unset($concepto['predial']);
			if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.2')
			{
				$predial = "<cfdi:CuentaPredial numero='$predial'/>";
			}
			else
			{
				$predial = "<cfdi:CuentaPredial Numero='$predial'/>";
			}
            
        }

        // Complementos
        if(isset($datosTodos['complemento']))
        {
            switch($datosTodos['complemento'])
            {
                case 'iedu10':
                case 'ventavehiculos11':
                case 'terceros11':
                case 'acreditamientoieps10':
                    $nodoComplemento = '<cfdi:ComplementoConcepto>' . mf_carga_complemento($datosTodos['complemento'], $datosTodos[$datosTodos['complemento']]) . '</cfdi:ComplementoConcepto>';
                    // Se elimina para evitar
                    // 1. Que se agregue en los otros conceptos
                    // 2. Que se agregue tambien en nodo complemento
                    unset($datosTodos['complemento']);
					unset($datosTodos[$datosTodos['complemento']]);
                break;
            }
        }

		// Parte
        if(isset($concepto['Parte']))
        {
            $partes = $concepto['Parte'];

            foreach($partes as $idxx => $parte)
            {
                $atrsparte = mf_atributos_nodo($parte, 'conceptos.Parte');
                $nodoparte .= "<cfdi:Parte $atrsparte>";

                foreach ($parte['InformacionAduanera'] as $idxxx => $infoAduana)
                {
                    $atrsinfoaduanera = mf_atributos_nodo($parte['InformacionAduanera'][$idxxx], 'conceptos.InformacionAduanera');
                    $nodoparte .= "<cfdi:InformacionAduanera $atrsinfoaduanera />";
                }

                $nodoparte .= "</cfdi:Parte>";
            }


        }

        $subatrs = mf_atributos_nodo($concepto, 'conceptos');
        $sub = "<cfdi:Concepto $subatrs>$impuestos$infoaduanera$predial$nodoComplemento$nodoparte</cfdi:Concepto>";
        $subs .= $sub;
    }
    $conceptos = "<cfdi:Conceptos>$subs</cfdi:Conceptos>";
    return $conceptos;
}
