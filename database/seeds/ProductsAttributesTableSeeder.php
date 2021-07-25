<?php

use Illuminate\Database\Seeder;
use App\ProductsAttribute;

class ProductsAttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $productAttributesRecords = [
            ['id'=>1,'product_id'=>2,'size'=>'Small','price'=>2000,'stock'=>10,'sku'=>'GCT002-S','status'=>1],
            ['id'=>2,'product_id'=>2,'size'=>'Medium','price'=>2100,'stock'=>20,'sku'=>'GCT002-M','status'=>1],
            ['id'=>3,'product_id'=>2,'size'=>'Large','price'=>2200,'stock'=>10,'sku'=>'GCT002-L','status'=>1]
        ];
        ProductsAttribute::insert($productAttributesRecords);
    }
}
