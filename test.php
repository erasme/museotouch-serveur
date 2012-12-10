<?php
require_once './content/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('cart_templates');
$twig = new Twig_Environment($loader, array(
    'cache' => false
));

echo $twig->render('1.html', array(
    'name' => 'Twig'
));
?>
