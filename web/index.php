<?php

require('../vendor/autoload.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use mikehaertl\wkhtmlto\Pdf;

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));

// Our web handlers

$app->get('/', function(Request $request) use($app) {
    $url = $request->get('url');
    $stream = function () use ($url) {
        $filename = preg_replace("/[^a-zA-Z0-9]+/", "-", $url).".pdf";
        $pdf = new Pdf([
            //replace the binary to the right binary
            'binary' => '../vendor/profburial/wkhtmltopdf-binaries-trusty/bin/wkhtmltopdf-linux-trusty-amd64'
        ]);
        $pdf->addPage($url);
        $pdf->send($filename);
    };
    
    return $app->stream($stream, 200);
});

$app->run();

