<?php
$request = curl_init();

curl_setopt_array($request, [
    CURLOPT_URL =>  "https://rajaongkir.komerce.id/api/v1/destination/province",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "key: dx7NRPTG9ec958e6014cfa0dIGzLJEy1"
    ],
]);

$response = curl_exec($request);
$err = curl_error($request);

curl_close($request);

if ($err)   {
    echo"cURL error #:" . $err;
} else {
    echo $response;
}
?>