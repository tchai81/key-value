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
    private $mockValRowCount = 99;

    /**
     * Setting up mock data for testing
     */
    public function setUp() : void {
        parent::setUp(); 
        //mock data
        $this->dbKey = Key::factory()->has(KeyValue::factory()->count($this->mockValRowCount), 'values')->create();
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
    public function testGetLatestValByKey() {

        $dbKeyVals = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp
        $timestamps = array_column($dbKeyVals, 'created_at');
        $ids = array_column($dbKeyVals, 'id');
        array_multisort($timestamps, SORT_DESC, $ids, SORT_DESC, $dbKeyVals);

        $this->json('GET', "api/key-value/{$this->dbKey->name}")
            ->assertStatus(200)
            ->assertJson(['result' => reset($dbKeyVals)['value']]);
    }

    /**
     * Test to get value by exact timestamp
     */
    public function testGetValByExactTimestampAndKey() {
        $dbKeyVals = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp & Id
        $timestamps = array_column($dbKeyVals, 'created_at');
        $ids = array_column($dbKeyVals, 'id');
        array_multisort($timestamps, SORT_DESC, $ids, SORT_DESC, $dbKeyVals);

        $indexToValidate = rand(0,$this->mockValRowCount - 1);
        $dateAtToValidate = $dbKeyVals[$indexToValidate]['created_at'];
        $timestampToValidate = strtotime($dateAtToValidate);

        //check whether db has rows with the same exact timestamp. If so, always retrieve the value based on latest Id
        $valsToValidate = array_filter($dbKeyVals, function($dbKeyVal) use ($dateAtToValidate){
            return $dbKeyVal['created_at'] == $dateAtToValidate;
        });
        
        $this->json('GET', "api/key-value/{$this->dbKey->name}?timestamp={$timestampToValidate}")
            ->assertJson(['result' => reset($valsToValidate)['value']])   
            ->assertStatus(200);
            
    }

    /**
     * Test to get value by random timestamp
     */
    public function testGetValueByRandomTimestampAndKey() {
        $dbKeyVals = $this->dbKey->values->toArray();

        // sort & get the value based on latest timestamp & Id
        $timestamps = array_column($dbKeyVals, 'created_at');
        $ids = array_column($dbKeyVals, 'id');
        array_multisort($timestamps, SORT_DESC, $ids, SORT_DESC, $dbKeyVals);

        $indexToValidate = rand(0,$this->mockValRowCount - 1);
        $dateAtToValidate = $dbKeyVals[$indexToValidate]['created_at'];
        $timestampToValidate = strtotime("+1 minutes", strtotime($dateAtToValidate));

        //check whether db has rows with the same exact timestamp. If so, always retrieve the value based on latest Id
        $valsToValidate = array_filter($dbKeyVals, function($dbKeyVal) use ($timestampToValidate){
            return strtotime($dbKeyVal['created_at']) <= $timestampToValidate;
        });

        $this->json('GET', "api/key-value/{$this->dbKey->name}?timestamp={$timestampToValidate}")
            ->assertJson(['result' => reset($valsToValidate)['value']]) 
            ->assertStatus(200);

    }
}
