<?php


namespace Database\Factories;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'entreprise' => $this->faker->company,
            'adresse' => $this->faker->address,
            'user_id' => User::factory(),
        ];
    }
}