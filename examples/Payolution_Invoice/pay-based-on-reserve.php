<?php
// # Payment after a reservation

// Enter the ID of the successful reserve transaction and start a pay transaction with it.

// ## Required objects

// To include the necessary files, we use the composer for PSR-4 autoloading.
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../inc/common.php';
require __DIR__ . '/../inc/globalconfig.php';
//Header design
require __DIR__ . '/../inc/header.php';

use Wirecard\PaymentSdk\Entity\Amount;
use Wirecard\PaymentSdk\Response\FailureResponse;
use Wirecard\PaymentSdk\Response\SuccessResponse;
use Wirecard\PaymentSdk\Transaction\PayolutionInvoiceTransaction;
use Wirecard\PaymentSdk\TransactionService;

if (!isset($_POST['parentTransactionId'])) {
    ?>
	<form action="pay-based-on-reserve.php" method="post">
		<div class="form-group">
			<label for="parentTransactionId">Transaction ID to capture:</label><br>
			<input id="parentTransactionId" name="parentTransactionId" class="form-control"/><br>
		</div>
		<button type="submit" class="btn btn-primary">Capture the payment</button>
	</form>
    <?php
} else {
// ### Transaction related objects

//Use the amount object as amount which has to be paid by the consumer.
    $amount = new Amount(500, 'EUR');

// As soon as the transaction status changes, a server-to-server notification will get delivered to this URL.
    $notificationUrl = getUrl('notify.php');

// ## Transaction

// The Payolution invoice transaction holds all transaction relevant data for the reserve process.
    $transaction = new PayolutionInvoiceTransaction();
    $transaction->setNotificationUrl($notificationUrl);
    $transaction->setAmount($amount);

    if (array_key_exists('parentTransactionId', $_POST)) {
        $parentTransactionId = $_POST['parentTransactionId'];
        $transaction->setParentTransactionId($_POST['parentTransactionId']);
    } else {
        $parentTransactionId = '';
    };

// ### Transaction service

// The _TransactionService_ is used to generate the request data needed for the generation of the UI.
    $transactionService = new TransactionService($config);
    $response = $transactionService->pay($transaction);

// ## Response handling

// The response from the service can be used for disambiguation.
// In case of a successful transaction, a `SuccessResponse` object is returned.
    if ($response instanceof SuccessResponse) {
        echo 'Payment successfully completed.<br>';
        echo getTransactionLink($baseUrl, $response);
        ?>
		<br>
		<form action="cancel.php" method="post">
			<input type="hidden" name="parentTransactionId" value="<?= $response->getTransactionId() ?>"/>
			<input type="hidden" name="transaction-type" value="<?= $response->getTransactionType() ?>"/>
            <?php
            if (array_key_exists('item_to_capture', $_POST)) {
                echo sprintf('<input type="hidden" name="amount" value="%0.2f"/>', $amount->getValue());
            }
            ?>
			<button type="submit" class="btn btn-primary">Cancel the capture</button>
		</form>
        <?php
// In case of a failed transaction, a `FailureResponse` object is returned.
    } elseif ($response instanceof FailureResponse) {
        // In our example we iterate over all errors and echo them out.
        // You should display them as error, warning or information based on the given severity.
        foreach ($response->getStatusCollection() as $status) {
            /**
             * @var $status \Wirecard\PaymentSdk\Entity\Status
             */
            $severity = ucfirst($status->getSeverity());
            $code = $status->getCode();
            $description = $status->getDescription();
            echo sprintf('%s with code %s and message "%s" occurred.<br>', $severity, $code, $description);
        }
    }
}
//Footer design
require __DIR__ . '/../inc/footer.php';
