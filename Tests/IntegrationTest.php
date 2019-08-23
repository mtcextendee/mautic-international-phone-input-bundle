<?php

/*
 * @copyright   2019 MTCExtendee. All rights reserved
 * @author      MTCExtendee
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\Tests;

use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticInternationalPhoneInputBundle\EventListener\FormSubscriber;
use MauticPlugin\MauticInternationalPhoneInputBundle\Integration\InternationalPhoneInputIntegration;
use MauticPlugin\MauticInternationalPhoneInputBundle\Service\InternationalPhoneInputClient;
use PHPUnit_Framework_MockObject_MockBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{

    const RECAPTCHA_TESTING_SITE_KEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    const RECAPTCHA_TESTING_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    /**
     * @var InternationalPhoneInputIntegration
     */
    protected $integration;

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    protected function setUp()
    {
        parent::setUp();

        $this->integration = $this->getMockBuilder(InternationalPhoneInputIntegration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->integration
            ->method('getKeys')
            ->willReturn(['site_key' => self::RECAPTCHA_TESTING_SITE_KEY, 'secret_key' => self::RECAPTCHA_TESTING_SECRET_KEY]);

        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcher
            ->method('addListener')
            ->willReturn(true);


        $this->integrationHelper = $this->getMockBuilder(IntegrationHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->integrationHelper
            ->method('getIntegrationObject')
            ->willReturn($this->integration);
    }

    public function testOnFormValidate()
    {
        /** @var ModelFactory $modelFactory */
        $modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var PHPUnit_Framework_MockObject_MockBuilder|ValidationEvent $validationEvent */
        $validationEvent = $this->getMockBuilder(ValidationEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validationEvent
            ->method('getValue')
            ->willReturn('any-value-should-work');
        $validationEvent
            ->expects($this->never())
            ->method('failedValidation');

        $formSubscriber = new FormSubscriber(
            $this->eventDispatcher,
            $this->integrationHelper,
            $modelFactory,
            new InternationalPhoneInputClient($this->integrationHelper)
        );
        $formSubscriber->onFormValidate($validationEvent);
    }
}