<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Key;
use App\Models\KeyValue;

class KeyValueCreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creation of a brand new key
     */
    public function testCreateValueWithNewKey() {
        //data to be saved for testing
        $payload = [
            'key' => 'value'
        ];

        //check json response
        $this->json('POST', "api/key-value", $payload)
        ->assertStatus(200)
        ->assertJson([
            'created' => true
        ]);

        //validate whether the key exists in db
        //validate key
        $keyToValidate = Key::where('name', 'key')->with('values')->get()->toArray();

        $this->assertNotEmpty($keyToValidate);

        //validate value
        $this->assertEquals($keyToValidate[0]['values'][0]['value'], 'value');
    }

    /**
     * Test creation based on an existing key
     */
    public function testCreateValueWithExistingKey() {
        //insert some data into db
        $dbKeys = Key::factory()->has(KeyValue::factory()->count(3), 'values')->create();
        list('name' => $keyToSave) = $dbKeys->toArray();

        //prepare key value pair to be inserted into db
        //in this case, we gonna use key that's already exists in the db
        $payload[$keyToSave] = '1234';

        //check json response
        $this->json('POST', "api/key-value", $payload)
            ->assertStatus(200)
            ->assertJson([
                'created' => true
            ]);

        $keyToValidate = Key::where('name', $keyToSave)->with('values')->get()->toArray();

        //make sure that Keys table doesn't contain duplicate key with same name
        $this->assertEquals(sizeof($keyToValidate), 1);

        //check the value is created
        $this->assertTrue(in_array('1234', array_column($keyToValidate[0]['values'], 'value')));
    }

    /**
     * Test creation if supplied with empty payload
     */
    public function testCreateValueWithInvalidPayloadSample1() {
        $this->json('POST', 'api/key-value', [])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The payload must contain at least a key value pair.'
            ]);
    }

    /**
     * Test creation if supplied with empty value
     */
    public function testCreateValueWithInvalidPayloadSample2() {
        $this->json('POST', 'api/key-value', ['test'=>''])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The payload must contain at least a key value pair.'
            ]);
    }

    /**
     * Test creation if supplied with empty key
     */
    public function testCreateValueWithInvalidPayloadSample3() {
        $this->json('POST', 'api/key-value', [''=>'test'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The payload must contain at least a key value pair.'
            ]);
    }

}
