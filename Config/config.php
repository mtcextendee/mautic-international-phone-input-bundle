<?php

return [
    'name'        => 'International Phone Input',
    'description' => 'Enables International Phone Input integration.',
    'version'     => '1.0',
    'author'      => 'MTCExtendee',

    'routes' => [
        'public' => [
            'mautic_country_code_generate' => [
                'path'       => '/country/code/generate/{formName}',
                'controller' => 'MauticInternationalPhoneInputBundle:Js:generateCountryCode',
            ],
        ],
    ],

    'services' => [
        'events' => [
            'mautic.internationalphoneinput.event_listener.form_subscriber' => [
                'class'     => \MauticPlugin\MauticInternationalPhoneInputBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
            'mautic.form.validation.inttel.subscriber' => [
                'class'     => \MauticPlugin\MauticInternationalPhoneInputBundle\EventListener\FormValidationSubscriber::class,
                'arguments' => [
                    'translator',
                    'request_stack',
                ],
            ],
        ],
        'forms' => [
            'mautic.form.type.internationalphoneinput' => [
                'class' => \MauticPlugin\MauticInternationalPhoneInputBundle\Form\Type\InternationalPhoneInputType::class,
                'alias' => 'internationalphoneinput',
            ],
        ],
        'models' => [
        ],
        'integrations' => [
            'mautic.integration.internationalphoneinput' => [
                'class'     => \MauticPlugin\MauticInternationalPhoneInputBundle\Integration\InternationalPhoneInputIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
        'controllers' => [
            'mautic.internationalphoneinput.controller.js' => [
                'class'     => \MauticPlugin\MauticInternationalPhoneInputBundle\Controller\JsController::class,
                'arguments' => [
                    'mautic.helper.ip_lookup',
                    'templating.helper.assets',
                ],
            ],
        ],
    ],
    'parameters' => [
    ],
];
