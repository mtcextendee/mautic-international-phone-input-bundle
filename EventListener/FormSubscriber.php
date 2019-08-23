<?php

/*
 * @copyright   2019 MTCExtendee. All rights reserved
 * @author      MTCExtendee
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticInternationalPhoneInputBundle\Integration\InternationalPhoneInputIntegration;
use MauticPlugin\MauticInternationalPhoneInputBundle\InternationalPhoneInputEvents;
use MauticPlugin\MauticInternationalPhoneInputBundle\Service\InternationalPhoneInputClient;

class FormSubscriber extends CommonSubscriber
{
    const FIELD_NAME = 'plugin.internationalphoneinput';

    /**
     * @var InternationalPhoneInputClient
     */
    protected $internationalphoneinputClient;

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var boolean
     */
    private $internationalphoneinputIsConfigured = false;

    /**
     * @param IntegrationHelper $integrationHelper
     */
    public function __construct(
        IntegrationHelper $integrationHelper
    ) {
        $integrationObject     = $integrationHelper->getIntegrationObject(InternationalPhoneInputIntegration::INTEGRATION_NAME);
        if ($integrationObject instanceof AbstractIntegration) {
                $this->internationalphoneinputIsConfigured = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD         => ['onFormBuild', 0],
            InternationalPhoneInputEvents::ON_FORM_VALIDATE => ['onFormValidate', 0],
        ];
    }

    /**
     * @param FormBuilderEvent $event
     */
    public function onFormBuild(FormBuilderEvent $event)
    {
        if (!$this->internationalphoneinputIsConfigured) {
            return;
        }
        $event->addFormField(self::FIELD_NAME, [
            'label'          => 'mautic.plugin.actions.internationalphoneinput',
            'formType'       =>  'internationalphoneinput',
            'template'       => 'MauticInternationalPhoneInputBundle:Integration:internationalphoneinput.html.php',
            'builderOptions' => [
            ],
        ]);

    }
}
