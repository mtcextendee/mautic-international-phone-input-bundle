<?php

/*
 * @copyright   2019 Konstantin Scheumann. All rights reserved
 * @author      Konstantin Scheumann
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\Tests;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticInternationalPhoneInputBundle\Integration\InternationalPhoneInputIntegration;
use MauticPlugin\MauticInternationalPhoneInputBundle\Service\InternationalPhoneInputClient;
use PHPUnit_Framework_MockObject_MockBuilder;

class InternationalPhoneInputClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|InternationalPhoneInputIntegration
     */
    private $integration;

    protected function setUp()
    {
        parent::setUp();

        $this->integrationHelper = $this->createMock(IntegrationHelper::class);
        $this->integration       = $this->createMock(InternationalPhoneInputIntegration::class);
    }

    public function testVerifyWhenPluginIsNotInstalled()
    {
        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn(null);

        $this->integration->expects($this->never())
            ->method('getKeys');

        $this->createInternationalPhoneInputClient()->verify('');
    }

    public function testVerifyWhenPluginIsNotConfigured()
    {
        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn($this->integration);

        $this->integration->expects($this->once())
            ->method('getKeys')
            ->willReturn(['site_key' => 'test', 'secret_key' => 'test']);

        $this->createInternationalPhoneInputClient()->verify('');
    }

    /**
     * @return InternationalPhoneInputClient
     */
    private function createInternationalPhoneInputClient()
    {
        return new InternationalPhoneInputClient(
            $this->integrationHelper
        );
    }
}
