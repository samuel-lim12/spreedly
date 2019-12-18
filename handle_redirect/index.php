<?php

    $transactionToken = $_GET['transaction_token'];

    echo("Transaction detail : $transactionToken<br><br>");

    $spreedlyEnvironmentKey = 'WVN8WL3u6Ns9H0ugU8UOw0rQu8t';
    $spreedlyApiAccessSecret = '6kUobnn4Y4FnE7T5OTN5EAnli9qybuJBS1DwxXf9zsBkrWmXKKlFvv4SRoyF4w3n';

    $ch = curl_init("https://core.spreedly.com/v1/transactions/$transactionToken.json");
    $header = array();
    $header[] = 'Content-type: application/json';
    $header[] = 'Content-Length: 0';
    $header[] = 'Authorization: Basic ' . base64_encode("$spreedlyEnvironmentKey:$spreedlyApiAccessSecret");

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, false);

    $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response
        ); 

    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $jsonResult = json_decode($result, true);
    curl_close($ch);

    var_dump($jsonResult);

    if (isset($jsonResult['transaction']['required_action']))
    {
        $requiredAction = $jsonResult['transaction']['required_action'];
    }
    else
    {
        $requiredAction = 'none';
    }

    echo("<br><br>Transaction Token : $transactionToken");
    echo('<br>Transaction State : ' . $jsonResult['transaction']['state']);
    echo('<br>Response Message : ' . $jsonResult['transaction']['response']['message']);
    echo("<br>Required Action : $requiredAction <br>");
