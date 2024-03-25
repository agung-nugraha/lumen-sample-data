<?php

namespace App\Service\Pubsub;

use Faker\Factory as Faker;
use Illuminate\Support\Str;

class Product extends Pubsub
{
    private array $colorUnique;

    public function push()
    {
        $topic = $this->getTopic('PRODUCT_FETCHED_PIM');
        $limitProduct = 0;
        while ($limitProduct < 10000) {
            $products = [];
            for ($i = 0; $i < 1000; $i++) {
                $products[]['data'] = json_encode($this->createProduct($i + $limitProduct));
                $limitProduct++;
            }
            $topic->publishBatch($products);
        }
    }

    private function createProduct(int $iteration)
    {
        $disk = storage_path('product/');
        $productData = json_decode(file_get_contents($disk . 'product-tkpd.json'), true);

        $fake = Faker::create('id_ID');

        $title = sprintf("Test %s", $fake->sentence(2, false));
        $sku = sprintf(
            "TST%s%d",
            strtoupper($fake->randomLetter()),
            ($fake->randomNumber() + $iteration + time())
        );
        $productId = time() + $iteration;
        $productIdIncrement = $productId;

        $productData['id'] = $fake->uuid();
        $productData['product']['name'] = $title;
        $productData['product']['sku'] = $sku;
        $productData['product']['description'] = $fake->paragraph();
        $productData['product']['slug'] = Str::slug($title);

        $variant = $productData['product']['variants'];
        $variants = [];
        $sizeUnique = [];
        $this->colorUnique = [];
        for ($i = 0; $i < $fake->numberBetween(1, 3); $i++) {
            $colorOptions = $this->getColorOption($fake);
            $sizeOptions = $fake->randomElement(['XL', 'L', 'M']);
            if (empty($sizeUnique)) {
                $sizeUnique[] = $sizeOptions;
            } else {
                $sizeOptions = reset($sizeUnique);
            }

            $variantSku = sprintf($sku . '-%s-%s', $colorOptions, $sizeOptions);
            $variant['variant_sku'] = $variantSku;
            $variant['variant_name'] = sprintf($title . ' / %s / %s', $colorOptions, $sizeOptions);
            $variant['status'] = $fake->randomElement(['inactive', 'active']);
            $variant['url'] = "https://tokopedia.com/" . Str::slug($title);
            $variant['img_urls'] = [$fake->randomElement($productData['product']['img_urls'])];

            foreach ($variant['attributes'] as $key => $attribute) {
                if ($attribute['name'] == 'color') {
                    $variant['attributes'][$key]['value'] = $colorOptions;
                }
                if ($attribute['name'] == 'size') {
                    $variant['attributes'][$key]['value'] = $sizeOptions;
                }
            }

            $variant['options'] = $variant['attributes'];

            $variant['remote_variant']['sku'] = $variantSku;
            $variant['remote_variant']['product_sku'] = $sku;
            $variant['remote_variant']['product_id'] = $productId;

            $productIdIncrement++;
            $variant['remote_variant']['variant_id'] = $productIdIncrement;

            $variants[] = $variant;
        }
        $productData['product']['variants'] = $variants;

        return $productData;
    }

    private function getColorOption($fake)
    {
        $colorOption = $fake->randomElement(['Merah', 'Biru', 'Orange', 'Hitam', 'Pink', 'Ungu', 'Abu-abu']);
        if (!empty($this->colorUnique) && in_array($colorOption, $this->colorUnique)) {
            return $this->getColorOption($fake);
        } else {
            $colorUnique[] = $colorOption;
        }

        return $colorOption;
    }

    public function fetch()
    {
        //
    }
}
