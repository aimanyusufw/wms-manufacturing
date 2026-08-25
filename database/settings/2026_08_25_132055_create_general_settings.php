<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Company & Organization Info
        $this->migrator->add('general.company_name', 'Acme Manufacturing & Logistics');
        $this->migrator->add('general.company_email', 'contact@acmemfg.com');
        $this->migrator->add('general.company_phone', '+1 (555) 019-2834');
        $this->migrator->add('general.company_address', '100 Industrial Parkway, Suite 500, Detroit, MI 48201');
        $this->migrator->add('general.company_website', 'https://acmemfg.com');
        $this->migrator->add('general.tax_id', 'US-987654321');
        $this->migrator->add('general.company_logo_id', null);

        // Localization & Formats
        $this->migrator->add('general.currency_code', 'USD');
        $this->migrator->add('general.currency_symbol', '$');
        $this->migrator->add('general.timezone', 'UTC');
        $this->migrator->add('general.date_format', 'Y-m-d');
        $this->migrator->add('general.time_format', 'H:i:s');

        // WMS & Inventory Defaults
        $this->migrator->add('general.default_weight_unit', 'kg');
        $this->migrator->add('general.default_dimension_unit', 'cm');
        $this->migrator->add('general.sku_prefix', 'WMS-');
        $this->migrator->add('general.low_stock_threshold', 10);
        $this->migrator->add('general.enable_auto_sku_generation', true);
        $this->migrator->add('general.enable_activity_logging', true);
    }
};
