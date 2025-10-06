<?php


namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_contacts_list()
    {
        $contact = Contact::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('contacts.index'));

        $response->assertStatus(200)
            ->assertViewIs('contacts.index')
            ->assertSee($contact->nom)
            ->assertSee($contact->email);
    }

    public function test_user_can_create_contact()
    {
        $contactData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john.doe@example.com',
            'telephone' => '0123456789',
            'entreprise' => 'ACME Inc',
            'adresse' => '123 rue Example'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('contacts.store'), $contactData);

        $response->assertRedirect(route('contacts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contacts', [
            'email' => 'john.doe@example.com',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_update_contact()
    {
        $contact = Contact::factory()->create(['user_id' => $this->user->id]);
        
        $updatedData = [
            'nom' => 'Updated Name',
            'prenom' => $contact->prenom,
            'email' => 'updated.email@example.com',
            'telephone' => '9876543210',
            'entreprise' => $contact->entreprise,
            'adresse' => $contact->adresse
        ];

        $response = $this->actingAs($this->user)
            ->put(route('contacts.update', $contact), $updatedData);

        $response->assertRedirect(route('contacts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'email' => 'updated.email@example.com'
        ]);
    }

    public function test_user_can_delete_contact()
    {
        $contact = Contact::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('contacts.destroy', $contact));

        $response->assertRedirect(route('contacts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id
        ]);
    }

    public function test_contact_requires_valid_email()
    {
        $contactData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'invalid-email',
            'telephone' => '0123456789',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('contacts.store'), $contactData);

        $response->assertSessionHasErrors('email');
    }

    public function test_contact_email_must_be_unique()
    {
        $existingContact = Contact::factory()->create(['user_id' => $this->user->id]);

        $contactData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => $existingContact->email,
            'telephone' => '0123456789',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('contacts.store'), $contactData);

        $response->assertSessionHasErrors('email');
    }
}