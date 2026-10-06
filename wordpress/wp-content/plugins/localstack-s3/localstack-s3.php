<?php

/**
 * Plugin Name: LocalStack S3 Integration
 * Description: Envia cópias dos uploads do WordPress para um bucket S3 no LocalStack.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'vendor/autoload.php';

use Aws\S3\S3Client;

function localstack_s3_client() {
    return new S3Client([
        'version' => 'latest',
        'region'  => getenv('AWS_DEFAULT_REGION') ?: 'us-east-1',

        'endpoint' => getenv('S3_ENDPOINT') ?: 'http://localstack:4566',

        'credentials' => [
            'key'    => getenv('AWS_ACCESS_KEY_ID') ?: 'test',
            'secret' => getenv('AWS_SECRET_ACCESS_KEY') ?: 'test',
        ],

        'use_path_style_endpoint' => true,
    ]);
}

function localstack_s3_upload($upload) {

    if (isset($upload['error']) && $upload['error']) {
        return $upload;
    }

    $bucket = getenv('S3_BUCKET');

    if (!$bucket) {
        error_log('LocalStack S3: S3_BUCKET não configurado.');
        return $upload;
    }

    try {

        $client = localstack_s3_client();

        $filename = basename($upload['file']);

        $key = 'uploads/' . date('Y/m/') . $filename;

        $client->putObject([
            'Bucket'      => $bucket,
            'Key'         => $key,
            'SourceFile'  => $upload['file'],
            'ContentType' => $upload['type'],
        ]);

        error_log(
            'LocalStack S3: upload realizado com sucesso: ' . $key
        );

    } catch (Throwable $e) {

        error_log(
            'LocalStack S3 ERROR: ' . $e->getMessage()
        );
    }

    return $upload;
}

add_filter(
    'wp_handle_upload',
    'localstack_s3_upload'
);
