<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use App\Models\Chains;
use App\Enums\CastGoalsEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $castGoalsEnums = CastGoalsEnum::cases(); 

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            //new data
            'about' => fake()->text(),
            'boosted_until' => fake()->optional()->dateTimeBetween(now(),now()->addMinutes(30)),
            'profession' => fake()->optional()->jobTitle(),
            'university' => fake()->optional()->word(),          
            'city' => fake()->optional()->city(),   
            'FID' => fake()->optional()->numberBetween(1000, 9999), 
            'typeProfile' => fake()->optional()->numberBetween(1,6),  
            'cast_goals' => $castGoalsEnums[rand(0,count($castGoalsEnums)-1)]

        ];
    }

    public function configure() : static{

        return $this->afterCreating(function(User $user){

            $languages = Language::limit(rand(1,4))->inRandomOrder()->get();
            foreach ($languages as $key => $language) {
                $user->languages()->attach($language);
            }

            $chains = Chains::limit(rand(1,4))->inRandomOrder()->get();
            foreach ($chains as $key => $chain) {
                $user->chains()->attach($chain);
            }
        });
    }


    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
