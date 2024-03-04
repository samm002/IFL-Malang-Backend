<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Campaign;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $currentDonation = $this->faker->randomFloat(2, 0, 100000);
        $targetDonation = $this->faker->randomFloat(2, $currentDonation + 1, 100000);
        // $startDate = $this->faker->date();
        // $startDate = $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d')
        $startDate = now()->format('Y-m-d');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year')->format('Y-m-d');
        
        return [
          'name' => $this->faker->unique()->sentence(4),
          'type' => $this->faker->randomElement(['kemanusiaan', 'kesehatan', 'pendidikan', 'tanggap bencana']),
          'current_donation' => $currentDonation,
          'target_donation' => $targetDonation,
          'start_date' => $startDate,
          'end_date' => $endDate,
          'description' => $this->faker->paragraph,
          'photo' => $this->faker->imageUrl(),
        ];
    }
}
