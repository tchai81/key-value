<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Key;
use App\Models\KeyValue;
use Tests\TestCase;
use Illuminate\Support\Str;

class KeyValueGetTest extends TestCase {

    use RefreshDatabase;
    private $key;

    /**
     * Setting up mock data for testing
     */
    public function setUp() : void {
        parent::setUp(); 
        //mock data
        $this->key = Key::factory()->has(KeyValue::factory()->count(3), 'values')->create();
    }

    /**
     * Test if specified key is empty string
     */
    public function testEmptyKey() {
        $this->json('GET', 'api/key-value/ ')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Key cannot be empty.'
            ]);
    }

    /**
     * Test if specified key does not exists in db
     */
    public function testNonExistKey() {
        //generate a random integer
        $keyToValidate = Str::random(2);

        $this->json('GET', "api/key-value/{$keyToValidate}")
            ->assertStatus(404)
            ->assertJson([
                'message' => "Key - {$keyToValidate} not found."
            ]);
    }


    /**
     * Test to get latest value from db if timestamp is not supplied
     */
    public function testGetLatestValueByKey() {

        $keyValues = $this->key->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($keyValues, 'created_at');
        array_multisort($timestamps, SORT_DESC, $keyValues);

        $this->json('GET', "api/key-value/{$this->key->name}")
            ->assertStatus(200)
            ->assertJson(['result' => $keyValues[0]['value']]);
    }

    /**
     * Test to get value by exact timestamp
     */
    public function testGetValueByExactTimestampAndKey() {
        $keyValues = $this->key->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($keyValues, 'created_at');
        array_multisort($timestamps, SORT_DESC, $keyValues);

        $indexToValidate = rand(0,2);

        $timestampToValidate = strtotime($keyValues[$indexToValidate]['created_at']);

        $this->json('GET', "api/key-value/{$this->key->name}?timestamp={$timestampToValidate}")
            ->assertStatus(200)
            ->assertJson(['result' => $keyValues[$indexToValidate]['value']]);   
    }

    /**
     * Test to get value by random timestamp
     */
    public function testGetValueByTimestampAndKey() {
        $keyValues = $this->key->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($keyValues, 'created_at');
        array_multisort($timestamps, SORT_DESC, $keyValues);

        $indexToValidate = rand(0,2);

        $timestampToValidate = strtotime("+1 minute", strtotime($keyValues[$indexToValidate]['created_at']));

        $this->json('GET', "api/key-value/{$this->key->name}?timestamp={$timestampToValidate}")
            ->assertStatus(200)
            ->assertJson(['result' => $keyValues[$indexToValidate]['value']]);   
    }
}
