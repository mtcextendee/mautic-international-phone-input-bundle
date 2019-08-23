<?php

/*
 * @copyright   2019 Konstantin Scheumann. All rights reserved
 * @author      Konstantin Scheumann
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

/**
 * Class InternationalPhoneInputIntegration.
 */
class InternationalPhoneInputIntegration extends AbstractIntegration
{
    const INTEGRATION_NAME = 'InternationalPhoneInput';

    public function getName()
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName()
    {
        return 'International Phone Input';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [
        ];
    }

    public function getIcon()
    {
        return 'plugins/MauticInternationalPhoneInputBundle/Assets/img/icon.png';
    }
}
