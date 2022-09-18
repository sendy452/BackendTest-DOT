<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testFetchingApi()
    {
        $this->get("api/fetch-api", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(["status","data"]);
    }
}
