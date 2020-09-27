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
        $this->dbKey = Key::factory()->has(KeyValue::factory()->count(3), 'values')->create();
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

        $dbKeyValues = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($dbKeyValues, 'created_at');
        array_multisort($timestamps, SORT_DESC, $dbKeyValues);

        $this->json('GET', "api/key-value/{$this->dbKey->name}")
            ->assertStatus(200)
            ->assertJson(['result' => $dbKeyValues[0]['value']]);
    }

    /**
     * Test to get value by exact timestamp
     */
    public function testGetValueByExactTimestampAndKey() {
        $dbKeyValues = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($dbKeyValues, 'created_at');
        array_multisort($timestamps, SORT_DESC, $dbKeyValues);

        $indexToValidate = rand(0,2);

        $timestampToValidate = strtotime($dbKeyValues[$indexToValidate]['created_at']);

        $this->json('GET', "api/key-value/{$this->dbKey->name}?timestamp={$timestampToValidate}")
            ->assertStatus(200)
            ->assertJson(['result' => $dbKeyValues[$indexToValidate]['value']]);   
    }

    /**
     * Test to get value by random timestamp
     */
    public function testGetValueByTimestampAndKey() {
        $dbKeyValues = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($dbKeyValues, 'created_at');
        array_multisort($timestamps, SORT_DESC, $dbKeyValues);

        $indexToValidate = rand(0,2);

        $timestampToValidate = strtotime("+1 minute", strtotime($dbKeyValues[$indexToValidate]['created_at']));

        $this->json('GET', "api/key-value/{$this->dbKey->name}?timestamp={$timestampToValidate}")
            ->assertStatus(200)
            ->assertJson(['result' => $dbKeyValues[$indexToValidate]['value']]);   
    }
}
