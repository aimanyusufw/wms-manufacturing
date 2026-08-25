<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // Company & Organization Info
    public string $company_name;
    public string $company_email;
    public string $company_phone;
    public string $company_address;
    public ?string $company_website;
    public ?string $tax_id;
    public ?int $company_logo_id;

    // Localization & Formats
    public string $currency_code;
    public string $currency_symbol;
    public string $timezone;
    public string $date_format;
    public string $time_format;

    // WMS & Inventory Defaults
    public string $default_weight_unit;
    public string $default_dimension_unit;
    public string $sku_prefix;
    public int $low_stock_threshold;
    public bool $enable_auto_sku_generation;
    public bool $enable_activity_logging;

    public static function group(): string
    {
        return 'general';
    }
}
