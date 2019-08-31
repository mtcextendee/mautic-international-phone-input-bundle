<?php
/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$containerType     = (isset($type)) ? $type : 'text';
$defaultInputClass = (isset($inputClass)) ? $inputClass : 'input';
include __DIR__.'/../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$formName= !empty($formName) ? $formName : 'preview';

$input = $view->render(
    'MauticFormBundle:Field:text.html.php',
    [
        'field'      => $field,
        'inForm'     => (isset($inForm)) ? $inForm : false,
        'type'       => 'text',
        'id'         => $id,
        'formId'     => (isset($formId)) ? $formId : 0,
        'formName'   => (isset($formName)) ? $formName : '',
        'inputClass' => 'input inttel'.$formName,
    ]
);
echo str_replace('<input', '<input data-field-alias="'.$field['alias'].'"', $input);

$elementId = 'mauticform_input'.$formName.'_'.$field['alias'];

echo <<<HTML
<link rel="stylesheet" href="{$view['assets']->getUrl(
    'plugins/MauticInternationalPhoneInputBundle/Assets/css/intlTelInput.min.css', null, null, true
)}">
<script src="{$view['assets']->getUrl('plugins/MauticInternationalPhoneInputBundle/Assets/js/intlTelInput.min.js', null, null, true)}"></script>
<script async defer src="{$view['router']->generate(
    'mautic_country_code_generate',
    ['formName' => $formName],
    true
)}"></script>
HTML;
