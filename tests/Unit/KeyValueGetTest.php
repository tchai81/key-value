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
            ->assertJson(['result' => reset($dbKeyValues)['value']]);
    }

    /**
     * Test to get value by exact timestamp
     */
    public function testGetValueByExactTimestampAndKey() {
        $dbKeyValues = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp & Id
        $timestamps = array_column($dbKeyValues, 'created_at');
        $ids = array_column($dbKeyValues, 'id');
        array_multisort($timestamps, SORT_DESC, $ids, SORT_DESC, $dbKeyValues);

        $indexToValidate = rand(0,2);
        $dateAtToValidate = $dbKeyValues[$indexToValidate]['created_at'];
        $timestampToValidate = strtotime($dateAtToValidate);

        //check whether db has rows with the same exact timestamp. If so, always retrieve the value based on latest Id
        $valuesToValidate = array_filter($dbKeyValues, function($dbKeyValue) use ($dateAtToValidate){
            return $dbKeyValue['created_at'] == $dateAtToValidate;
        });
        
        $this->json('GET', "api/key-value/{$this->dbKey->name}?timestamp={$timestampToValidate}")
            ->assertStatus(200)
            ->assertJson(['result' => reset($valuesToValidate)['value']]);   
    }

    /**
     * Test to get value by random timestamp
     */
    public function testGetValueByRandomTimestampAndKey() {
        $dbKeyValues = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp & Id
        $timestamps = array_column($dbKeyValues, 'created_at');
        $ids = array_column($dbKeyValues, 'id');
        array_multisort($timestamps, SORT_DESC, $ids, SORT_DESC, $dbKeyValues);

        $indexToValidate = rand(0,2);
        $dateAtToValidate = $dbKeyValues[$indexToValidate]['created_at'];
        $timestampToValidate = strtotime("+1 minutes", strtotime($dateAtToValidate));

        //check whether db has rows with the same exact timestamp. If so, always retrieve the value based on latest Id
        $valuesToValidate = array_filter($dbKeyValues, function($dbKeyValue) use ($timestampToValidate){
            return strtotime($dbKeyValue['created_at']) <= $timestampToValidate;
        });

        $db = Key::with('values')->get()->toArray();

        $this->json('GET', "api/key-value/{$this->dbKey->name}?timestamp={$timestampToValidate}")
            ->assertStatus(200)
            ->assertJson(['result' => reset($valuesToValidate)['value']]);   
    }
}
