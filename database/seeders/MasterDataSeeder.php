<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUom;
use App\Models\Supplier;
use App\Models\Uom;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed master data: Categories, UOMs, Suppliers, Customers, Warehouses, Locations, Products, Pallets.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. UOMs
            $uoms = collect([
                ['code' => 'PCS', 'name' => 'Pieces'],
                ['code' => 'BOX', 'name' => 'Box'],
                ['code' => 'KG', 'name' => 'Kilogram'],
                ['code' => 'MTR', 'name' => 'Meter'],
                ['code' => 'LTR', 'name' => 'Liter'],
                ['code' => 'PLT', 'name' => 'Pallet'],
            ])->map(fn($item) => Uom::firstOrCreate(['code' => $item['code']], $item));

            $baseUom = $uoms->firstWhere('code', 'PCS') ?? $uoms->first();
            $boxUom = $uoms->firstWhere('code', 'BOX') ?? $uoms->last();

            // 2. Product Categories (Main & Sub)
            $mainCategories = ProductCategory::factory()->count(4)->create();
            $subCategories = collect();
            foreach ($mainCategories as $main) {
                $sub = ProductCategory::factory()->subCategory($main->id)->count(2)->create();
                $subCategories = $subCategories->merge($sub);
            }
            $allCategories = $mainCategories->merge($subCategories);

            // 3. Suppliers & Customers
            $suppliers = Supplier::factory()->count(5)->create();
            $customers = Customer::factory()->count(5)->create();

            // 4. Warehouses & Locations
            $warehouses = Warehouse::factory()->count(2)->create();

            foreach ($warehouses as $warehouse) {
                // Staging Area
                Location::factory()->staging()->create([
                    'warehouse_id' => $warehouse->id,
                    'code' => 'STG-' . $warehouse->code,
                    'name' => 'Inbound Staging Area ' . $warehouse->name,
                ]);

                // Receiving Area
                Location::factory()->receiving()->create([
                    'warehouse_id' => $warehouse->id,
                    'code' => 'RCV-' . $warehouse->code,
                    'name' => 'Receiving Dock ' . $warehouse->name,
                ]);

                // Storage Bins
                Location::factory()->count(6)->create([
                    'warehouse_id' => $warehouse->id,
                ]);

                // Pallets
                Pallet::factory()->count(8)->create([
                    'warehouse_id' => $warehouse->id,
                ]);
            }

            // 5. Products & Product UOM conversions
            $products = Product::factory()->count(10)->recycle($allCategories)->recycle($uoms)->create([
                'base_uom_id' => $baseUom->id,
            ]);

            foreach ($products as $product) {
                ProductUom::firstOrCreate(
                    ['product_id' => $product->id, 'uom_id' => $boxUom->id],
                    [
                        'conversion_factor' => 12,
                        'is_purchase_uom' => true,
                        'is_sales_uom' => false,
                    ]
                );
            }
        });
    }
}
