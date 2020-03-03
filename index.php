<?php
require __DIR__ . "/vendor/autoload.php";
// assuming args from json body
use Parser\FonbetParser;

$json = file_get_contents('php://input');
$data = json_decode($json);

if (!isset($data->title)) {
    die(json_encode(['status' => 400, 'msg' => "Title wasn't provided in query"]));
}
$title = $data->title;

$coeffs = [];
if (!isset($data->coeffs)) {
    $coeffs[] = 1;
} else {
    $coeffs = $data->coeffs;
}

$parser = new FonbetParser();
$parser->createFirefoxRemoteDriver();
$result = $parser->searchMatch($title, $coeffs);
if ($result) {
    $result = (object) $result;
    $msg = date('Y-m-d H:i:s')." - ".$result->title." : ".array_reduce(
        array_keys($result->coeffs),
        function ($carry, $key) use ($result) {
            return $carry . ' ' . $key . ' - ' .$result->coeffs[$key]. ',';
        },
        ''
    );
    die(json_encode(['status' => 200, 'msg' => $msg, 'data' => $result]));
}
die(json_encode(['status' => 200, 'msg' => "Not found"]));
