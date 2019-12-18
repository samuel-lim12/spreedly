<?php

echo('Payment Method Token : ' . $_GET['payment_method_token'] . '<br>');
echo('Browser Info : ' . $_GET['browser_info'] . '<br>');

?>
<br>
<div>
    <pre>
Amount in cents     Result
3001    3D Secure 2 full frictionless flow (immediate transaction flow)
3002    Fallback from 3DS2 to 3DS1
3003    3D Secure device fingerprint flow with direct authorize (requires lifecycle)
3004    3D Secure device fingerprint flow to challenge (requires lifecycle and completion call)
3005    3D Secure direct challenge (requires lifecycle and completion call)
3103    3D Secure device fingerprint flow with forced failure
3104    3D Secure challenge flow with forced failure
    </pre>
    <form method=post action="purchase.php">
        <input type="hidden" name="payment_method_token" value="<?php echo($_GET['payment_method_token']); ?>">
        <input type="hidden" name="browser_info" value="<?php echo($_GET['browser_info']); ?>">

        <label for="amount">Purchase Amount in cents</label>
        <input type="text" name="amount">
        <br>
        <input type="submit" value="Purchase">
    </form>
</div>