<?php

use Moloni\Webservice\Product\ProductSync;

/**
 * 2022 - Moloni.com
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Moloni
 * @copyright Moloni
 * @license   https://creativecommons.org/licenses/by-nd/4.0/
 * @noinspection PhpIllegalPsrClassPathInspection
 */

class WebserviceSpecificManagementMoloniResource implements WebserviceSpecificManagementInterface
{
    protected $objOutput;

    protected $output;

    protected $wsObject;

    /**
     * Interface method
     *
     * @param WebserviceOutputBuilderCore|WebserviceOutputBuilder $obj
     *
     * @return $this
     */
    public function setObjectOutput($obj)
    {
        $this->objOutput = $obj;

        return $this;
    }

    /**
     * Interface method
     */
    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    /**
     * Interface method
     *
     * @param WebserviceRequestCore|WebserviceRequest $obj
     *
     * @return $this
     */
    public function setWsObject($obj)
    {
        $this->wsObject = $obj;

        return $this;
    }

    /**
     * Interface method
     */
    public function getWsObject()
    {
        return $this->wsObject;
    }

    /**
     * Manages the incoming requests
     * Switches between operations
     */
    public function manage()
    {
        $request = Tools::getAllValues();

        if (!isset($request['operation'])) {
            $this->output = 'Bad request';

            return $this->wsObject->getOutputEnabled();
        }

        switch ($request['operation']) {
            case 'syncProducts':
                $action = new ProductSync();
                $this->output = $action->getResults();
                break;
            default:
                $this->output = 'Acknowledge';
                break;
        }
    }

    /**
     * Interface method
     *
     * @return array|string|null
     */
    public function getContent()
    {
        return $this->objOutput->getObjectRender()->overrideContent($this->output);
    }
}
