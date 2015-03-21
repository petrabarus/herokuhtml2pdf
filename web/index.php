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
    $title = $request->get('title');
    $queries = $request->query->all();
    //assume there is no get using url and title
    unset($queries['url']);
    unset($queries['title']);
    $url = $url . '&' . http_build_query($queries);
    if (!empty($url)) {
        $stream = function () use ($url, $title) {
            if (empty($title)){
                $title = $url;
            }
            $filename = preg_replace("/[^a-zA-Z0-9]+/", "-", $title).".pdf";
            $pdf = new Pdf([
                //replace the binary to the right binary
                'binary' => '../vendor/profburial/wkhtmltopdf-binaries-trusty/bin/wkhtmltopdf-linux-trusty-amd64',
                
                'no-outline',         // Make Chrome not complain
                'no-stop-slow-scripts',
                'margin-top'    => 0,
                'margin-right'  => 0,
                'margin-bottom' => 0,
                'margin-left'   => 0,
                'disable-smart-shrinking',
            ]);
            $pdf->addPage($url);
            $pdf->send($filename);
        };
        return $app->stream($stream, 200);
    } else {
        return "<h1>Hello World!</h1>";
    }
});

$app->run();

