<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Static counter for unique doc_no.
     */
    private static int $docCounter = 0;

    public function definition(): array
    {
        self::$docCounter++;

        $docDate = Carbon::now()->subDays(rand(0, 180));
        $dueDate = (clone $docDate)->addDays(rand(15, 60));
        $subtotal = rand(1000, 200000) + (rand(0, 99) / 100);
        $vatRate = 7.00;
        $vat = round($subtotal * ($vatRate / 100), 2);
        $total = $subtotal + $vat;

        $statuses = ['ชำระแล้ว', 'ค้างชำระ', 'ค้างชำระ', 'ค้างชำระ', 'ชำระแล้ว'];

        return [
            'doc_no' => 'INV-' . date('Ym') . '-' . str_pad(self::$docCounter, 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()->id ?? 1,
            'branch_id' => Branch::inRandomOrder()->first()->id ?? 1,
            'doc_date' => $docDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $total,
            'vat_rate' => $vatRate,
            'note' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement($statuses),
            'created_at' => $docDate,
            'updated_at' => $docDate,
        ];
    }

    /**
     * Mark sale as paid.
     */
    public function paid(): Factory
    {
        return $this->state(function (array $attributes) {
            return ['status' => 'ชำระแล้ว'];
        });
    }

    /**
     * Mark sale as unpaid.
     */
    public function unpaid(): Factory
    {
        return $this->state(function (array $attributes) {
            return ['status' => 'ค้างชำระ'];
        });
    }

    /**
     * Set sale date to today.
     */
    public function today(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'doc_date' => Carbon::today(),
                'due_date' => Carbon::today()->addDays(30),
            ];
        });
    }
}
