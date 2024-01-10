<?php

namespace Moloni\Helpers;

class Version
{
    public static function isPrestashopVersion_8()
    {
        return version_compare(_PS_VERSION_, '8') >= 0;
    }

    public static function isPrestashopVersion_1_7()
    {
        if (version_compare(_PS_VERSION_, '8') >= 0) {
            return false;
        }

        return version_compare(_PS_VERSION_, '1.7') >= 0;
    }

    public static function isPrestashopVersion_1_6()
    {
        if (version_compare(_PS_VERSION_, '1.7') >= 0) {
            return false;
        }

        return version_compare(_PS_VERSION_, '1.6') >= 0;
    }
}
