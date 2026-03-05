<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Amazon PA-API Client
|--------------------------------------------------------------------------
|
| Minimal client for performing a GetItems request against the Amazon
| Product Advertising API (PA-API). This file focuses on signing the
| request using AWS Signature Version 4 and performing the request.
|
| The goal here is to keep the implementation small and readable for
| an experimental plugin.
|
*/


/*
|--------------------------------------------------------------------------
| Public helper: fetch price for ASIN
|--------------------------------------------------------------------------
*/

function amzpu_fetch_price_for_asin($asin)
{
    $creds = amzpu_get_credentials();

    if (!$creds['access_key'] || !$creds['secret_key'] || !$creds['partner_tag']) {
        return null;
    }

    $host   = 'webservices.amazon.com';
    $region = 'us-east-1';
    $path   = '/paapi5/getitems';

    $payload = json_encode([
        "ItemIds" => [$asin],
        "ItemIdType" => "ASIN",
        "Marketplace" => "www.amazon.com",
        "PartnerTag" => $creds['partner_tag'],
        "PartnerType" => "Associates",
        "Resources" => [
            "Offers.Listings.Price"
        ]
    ]);

    $amz_date = gmdate('Ymd\THis\Z');

    $headers = [
        'content-encoding' => 'amz-1.0',
        'content-type'     => 'application/json; charset=utf-8',
        'host'             => $host,
        'x-amz-date'       => $amz_date,
        'x-amz-target'     => 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.GetItems'
    ];

    $authorization = amzpu_sign_request(
        'POST',
        $path,
        $headers,
        $payload,
        $region,
        'ProductAdvertisingAPI',
        $creds['access_key'],
        $creds['secret_key'],
        $amz_date
    );

    $headers['Authorization'] = $authorization;

    $response = wp_remote_post(
        'https://' . $host . $path,
        [
            'headers' => $headers,
            'body' => $payload,
            'timeout' => 10
        ]
    );

    if (is_wp_error($response)) {
        return null;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['ItemsResult']['Items'][0]['Offers']['Listings'][0]['Price']['DisplayAmount'])) {
        return null;
    }

    return $body['ItemsResult']['Items'][0]['Offers']['Listings'][0]['Price']['DisplayAmount'];
}


/*
|--------------------------------------------------------------------------
| AWS Signature Version 4
|--------------------------------------------------------------------------
*/

function amzpu_sign_request($method, $path, $headers, $payload, $region, $service, $access_key, $secret_key, $amz_date)
{
    ksort($headers);

    $canonical_headers = '';
    $signed_headers = [];

    foreach ($headers as $k => $v) {
        $k = strtolower($k);
        $canonical_headers .= $k . ':' . trim($v) . "\n";
        $signed_headers[] = $k;
    }

    $signed_headers = implode(';', $signed_headers);
    $payload_hash = hash('sha256', $payload);

    $canonical_request = implode("\n", [
        $method,
        $path,
        '',
        $canonical_headers,
        $signed_headers,
        $payload_hash
    ]);

    $algorithm = 'AWS4-HMAC-SHA256';
    $datestamp = substr($amz_date, 0, 8);
    $credential_scope = "$datestamp/$region/$service/aws4_request";

    $string_to_sign = implode("\n", [
        $algorithm,
        $amz_date,
        $credential_scope,
        hash('sha256', $canonical_request)
    ]);

    $kDate = hash_hmac('sha256', $datestamp, 'AWS4' . $secret_key, true);
    $kRegion = hash_hmac('sha256', $region, $kDate, true);
    $kService = hash_hmac('sha256', $service, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

    $signature = hash_hmac('sha256', $string_to_sign, $kSigning);

    return $algorithm
        . ' Credential=' . $access_key . '/' . $credential_scope
        . ', SignedHeaders=' . $signed_headers
        . ', Signature=' . $signature;
}
