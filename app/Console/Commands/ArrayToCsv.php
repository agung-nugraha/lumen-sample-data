<?php

namespace App\Console\Commands;

use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'product:generate-csv',
    description: 'Generate product to csv.'
)]
class ArrayToCsv extends Command
{
    private function header()
    {
        return [
            'sku',
            'product.attribute_set',
            'name',
            'stock.qty',
            'description',
            'price',
            'status',
            'visibility',
            'product.type'
        ];
    }

    public function handle()
    {
        $productDir = storage_path('product/');
        $filePath = $productDir . 'product-' . strtotime('now') . '.csv';

        $faker = Factory::create('id_ID');
        try {
            $handle = fopen($filePath, 'w');
            fputcsv($handle, $this->header());

            for ($i = 0; $i <= 100000; $i++) {
                fputcsv($handle, [
                    $faker->isbn10(),
                    'Default',
                    ucwords(str_replace('-', ' ', $faker->slug(3))),
                    100,
                    $faker->paragraph(),
                    floor($faker->numberBetween(1000, 100000)),
                    'Enabled',
                    'Catalog, Search',
                    'simple'
                ]);
            }
            fclose($handle);
        } catch (\Exception $e) {
            File::delete($filePath);
        }
    }
}
