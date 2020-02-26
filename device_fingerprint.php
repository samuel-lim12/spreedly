<?php
require_once __DIR__ . '/vendor/autoload.php';
?>
<html>
<head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <script language="JavaScript" type="text/javascript" src="https://core.spreedly.com/iframe/iframe-v1.min.js"></script>
    
    <script src="https://core.spreedly.com/iframe/express-2.min.js"></script>

</head>

<body>

<script type="text/javascript">

    $(document).ready(function(){

    Spreedly.init("DDZilKGbt6ptLWMguCNi4UXQHi7", {
        "numberEl": "spreedly-number",
        "cvvEl": "spreedly-cvv"
    });

    var lifecycle = new Spreedly.ThreeDS.Lifecycle({
          hiddenIframeLocation: 'device-fingerprint',
          // The DOM node that you'd like to inject hidden iframes
          challengeIframeLocation: 'challenge', 
          // The DOM node that you'd like to inject the challenge flow
          transactionToken: '<?php echo($_POST['transaction_token']); ?>', 
          environmentKey: 'DDZilKGbt6ptLWMguCNi4UXQHi7'
          // The token for the transaction - used to poll for state
          // challengeIframeClasses: '...', (optional)
          // The css classes that you'd like to apply to the challenge iframe.
          //
          // Note: This is where you'll change the height and width of the challenge
          //       iframe. You'll need to match the height and width option that you
          //       selected when collecting browser data with `Spreedly.ThreeDS.serialize`.
          //       For instance if you selected '04' for browserSize you'll need to have a
              //       CSS class that has width and height of 600px by 400px.
    });

    var statusUpdates = function(event) {
      if (event.action === 'succeeded') {
        alert('succeed');
      } else if (event.action === 'error') {
        alert('error');
      } else if (event.action === 'challenge') {
        // Show your modal containing the div provided in `challengeIframeLocation` when
        // creating the lifecycle event.
        //
        // Example HTML on your page:
        //
        // <head>
        //   <style>
        //     .hidden {
        //       display: none;
        //     }
        //     
        //     #challenge-modal {
        //       <!-- style your modal here -->
        //     }
        //   </style>
        // </head>
        // <body>
        //   <div id="device-fingerprint" class="hidden">
        //     <!-- Spreedly injects content into this div,
        //          do not nest the challenge div inside of it -->
        //   </div>
        //   <div id="challenge-modal" class="hidden">
        //     <div id="challenge">
        //     </div>
        //   </div>
        // </body>
        //
        //  Example lifecycle object from step 6:
        //
        //  var lifecycle = new Spreedly.ThreeDS.Lifecycle({
        //    hiddenIframeLocation: 'device-fingerprint',
        //    challengeIframeLocation: 'challenge',
        //    ...
        //  })
        //
        //  and then we show the challenge-modal
        //
        alert('challenge');
        document.getElementById('challenge-modal').classList.remove('hidden');
      } else if (event.action === 'trigger-completion') {
        // 1. make a request to your backend to do an authenticated call to Spreedly to complete the request
        //    The completion call is `https://core.spreedly.com/v1/transactions/[transaction-token]/complete.json (or .xml)`
        // 2a. if the transaction is marked as "succeeded" finish your checkout and redirect to success page
        // 2b. if the transaction is marked "pending" you'll need to call finalize `event.finalize(transaction)` with the transaction data from the authenticated completion call.

        // This is an example of the authenticated call that you'd make
        // to your service.
        alert("trigger completion");
        window.location.href = "./trigger_completion.php?transaction_token=<?php echo($_POST['transaction_token']); ?>"
        // fetch(`https://your-service/complete/${purchaseToken}.json`, { method: 'POST' })
        //   .then(response => response.json())
        //   .then((data) => {
        //     if (data.state === 'succeeded') {
        //         alert("fetch succeeded");
        //       // finish your checkout and redirect to success page
        //     }

        //     if (data.state === 'pending') {
        //         alert("fetch pending");
        //       event.finalize(data);
        //     }
        // })
          
      }
    };

    Spreedly.on('3ds:status', statusUpdates);

    var transactionData = {
      state: "pending",
      // The current state of the transaction. 'pending', 'succeeded', etc
      required_action: "device_fingerprint",
      // The next action to be performed in the 3D Secure workflow
      device_fingerprint_form: {
            "cdata": '<?php echo(trim(urldecode($_POST['device_fingerprint_form']))); ?>'
        },
      // Available when the required_action is on the device fingerprint step
      checkout_form: null,
      // Available when the required_action is on the 3D Secure 1.0 fallback step
      checkout_url: null,
      // Available when the required_action is on the 3D Secure 1.0 fallback step
      challenge_form: null,
      // The challenge form that is injected when the user is challenged
      challenge_url: null,
      // The challenge url that is loaded when there is no challenge form
    };

  lifecycle.start(transactionData);

});

</script>

<p>3DS Lifecycle, please wait 10 - 30 seconds until an alert pops up</p>
<div id="spreedly-number"></div>
<div id="spreedly-cvv"></div>
<div id="device-fingerprint"></div>
<div id="challenge"></div>

</body>
</html>
