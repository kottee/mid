<?php
/**
 * This module is used for real time processing of
 * Novalnet payment module of customers.
 * Released under the GNU General Public License.
 * This free contribution made by request.
 * 
 * If you have found this script useful a small
 * recommendation as well as a comment on merchant form
 * would be greatly appreciated.
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet AG
 * All rights reserved. https://www.novalnet.de/payment-plugins/kostenpflichtig/lizenz
 */

namespace Novalnet\Methods;

use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodService;
use Plenty\Plugin\Application;
use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;

use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;

/**
 * Class NovalnetSofortPaymentMethod
 *
 * @package Novalnet\Methods
 */
class NovalnetSofortPaymentMethod extends PaymentMethodService
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    
     /**
	 * @var PaymentService
	 */
	private $paymentService;
	
	
	
    /**
     * NovalnetPaymentMethod constructor.
     *
     * @param ConfigRepository $configRepository
     * @param PaymentHelper $paymentHelper
     * @param PaymentService $paymentService
     */
    public function __construct(ConfigRepository $configRepository,
                                PaymentHelper $paymentHelper,
                                PaymentService $paymentService)
    {
        $this->configRepository = $configRepository;
        $this->paymentHelper = $paymentHelper;
        $this->paymentService  = $paymentService;
    }

    /**
     * Check the configuration if the payment method is active
     * Return true only if the payment method is active
     *
     * @return bool
     */
    public function isActive(BasketRepositoryContract $basketRepo):bool
    {
	    if(($this->configRepository->get('Novalnet.novalnet_sofort_payment_active') == 'true')){
		$active_payment_allowed_country = 'true';
		if ($allowed_country = $this->configRepository->get('Novalnet.novalnet_sofort_allowed_country')) {
			$basket = $basketRepo->load();
		$active_payment_allowed_country  = $this->paymentService->allowedCountrieslist($allowed_country, $basket);
		}
        return (bool)($this->paymentHelper->paymentActive() && $active_payment_allowed_country);
	    } 
	    return false;
    }

    /**
     * Get the name of the payment method. The name can be entered in the config.json.
     *
     * @return string
     */
    public function getName():string
    {   
		$name = trim($this->configRepository->get('Novalnet.novalnet_sofort_payment_name'));
        return ($name ? $name : $this->paymentHelper->getTranslatedText('novalnet_sofort'));
    }

    /**
     * Retrieves the icon of the payment. The URL can be entered in the configuration.
     *
     * @return string
     */
    public function getIcon():string
    {
       $logoUrl = $this->configRepository->get('Novalnet.novalnet_sofort_payment_logo');
        if($logoUrl == 'images/banktransfer.png'){
            /** @var Application $app */
            $app = pluginApp(Application::class);
            $logoUrl = $app->getUrlPath('novalnet') .'/images/banktransfer.png';
        } 
        return $logoUrl;
    }

    /**
     * Retrieves the description of the payment. The description can be entered in the configuration.
     *
     * @return string
     */
    public function getDescription():string
    {
		$description = trim($this->configRepository->get('Novalnet.novalnet_sofort_description'));
        return ($description ? $description : $this->paymentHelper->getTranslatedText('redirectional_payment_description'));
    }

    /**
     * Check if it is allowed to switch to this payment method
     *
     * @return bool
     */
    public function isSwitchableTo(): bool
    {
        return false;
    }

    /**
     * Check if it is allowed to switch from this payment method
     *
     * @return bool
     */
    public function isSwitchableFrom(): bool
    {
        return false;
    }
}
