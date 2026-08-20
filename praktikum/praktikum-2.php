<?php
$request = curl_init();

curl_setopt_array($request, array (
    CURLOPT_URL =>  "https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost",
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "key: dx7NRPTG9ec958e6014cfa0dIGzLJEy1",
        "content-type: application/x-www-form-urlencoded",
    ),
    CURLOPT_POSTFIELDS => http_build_query([
        "origin"        => "12", // district id
        "destination"   => "80", // district id
        "weight"        => "1000",
        "courier"       => "jne", // salah satu
        "price"         => "lowest",
    ]),
    CURLOPT_RETURNTRANSFER => true,
));

$response   = curl_exec($request);
$error      = curl_error($request);

curl_close($request);

if ($error) {
    echo $error;
} else {
    echo $response;
}
?>