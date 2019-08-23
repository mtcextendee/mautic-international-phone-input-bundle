<?php

return [
    'name'        => 'International Phone Input',
    'description' => 'Enables International Phone Input integration.',
    'version'     => '1.0',
    'author'      => 'Konstantin Scheumann',

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
                'arguments' =>  []
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
