<?php

namespace Moloni\Hooks;

use Db;
use Moloni\Classes\Curl;
use Moloni\Classes\Start;
use Moloni\Enums\DocumentStatus;
use Moloni\Facades\ModuleFacade;
use Moloni\Traits\ClassTrait;

class DisplayOrderDetail
{
    use ClassTrait;

    private $order;

    private $documents = [];

    private $html = '';

    public function __construct($order)
    {
        $this->order = $order;
    }

    //         Public's         //

    public function run()
    {
        new Start();

        if (!defined('SHOW_DOWNLOAD_ORDER_VIEW') || (int)SHOW_DOWNLOAD_ORDER_VIEW === 0) {
            return;
        }

        $this->loadDocuments();

        if (empty($this->documents)) {
            return;
        }

        $this->loadHtmlToRender();
    }

    //         Private's         //

    private function loadDocuments()
    {
        $documents = Db::getInstance()->executeS('SELECT invoice_id FROM ' . _DB_PREFIX_ . "moloni_invoices WHERE order_id = '" . (int)$this->order->id . "'");

        if (empty($documents)) {
            return;
        }

        foreach ($documents as $document) {
            $documentId = (int)$document['invoice_id'];

            if ($documentId <= 0) {
                continue;
            }

            $documentData = $this->getDocumentData($documentId);

            if (empty($documentData) || $documentData['status'] !== DocumentStatus::CLOSED) {
                continue;
            }

            $documentPdfLink = $this->getDocumentUrl($documentId);

            if (empty($documentPdfLink)) {
                continue;
            }

            $documentTypeName = $this->getDocumentTypeName($documentData['document_type']['saft_code']);

            if (empty($documentTypeName)) {
                continue;
            }

            $this->documents[] = [
                'label' => $documentTypeName,
                'href' => $documentPdfLink,
                'data' => $documentData
            ];
        }
    }

    private function loadHtmlToRender()
    {
        ob_start();

        ?>
            <section id="billing_document" class="box">
                <h5>
                    <?= ModuleFacade::getModule()->l('Billing document', $this->className()) ?>
                </h5>
                <ul>
                    <?php foreach ($this->documents as $document) : ?>
                    <li>
                        <a href="<?= $document['href'] ?>" target="_blank">
                            <?= $document['label'] ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php

        $this->html = ob_get_clean();
    }

    private function getDocumentUrl($documentId)
    {
        $result = Curl::simple("documents/getPDFLink", ['company_id' => COMPANY, 'document_id' => $documentId]);

        if (isset($result['url'])) {
            return $result['url'];
        }

        return '';
    }

    private function getDocumentData($documentId)
    {
        $document = Curl::simple("documents/getOne", ['company_id' => COMPANY, 'document_id' => $documentId]);

        if (isset($document['document_id'])) {
            return $document;
        }

        return [];
    }

    private function getDocumentTypeName($saftCode)
    {
        switch ($saftCode) {
            case 'FT' :
            default:
                $typeName = ModuleFacade::getModule()->l("Invoice", $this->className());
                break;
            case 'FR' :
                $typeName = ModuleFacade::getModule()->l("Invoice/Receipt", $this->className());
                break;
            case 'GT' :
                $typeName = ModuleFacade::getModule()->l("Bill of Landing", $this->className());
                break;
            case 'NEF' :
                $typeName = ModuleFacade::getModule()->l("Order Note", $this->className());
                break;
            case 'OR':
                $typeName = ModuleFacade::getModule()->l("Estimate", $this->className());
                break;
        }

        return $typeName;
    }

    //         Get's         //

    public function getHtml()
    {
        return $this->html;
    }
}
