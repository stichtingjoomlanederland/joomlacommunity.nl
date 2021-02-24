<?php

function acym_sendAjaxResponse($message = '', $data = [], $success = true)
{
    $response = [
        'message' => $message,
        'data' => $data,
        'error' => !$success,
    ];

    $document = acym_getGlobal('doc');

    $document->setMimeEncoding('application/json');

    echo json_encode($response);
    exit;
}
