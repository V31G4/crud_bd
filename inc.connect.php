<?php

function executeCypherQuery($query)
{
    $url = 'http://localhost:7474/db/neo4j/tx/commit';
    $user = 'neo4j';
    $pass = 'senhacamille';

    $data = [
        'statements' => [
            [
                'statement' => $query,
                'resultDataContents' => ['row'],
                'includeStats' => true,
            ],
        ],
    ];

    $headers = [
        'Accept: application/json;charset=UTF-8',
        'Content-Type: application/json',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
    curl_setopt($ch, CURLOPT_FAILONERROR, true); 
    
    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        echo 'Erro cURL: ' . curl_error($ch);
    }
    curl_close($ch);

    return $response;
}

?>