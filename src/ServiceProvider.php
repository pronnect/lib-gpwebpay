<?php

namespace Pronnect\GpWebPay;

/**
 * Class ServiceProvider
 */
class ServiceProvider
{
    public const PROVIDER_KB_SMARTPAY           = "0100"; //
    public const PROVIDER_KB_SMARTPAY_WORLDLINE = "0110"; // Cataps, s.r.o. (KB SmartPay) / Worldline
    public const PROVIDER_CSOB_CZ               = "0300"; // Československá obchodní banka, a.s.
    public const PROVIDER_GLOBAL_PAYMENTS_RO    = "0870"; // Global Payments s.r.o. – RO
    public const PROVIDER_GLOBAL_PAYMENTS_CZ    = "0880"; // Global Payments s.r.o. – CZ
    public const PROVIDER_GLOBAL_PAYMENTS_SK    = "0902"; // Global Payments s.r.o. – SK
    public const PROVIDER_GLOBAL_PAYMENTS_AT    = "0910"; // Global Payments s.r.o. – AT
    public const PROVIDER_UNICREDIT_SK          = "1111"; // UniCredit Bank Czech Republic and Slovakia, a.s. – SK
    public const PROVIDER_UNICREDIT_CZ          = "2702"; // UniCredit Bank Czech Republic and Slovakia, a.s. – CZ
    public const PROVIDER_EVO_PAYMENTS          = "5501"; // EVO Payments International s.r.o. (REVO)
    public const PROVIDER_POSTOVA_BANKA         = "6500"; // Poštová banka, a.s.
    public const PROVIDER_CSOB_SK               = "7500"; // Československá obchodná banka, a.s.
    public const PROVIDER_GLOBAL_PAYMENT_MALTA  = "8470"; // Global Payments Malta
    public const PROVIDER_GLOBAL_PAYMENT_CZ     = "9203"; // Global Payments Europe, s.r.o. – CZ
    public const PROVIDER_GLOBAL_PAYMENT_HU     = "9348"; // Global Payments Europe, s.r.o. – HU
}
