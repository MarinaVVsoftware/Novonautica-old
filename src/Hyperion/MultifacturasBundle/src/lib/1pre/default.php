<?php

function elimina_ampersand($texto)
{
	// Se corrigen los ampersand
	$matches = array();
	$rr = preg_match('/&[^amp;]/', $texto, $matches, PREG_OFFSET_CAPTURE);
	
	// Si se encontraron incidencias
	if($rr !== false)
	{
		//var_dump($matches);
		foreach($matches as $match)
		{
			//var_dump($match);
			$pos = $match[1];
			$aux = '';
			for($i = 0; $i < strlen($texto); $i++)
			{
				if($i == $pos)
				{
					$aux .= '&amp;';
				}
				else
				{
					$aux .= $texto[$i];
				}
			}
			return $aux;
		}
	}
	return $texto;
}

function mf_default(&$datos)
{
    // Retencion por defecto en NO
    if(!isset($datos['retencion']))
    {
        $datos['retencion']='NO';
    }

    // Modo externo por defecto en SI
    if(!isset($datos['modo_externo']))
    {
        $datos['modo_externo'] = 'SI';
    }
	
	// Se eliminan ampersand
	if(isset($datos['emisor']['rfc']))
	{
		$datos['emisor']['rfc'] = elimina_ampersand($datos['emisor']['rfc']);
	}
	if(isset($datos['receptor']['rfc']))
	{
		$datos['receptor']['rfc'] = elimina_ampersand($datos['receptor']['rfc']);
	}
	
	
	// Ajuste de nuevo RFC de pruebas
	/*if(isset($datos['emisor']['rfc']) && $datos['emisor']['rfc'] == 'AAA010101AAA')
	{
		// Se cambia el RFC
		$datos['emisor']['rfc'] = 'LAN7008173R5';
		
		if(strpos(strtolower($datos['conf']['cer']), 'certificados/aaa010101aaa') !== false)
		{
			$datos['conf'] = array(
				'cer' => '../../certificados/lan7008173r5.cer.pem',
				'key' => '../../certificados/lan7008173r5.key.pem',
				'pass' => '12345678a'
			);
		}
		
		if(strpos(strtolower($datos['conf']['cer']), 'certificados\aaa010101aaa') !== false)
		{
			$datos['conf']['cer'] = str_replace('certificados\aaa010101aaa', 'certificados\lan7008173r5');
			$datos['conf']['key'] = str_replace('certificados\aaa010101aaa', 'certificados\lan7008173r5');
			$datos['pass'] = '12345678a';
		}
	}*/
	
	global $__mf_constantes__;

    // Se verifica la version
    switch ($__mf_constantes__['__MF_VERSION_CFDI__'])
	{
		case '3.2':
			// Complemento Nomina
			if($datos['modonomina'] == 'SI')
			{
				$datos['complemento'] = 'nomina12';
				
				$nomina = $datos['nomina'];
				
				$datos['nomina12'] = $nomina['datos'];
				unset($nomina['datos']);
				unset($datos['nomina']);
				$datos['nomina12'] = array_merge($datos['nomina12'], $nomina);
				
				if(isset($datos['nomina12']['emisor']))
				{
					$datos['nomina12']['Emisor'] = $datos['nomina12']['emisor'];
					unset($datos['nomina12']['emisor']);
				}
				
				if(isset($datos['nomina12']['receptor']))
				{
					$datos['nomina12']['Receptor'] = $datos['nomina12']['receptor'];
					unset($datos['nomina12']['receptor']);
				}
				
				if(isset($datos['nomina12']['percepciones']))
				{
					$datos['nomina12']['Percepciones'] = $datos['nomina12']['percepciones'];
					unset($datos['nomina12']['percepciones']);
				}
				
				if(isset($datos['nomina12']['deducciones']))
				{
					$datos['nomina12']['Deducciones'] = $datos['nomina12']['deducciones'];
					unset($datos['nomina12']['deducciones']);
				}
				
				if(isset($datos['nomina12']['otrospagos']))
				{
					$datos['nomina12']['OtrosPagos'] = $datos['nomina12']['otrospagos'];
					unset($datos['nomina12']['otrospagos']);
				}
				
				if(isset($datos['nomina12']['deducciones']))
				{
					$datos['nomina12']['Incapacidades'] = $datos['nomina12']['deducciones'];
					unset($datos['nomina12']['deducciones']);
				}
			}
			break;
		case '3.3':
			break;
	}
	
    return $datos;
}