<?php
/**
 * Shop System SDK:
 * - Terms of Use can be found under:
 * https://github.com/wirecard/paymentSDK-php/blob/master/_TERMS_OF_USE
 * - License can be found under:
 * https://github.com/wirecard/paymentSDK-php/blob/master/LICENSE
 */
namespace WirecardExample\Helpers\notifications;

use WirecardExample\Helpers\NotificationType;

class EpsNotificationHelper extends BaseNotificationHelper
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return NotificationType::TYPE_EPS;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationAsBase64()
    {
        return file_get_contents($this->getNotificationBasePath() . '/notification-base64');
    }
}