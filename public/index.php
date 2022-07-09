<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/api', function (Request $req, Response $res, $args) {    
    $composerJsonFile = file_get_contents('../composer.json');
    $composerJsonContent = json_decode($composerJsonFile, true);

    $res->getBody()->write(json_encode([
        'version' => $composerJsonContent['version'],
    ]));

    return $res
              ->withHeader('Content-Type', 'application/json')
              ->withStatus(200);
              
});

$app->get('/jsontoxml', function (Request $req, Response $res, $args) {
    $jsonDataUrl = $req->getQueryParams()['jsonDataUrl'];

    $jsonDataStr = file_get_contents($jsonDataUrl);
    $obj = json_decode($jsonDataStr, true);    

    $xml = new SimpleXMLElement('<root/>');
    to_xml($xml, $obj);
    
    $res->getBody()->write($xml->asXML());
    return $res
              ->withHeader('Content-Type', 'application/xml')
              ->withStatus(200);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();

function to_xml(SimpleXMLElement $object, array $data)
{   
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $new_object = $object->addChild($key);
            to_xml($new_object, $value);
        } else {
            if ($key != 0 && $key == (int) $key) {
                $key = "key_$key";
            }
            $object->addChild($key, $value);
        }   
    }   
}  