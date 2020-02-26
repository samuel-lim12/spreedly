<?php
require_once __DIR__ . '/vendor/autoload.php';
    $payload = json_encode([
      'transaction' => [
        'payment_method_token' => $_POST['payment_method_token'],
        'amount' => $_POST['amount'],
        'currency_code' => 'USD',
        'retain_on_success' => true,
        'redirect_url' => 'http://127.0.0.1/spreedly/handle_redirect',
        'callback_url' => 'https://vendo-images.sgp1.digitaloceanspaces.com/spreedly/callback.php',    
        'three_ds_version' => 2,
        'attempt_3dsecure' => true,
        'browser_info' => $_POST['browser_info']
      ]
    ]);
    dump($payload);
    $spreedlyGatewayToken = 'KGciMycydHw6TwNxoRO9wFvPvWN';
    $spreedlyEnvironmentKey = 'DDZilKGbt6ptLWMguCNi4UXQHi7';
    $spreedlyApiAccessSecret = 'n2prFH4qozF6s2S71uvY4fwDANrpG9irkcNVU2YgctkBxG7o7IeFbrv2j0Satapp';

    $ch = curl_init("https://core.spreedly.com/v1/gateways/$spreedlyGatewayToken/purchase.json");
    $header = array();
    $header[] = 'Content-type: application/json';
    $header[] = 'Content-Length: ' . strlen($payload);
    $header[] = 'Authorization: Basic ' . base64_encode("$spreedlyEnvironmentKey:$spreedlyApiAccessSecret");

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

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
    print_r($jsonResult);
    // dd($jsonResult);

    $transactionToken = $jsonResult['transaction']['token'];

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
    echo("<br>Required Action : $requiredAction");
    dump($jsonResult);

    if ($requiredAction == 'device_fingerprint') :
        $deviceFingerprintForm = $jsonResult['transaction']['device_fingerprint_form']['cdata'];
        $deviceFingerprintForm = str_replace(array("\n","\r"), '', $deviceFingerprintForm);
        $deviceFingerprintForm = urlencode($deviceFingerprintForm);
    ?>
        
        <form method="post" action="device_fingerprint.php">
            <input type="hidden" name="transaction_token" value="<?php echo($transactionToken); ?>">
            <input type="hidden" name="device_fingerprint_form" value="<?php echo($deviceFingerprintForm); ?>">
            <input type="submit" value="Trigger Device Fingerprint">
        </form>

    <?php
    elseif ($requiredAction == 'challenge') :
        echo('<a href="' . $jsonResult['transaction']['challenge_url'] . '" target="_blank">Go to challenge page</a><br>');
        echo("<a href='./transaction_detail.php?transaction_token=$transactionToken'>After click ALLOW in challenge page, click here to see transaction status</a>");

    elseif ($requiredAction == 'redirect') :
        echo('<br><a href="' . $jsonResult['transaction']['checkout_url'] . '" target="_blank">Go to checkout page</a><br>');
        echo("<a href='./transaction_detail.php?transaction_token=$transactionToken'>After click 'Successfully Authenticate' button in checkout page, click here to see transaction status</a>");
    
    endif;