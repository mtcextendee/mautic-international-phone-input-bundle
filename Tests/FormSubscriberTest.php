<?php

/*
 * @copyright   2019 Konstantin Scheumann. All rights reserved
 * @author      Konstantin Scheumann
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\Tests;

use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticInternationalPhoneInputBundle\EventListener\FormSubscriber;
use MauticPlugin\MauticInternationalPhoneInputBundle\Integration\InternationalPhoneInputIntegration;
use MauticPlugin\MauticInternationalPhoneInputBundle\Service\InternationalPhoneInputClient;
use PHPUnit_Framework_MockObject_MockBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FormSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|InternationalPhoneInputIntegration
     */
    private $integration;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|ModelFactory
     */
    private $modelFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|InternationalPhoneInputClient
     */
    private $internationalphoneinputClient;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|ValidationEvent
     */
    private $validationEvent;

    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder|FormBuilderEvent
     */
    private $formBuildEvent;

    protected function setUp()
    {
        parent::setUp();

        $this->integration       = $this->createMock(InternationalPhoneInputIntegration::class);
        $this->eventDispatcher   = $this->createMock(EventDispatcherInterface::class);
        $this->integrationHelper = $this->createMock(IntegrationHelper::class);
        $this->modelFactory      = $this->createMock(ModelFactory::class);
        $this->internationalphoneinputClient   = $this->createMock(InternationalPhoneInputClient::class);
        $this->validationEvent   = $this->createMock(ValidationEvent::class);
        $this->formBuildEvent    = $this->createMock(FormBuilderEvent::class);

        $this->eventDispatcher
            ->method('addListener')
            ->willReturn(true);

        $this->integration
            ->method('getKeys')
            ->willReturn(['site_key' => 'test', 'secret_key' => 'test']);
    }

    public function testOnFormValidateSuccessful()
    {
        $this->internationalphoneinputClient->expects($this->once())
            ->method('verify')
            ->willReturn(true);

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn($this->integration);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormValidateFailure()
    {
        $this->internationalphoneinputClient->expects($this->once())
            ->method('verify')
            ->willReturn(false);

        $this->validationEvent->expects($this->once())
            ->method('getValue')
            ->willReturn('any-value-should-work');

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn($this->integration);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormValidateWhenPluginIsNotInstalled()
    {
        $this->internationalphoneinputClient->expects($this->never())
            ->method('verify');

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn(null);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormValidateWhenPluginIsNotConfigured()
    {
        $this->internationalphoneinputClient->expects($this->never())
            ->method('verify');

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn(['site_key' => '']);

        $this->createFormSubscriber()->onFormValidate($this->validationEvent);
    }

    public function testOnFormBuildWhenPluginIsInstalledAndConfigured()
    {
        $this->formBuildEvent->expects($this->once())
            ->method('addFormField')
            ->with('plugin.internationalphoneinput');

        $this->formBuildEvent->expects($this->once())
            ->method('addValidator')
            ->with('plugin.internationalphoneinput.validator');

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn($this->integration);

        $this->createFormSubscriber()->onFormBuild($this->formBuildEvent);
    }

    public function testOnFormBuildWhenPluginIsNotInstalled()
    {
        $this->formBuildEvent->expects($this->never())
            ->method('addFormField');

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn(null);

        $this->createFormSubscriber()->onFormBuild($this->formBuildEvent);
    }

    public function testOnFormBuildWhenPluginIsNotConfigured()
    {
        $this->formBuildEvent->expects($this->never())
            ->method('addFormField');

        $this->integrationHelper->expects($this->once())
            ->method('getIntegrationObject')
            ->willReturn(['site_key' => '']);

        $this->createFormSubscriber()->onFormBuild($this->formBuildEvent);
    }

    /**
     * @return FormSubscriber
     */
    private function createFormSubscriber()
    {
        return new FormSubscriber(
            $this->eventDispatcher,
            $this->integrationHelper,
            $this->modelFactory,
            $this->internationalphoneinputClient
        );
    }
}
