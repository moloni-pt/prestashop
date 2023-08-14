<?php

namespace Moloni\Services\Product;

use Country;
use TaxRulesGroup;

class FindTaxGroupFromMoloniTax
{
    private $moloniTax;

    public function __construct(array $moloniTax)
    {
        $this->moloniTax = $moloniTax;
    }

    public function handle(): int
    {
        $fiscalZone = $this->moloniTax['fiscalZone'] ?? 'pt';
        $countryId = Country::getByIso($fiscalZone);
        $value = (float)($this->moloniTax['value'] ?? 0);

        $taxes = array_reverse(TaxRulesGroup::getAssociatedTaxRatesByIdCountry($countryId), true);

        foreach ($taxes as $id => $tax) {
            if ($value === (float)$tax) {
                $taxRuleGroupObject = new TaxRulesGroup($id);

                if (!empty($taxRuleGroupObject->deleted) || empty($taxRuleGroupObject->active)) {
                    continue;
                }

                return (int)$id;
            }
        }

        return 0;
    }
}
