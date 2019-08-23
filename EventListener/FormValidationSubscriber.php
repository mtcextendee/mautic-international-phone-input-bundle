<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\EventListener;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\FormBundle\Event as Events;
use Mautic\FormBundle\FormEvents;

class FormValidationSubscriber extends CommonSubscriber
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD                => ['onFormBuilder', 0],
            FormEvents::ON_FORM_VALIDATE             => ['onFormValidate', 0],
        ];
    }

    /**
     * Add a simple email form.
     *
     * @param Events\FormBuilderEvent $event
     */
    public function onFormBuilder(Events\FormBuilderEvent $event)
    {
        $event->addValidator(
            'inttel.validation',
            [
                'eventName' => FormEvents::ON_FORM_VALIDATE,
                'fieldType' => FormSubscriber::FIELD_NAME,
                'formType'  => \Mautic\FormBundle\Form\Type\FormFieldTelType::class,
            ]
        );
    }

    /**
     * Custom validation     *.
     *
     *@param Events\ValidationEvent $event
     */
    public function onFormValidate(Events\ValidationEvent $event)
    {
        $field = $event->getField();
        $phoneNumber = $event->getValue();
        $fullPhoneNumber = ArrayHelper::getValue($field->getAlias().'_full', $this->request->request->get('mauticform'));
        if (!empty($phoneNumber) && $field->getType() === FormSubscriber::FIELD_NAME && !empty($field->getValidation()['international'])) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $parsedPhone = $phoneUtil->parse($fullPhoneNumber, PhoneNumberUtil::UNKNOWN_REGION);
                if (!$phoneUtil->isValidNumber($parsedPhone)) {
                    $this->setFailedValidation($event);
                }
            } catch (NumberParseException $e) {
                $this->setFailedValidation($event);
            }
        }
    }

    /**
     * @param Events\ValidationEvent $event
     */
    private function setFailedValidation(Events\ValidationEvent $event)
    {
        $field = $event->getField();
        if (!empty($field->getValidation()['international_validationmsg'])) {
            $event->failedValidation($field->getValidation()['international_validationmsg']);
        } else {
            $event->failedValidation($this->translator->trans('mautic.internationalphoneinput.form.submission.phone.invalid'));
        }
    }
}
