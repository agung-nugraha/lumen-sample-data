<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Order\Item;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @inheritDoc
     */
    public function definition()
    {
        return $this->orders();
    }

    private function orders(): array
    {
        $faker = Faker::create('id_ID');
        $id = 'SWIFT' . time() + $faker->numberBetween(1, 10000);

        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $streetAddress = $faker->streetAddress();
        $postCode = $faker->postcode();
        $phoneNumber = str_replace([' ', '+', '-'], '', $faker->phoneNumber());

        return [
            "channel_order_increment_id" => $id,
            "channel_order_id" => $id,
            "channel_code" => "SWIFT",
            "email" => $faker->email(),
            "shipping_firstname" => $firstName,
            "shipping_lastname" => $lastName,
            "shipping_street" => $streetAddress,
            "shipping_city" => "Sleman, Berbah, Kali Tirto",
            "shipping_region" => "ID-YO",
            "shipping_postcode" => $postCode,
            "shipping_country_id" => 'ID',
            "shipping_telephone" => $phoneNumber,
            "billing_firstname" => $firstName,
            "billing_lastname" => $lastName,
            "billing_street" => $streetAddress,
            "billing_city" => "Sleman, Berbah, Kali Tirto",
            "billing_region" => "ID-YO",
            "billing_country_id" => 'ID',
            "billing_postcode" => $postCode,
            "billing_telephone" => $phoneNumber,
            "shipping_longitude" => '110.2474787',
            "shipping_latitude" => '-7.7710497',
            "status" => "allocating_failed",
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "channel_order_status" => "processing",
            "customer_group" => "General",
            "channel_payment_method" => "banktransfer",
            "channel_shipping_method" => "JNE Reguler",
            "channel_shipping_cost" => 22000,
            "channel_grand_total" => 235300,
            "custom_order_attributes" => serialize([
                "remark" => "",
                "provider" => "JNE",
                "service" => "REG",
                "pickup_person_name" => "",
                "pickup_person_phone" => "",
                "pickup_person_email" => "",
                "pos_payments" => ""
            ])
        ];
    }
}
