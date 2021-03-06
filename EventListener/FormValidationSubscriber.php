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
use Mautic\LeadBundle\Model\LeadModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class FormValidationSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $request;

    /**
     * @var LeadModel
     */
    private $leadModel;

    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, LeadModel $leadModel)
    {
        $this->translator = $translator;
        $this->request    = $requestStack->getCurrentRequest();
        $this->leadModel = $leadModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD    => ['onFormBuilder', 0],
            FormEvents::ON_FORM_VALIDATE => ['onFormValidate', 0],
            FormEvents::FORM_ON_SUBMIT   => ['onFormSubmit', 0],
        ];
    }

    /**
     * Add a simple email form.
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

    public function onFormSubmit(Events\SubmissionEvent $submissionEvent)
    {
        if (!$contact = $submissionEvent->getLead()) {
            return;
        }

        $fields = $submissionEvent->getForm()->getFields();

        foreach ($fields as $field) {
            if (FormSubscriber::FIELD_NAME === $field->getType() && $field->getLeadField()) {
                if($fullPhoneNumber = ArrayHelper::getValue($field->getAlias().'_full', $this->request ? $this->request->request->get('mauticform') : [])) {
                    $this->leadModel->setFieldValues($contact, [$field->getLeadField() => $fullPhoneNumber]);
                }
            }
        }

        if (!empty($contact->getChanges())) {
            $this->leadModel->saveEntity($contact);
        }
    }

    /**
     * Custom validation     *.
     */
    public function onFormValidate(Events\ValidationEvent $event)
    {
        $field           = $event->getField();
        $phoneNumber     = $event->getValue();
        $fullPhoneNumber = ArrayHelper::getValue($field->getAlias().'_full', $this->request ? $this->request->request->get('mauticform') : []);
        if (!empty($phoneNumber) && FormSubscriber::FIELD_NAME === $field->getType() && !empty($field->getValidation()['international'])) {
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
