<?php

function ___email($datos)
{
    require_once __DIR__ . '/mailer/Mailer.php';
    spl_autoload_register('cargar_librerias');

    $mailer = new \Tx\Mailer();

    $mailer->setServer('smtp.gmail.com', 587);
    $mailer->setAuth('mashtersoporte@gmail.com', 'mash9900');

    $mailer->setFrom('Modulo de Coreo', 'mashtersoporte@gmail.com');
    $mailer->addTo('Ventas MultiFacturas','mashterventas@gmail.com');

    $mailer->setSubject('Asunto');
    $mailer->setBody('Prueba de envio de correo: ' . date('Y-m-d H:i:s'));

    $resp = $mailer->send();

    var_dump($resp);
}

function cargar_librerias($clase)
{
    $path = __DIR__ . '/mailer/' . $clase . '.php';
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
    require_once $path;
}