<?php


namespace Sisow\Requests;


use Sisow\Exceptions\SisowException;
use Sisow\Responses\Invoice;

class Invoices extends AbstractRequest
{
    /**
     * @param $transactionId
     * @return Invoice
     * @throws SisowException
     */
    function create($transactionId){
        $response = $this->execute('InvoiceRequest', [
            'trxid' => $transactionId,
            'merchantid' => $this->getMerchantId(),
            'sha1' => sha1($transactionId . $this->getMerchantId() . $this->getMerchantKey())
        ]);

        // get params
        $invoiceNo = $response->getValue('invoiceno');
        $documentId = $response->getValue('documentid');
        $sha1 = $response->getValue('sha1');

        if($sha1 != sha1($invoiceNo . $documentId . $this->getMerchantId() . $this->getMerchantKey())){
            throw new SisowException('Invalid response!');
        }

        $invoiceResponse = new Invoice();
        $invoiceResponse->documentId = $documentId;
        $invoiceResponse->invoiceNo = $invoiceNo;
        return $invoiceResponse;
    }

    /**
     * @param $transactionId
     * @return Invoice
     * @throws SisowException
     */
    function credit($transactionId){
        $response = $this->execute('CreditInvoiceRequest', [
            'trxid' => $transactionId,
            'merchantid' => $this->getMerchantId(),
            'sha1' => sha1($transactionId . $this->getMerchantId() . $this->getMerchantKey())
        ]);

        // get params
        $invoiceNo = $response->getValue('invoiceno');
        $documentId = $response->getValue('documentid');
        $sha1 = $response->getValue('sha1');

        if($sha1 != sha1($invoiceNo . $documentId . $this->getMerchantId() . $this->getMerchantKey())){
            throw new SisowException('Invalid response!');
        }

        $invoiceResponse = new Invoice();
        $invoiceResponse->documentId = $documentId;
        $invoiceResponse->invoiceNo = $invoiceNo;
        return $invoiceResponse;
    }
}