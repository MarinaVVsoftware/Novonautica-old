<?php

function elimina_ampersand($texto)
{
	// Se corrigen los ampersand
	$matches = array();
	$rr = preg_match('/&[^amp;]/', $texto, $matches, PREG_OFFSET_CAPTURE);
	
	// Si se encontraron incidencias
	if($rr !== false)
	{
		foreach($matches as $match)
		{
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
	
    return $datos;
}