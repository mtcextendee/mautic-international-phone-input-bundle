<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticInternationalPhoneInputBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\CoreBundle\Helper\IpLookupHelper;
use Mautic\CoreBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\HttpFoundation\Response;

class JsController extends CommonController
{
    /**
     * @var IpLookupHelper
     */
    private $ipLookupHelper;

    /**
     * @var AssetsHelper
     */
    private $assetsHelper;

    /**
     * PublicController constructor.
     *
     * @param IpLookupHelper $ipLookupHelper
     * @param AssetsHelper   $assetsHelper
     */
    public function __construct(IpLookupHelper $ipLookupHelper, AssetsHelper $assetsHelper)
    {
        $this->ipLookupHelper = $ipLookupHelper;
        $this->assetsHelper   = $assetsHelper;
    }

    /**
     * @param string $formName
     *
     * @return Response
     */
    public function generateCountryCodeAction($formName)
    {
        $ip       = $this->ipLookupHelper->getIpAddress();
        include_once __DIR__.'/../countries.php';
        $country  = ArrayHelper::getValue('country', $ip->getIpDetails() ?? []);
        $countryCode = array_search(strtolower($country), array_map('strtolower', $countries)); ## easy version
        $realFormName = ltrim($formName, '_');
        $utilsUrl = $this->assetsHelper->getUrl('plugins/MauticInternationalPhoneInputBundle/Assets/lib/js/utils.js', null, null, true
        );
        $js       = <<<JS
        if(!window.{$formName}){
       var elems = document.getElementsByClassName('inttel{$formName}');
       
       for(var i=0;i< elems.length;i ++) {
               var elem = elems[i];

      window.intlTelInput(elem , {
            hiddenInput: elem.getAttribute('data-field-alias'),
            separateDialCode: true,
          initialCountry: "{$countryCode}",
          utilsScript: "{$utilsUrl}"
     });
      window.{$formName} = true;
      }
}

  if (typeof MauticFormCallback == 'undefined') {
    var MauticFormCallback = {};
}
  
  
MauticFormCallback['{$realFormName}'] = {
    onValidateStart: function () {
         for(var i=0;i< elems.length;i ++) {
    var elem = elems[i];
    var iti  = window.intlTelInputGlobals.getInstance(elem);
      elem.nextSibling.value = iti.getNumber();
      }
}
};
    

JS;

        return new Response(
            $js,
            200,
            [
                'Content-Type' => 'application/javascript',
            ]
        );
    }
}
