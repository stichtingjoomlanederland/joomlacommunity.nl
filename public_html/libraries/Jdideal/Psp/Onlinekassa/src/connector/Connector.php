<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;

/**
 * This interface describes the abstract calls you can make to the Rabobank OmniKassa.
 */
interface Connector
{
    /**
     * Announce an order.
     *
     * @param MerchantOrderRequest $order
     *
     * @return string json response body
     */
    public function announceMerchantOrder(MerchantOrderRequest $order);

    /**
     * Retrieve the order details from an announcement.
     *
     * @param AnnouncementResponse $announcement
     *
     * @return string json response body
     */
    public function getAnnouncementData(AnnouncementResponse $announcement);
}
