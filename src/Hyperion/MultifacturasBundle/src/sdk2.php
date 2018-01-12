<?php

function mf_phpversion()
{
    $version = phpversion();
    $numversion = '';
    for($i = 0, $punto = 0; $i < strlen($version); $i++)
    {
        if($version[$i] == '.')
        {
            if($punto == 0)
            {
                $numversion .= $version[$i];
                $punto++;
            }
        }else
        {
            $numversion .= $version[$i];
        }
    }
    return doubleval($numversion);
}

$php_version = mf_phpversion();
if($php_version < 5.3)
{
	echo "La version '$php_version' no es compatible";die();
}

if($php_version >= 5.3 && $php_version < 7.1)
{
	if($php_version >= 5.6)
	{
		require_once 'sdk27.php';
	}
	else
	{
		require_once 'sdk25.php';
	}
}
else
{
    if($php_version >= 7.1)
    {
        require_once 'sdk271.php';
    }   
    else
    {
        echo "La version '$php_version' no es compatible";die();
    } 
}

