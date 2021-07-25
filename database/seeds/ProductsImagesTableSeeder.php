<?php

use Illuminate\Database\Seeder;
use App\ProductsImage;

class ProductsImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $productImageRecords = [
            ['id'=>2,'product_id'=>2,'image'=>'bdefcbc72735f64db17f3250b1e64245.png-72413.png','status'=>1],
            ['id'=>3,'product_id'=>3,'image'=>'bdefcbc72735f64db17f3250b1e64245.png-72413.png','status'=>1]
        ];

        ProductsImage::insert($productImageRecords);
    }
}
