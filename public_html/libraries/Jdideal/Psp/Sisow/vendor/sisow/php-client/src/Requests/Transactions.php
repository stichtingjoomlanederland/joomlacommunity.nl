<?php


namespace Sisow\Requests;

use Sisow\Exceptions\SisowException;
use Sisow\Responses\Transaction;

class Transactions extends AbstractRequest
{
    /**
     * @param $transactionId
     * @return Transaction
     * @throws SisowException
     */
    function get($transactionId){
        $response = $this->execute('StatusRequest', [
            'trxid' => $transactionId,
            'shopid' => $this->getShopId(),
            'merchantid' => $this->getMerchantId(),
            'sha1' => sha1($transactionId . $this->getShopId() . $this->getMerchantId() . $this->getMerchantKey())
        ]);

        // get params
        $trxId = $response->getValue('trxid');
        $status = $response->getValue('status');
        $amount = $response->getValue('amount');
        $description = $response->getValue('description');
        $purchaseId = $response->getValue('purchaseid');
        $ec = $response->getValue('entrancecode');
        $consumerAccount = $response->getValue('consumeraccount');
        $sha1 = $response->getValue('sha1');

        // validate response
        if($sha1 != sha1($trxId . $status . $amount . $purchaseId . $ec . $consumerAccount . $this->getMerchantId() . $this->getMerchantKey())){
            throw new SisowException('Invalid response!');
        }

        $transactionResponse = new Transaction();
        $transactionResponse->transactionId = $trxId;
        $transactionResponse->status = $status;
        $transactionResponse->amount = $amount;
        $transactionResponse->purchaseId = $purchaseId;
        $transactionResponse->description = $description;
        $transactionResponse->entranceCode = $ec;
        $transactionResponse->issuerId = $response->getValue('issuerid');
        $transactionResponse->timestamp = $response->getValue('timestamp');
        $transactionResponse->consumerName = $response->getValue('consumername');
        $transactionResponse->consumerAccount = $consumerAccount;
        $transactionResponse->consumerBic = $response->getValue('consumerbic');
        $transactionResponse->consumerCity = $response->getValue('consumercity');
        $transactionResponse->consumerIban = $response->getValue('consumeriban');

        return $transactionResponse;
    }

    /**
     * @param string[] $data
     * @return Transaction
     * @throws SisowException
     */
    function create($data){
        // do we have a purchase ID?
        if(!array_key_exists('purchaseid', $data) || empty($data['purchaseid'])){
            throw new SisowException('TransactionRequest: no purchase ID');
        }

        // do we have a entrance code?
        if(!array_key_exists('entrancecode', $data) || empty($data['entrancecode'])){
            $data['entrancecode'] = $data['purchaseid'];
        }

        // do we have an amount?
        if(!array_key_exists('amount', $data) || empty($data['amount'])){
            throw new SisowException('TransactionRequest: no amount');
        }

        // add merchant ID and sha1 to request
        $data['merchantid'] = $this->getMerchantId();
        $data['sha1'] = sha1($data['purchaseid'] . $data['entrancecode'] . $data['amount'] . $this->getShopId() . $this->getMerchantId() . $this->getMerchantKey());

        // do request
        $response = $this->execute('TransactionRequest', $data);

        // get values for sha1 calculation
        $trxId = $response->getValue('trxid');
        $issuerUrl = $response->getValue('issuerurl');
        $invoiceNo = $response->getValue('invoiceno');
        $documentId = $response->getValue('documentid');
        $documentUrl = $response->getValue('documenturl');
        $sha1 = $response->getValue('sha1');

        // validate response
        if(
            $sha1 != sha1($trxId . $issuerUrl . $this->getMerchantId() . $this->getMerchantKey()) &&
            $sha1 != sha1($trxId . $invoiceNo . $documentId . $this->getMerchantId() . $this->getMerchantKey()) &&
            $sha1 != sha1($trxId . $documentId . $this->getMerchantId() . $this->getMerchantKey())
        ){
            throw new SisowException('Invalid response!');
        }

        // generate result
	    $transactionResponse = new Transaction();

        if ($trxId)
        {
	        $transactionResponse = $this->get($trxId);
        }

	    $transactionResponse->documentId  = $documentId;
	    $transactionResponse->documentURL = $documentUrl;
	    $transactionResponse->issuerUrl   = urldecode($issuerUrl);
	    $transactionResponse->invoiceNo   = $invoiceNo;

        // return result
        return $transactionResponse;
    }

    /**
     * @param $transactionId
     * @param $oldPurchaseId
     * @param $newPurchaseId
     * @return Transaction
     * @throws SisowException
     */
    function edit($transactionId, $oldPurchaseId, $newPurchaseId){
        // get merchant info
        $merchantId = $this->getMerchantId();
        $merchantKey = $this->getMerchantKey();

        // make request to Sisow
        $xmlResult = $this->execute('AdjustPurchaseId', [
            'trxid' => $transactionId,
            'old' => $oldPurchaseId,
            'new' => $newPurchaseId,
            'merchantid' => $merchantId,
            'sha1' => sha1($transactionId . $oldPurchaseId . $newPurchaseId . $merchantId . $merchantKey)
        ]);

        // validate request
        if(empty($xmlResult->getValue('purchaseid')) || $xmlResult->getValue('sha1') != sha1($newPurchaseId . $merchantId . $merchantKey)){
            throw new SisowException('Invalid Sisow response');
        }

        return $this->get($transactionId);
    }

    /**
     * @param string $transactionId
     * @return Transaction
     * @throws SisowException
     */
    function cancel($transactionId){
        // validate transaction ID
        if(empty($transactionId)){
            throw new SisowException('No Transaction Id');
        }

        // load merchant information
        $merchantId = $this->getMerchantId();
        $merchantKey = $this->getMerchantKey();

        // do request
        $xmlResponse = $this->execute('CancelReservationRequest', [
            'trxid' => $transactionId,
            'merchantid' => $merchantId,
            'sha1' => sha1($transactionId . $merchantId . $merchantKey)
        ]);

        // validate response
        if($xmlResponse->getValue('status') != 'Cancelled' || $xmlResponse->getValue('sha1') != sha1($transactionId . $merchantId . $merchantKey)){
            throw new SisowException('Sisow invalid response');
        }

        return $this->get($transactionId);
    }

    /**
     * @param $transactionId
     * @param $amount
     * @return string
     * @throws SisowException
     */
    function refund($transactionId, $amount){
        if(empty($transactionId)){
            throw new SisowException('No Transaction ID');
        }

        if(empty($amount)){
            throw new SisowException('No amount');
        }

        // get merchant info
        $merchantId = $this->getMerchantId();
        $merchantKey = $this->getMerchantKey();

        // get response
        $xmlResponse = $this->execute('RefundRequest', [
            'trxid' => $transactionId,
            'amount' => $amount,
            'merchantid' => $merchantId,
            'sha1' => sha1($transactionId . $merchantId . $merchantKey)
        ]);

        // get refund ID
        $refundId = $xmlResponse->getValue('refundid');

        // validate response
        if(empty($refundId) || $xmlResponse->getValue('sha1') != sha1($refundId . $merchantId . $merchantKey)){
            throw new SisowException('Invalid Sisow response');
        }

        return $refundId;
    }
}
