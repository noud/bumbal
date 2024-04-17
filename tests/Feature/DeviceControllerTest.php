<?php

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class DeviceControllerTest extends TestCase {
    /**
     * @var string
     */
    public $token = '';

    public $employee_id;

    public function setUp() :void
    {
        parent::setUp();
        Artisan::call('migrate');
        DB::table('devices')->where('name', 'device 1')->delete();
        $response = $this->json('post', 'http://localhost/api/v1/register', [
            'name' => 'noud3',
            'email' => 'noud3@home.nl',
            'password' => 'test1234',
        ]);

        $response = $this->json('post', 'http://localhost/api/v1/login', [
            'email' => 'noud3@home.nl',
            'password' => 'test1234',
        ]);       
        $response->assertStatus(200);
        $token = $response['authorisation']['token'];
        $this->token = $token;

        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ], ['Authorization' => 'Bearer ' . $this->token]);       
        $response->assertStatus(200);
        $employee_id = $response['employee']['id'];
        $this->employee_id = $employee_id;

    }
    
    public function tearDown() :void
    {
        parent::tearDown();
    }

    /**
     * Register test user
     *
     * @return void
     */
    public function testAddDevice()
    {
        $response = $this->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 1',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Device created successfully",
       ]);
    }

    public function testDeleteDevice()
    {
        $response = $this->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 1',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $id = $response['device']['id'];

        $response = $this->json('delete', 'http://localhost/api/v1/device/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Device deleted successfully",
       ]);
    }

    public function testShowDevice()
    {
        $response = $this->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 1',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $id = $response['device']['id'];

        $response = $this->json('get', 'http://localhost/api/v1/device/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
       ]);
    }

    public function testUpdateDevice()
    {
        $response = $this->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 1',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $id = $response['device']['id'];

        $response = $this->json('put', 'http://localhost/api/v1/device/' . $id, [
            'name' => 'device 2',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Device updated successfully",
       ]);
    }

    public function testDevices()
    {
        $response = $this->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 1',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response = $this->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 2',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response = $this->json('get', 'http://localhost/api/v1/devices?page=1',
        ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
       ]);
    }

    public function testDeleteNonExistendDevice()
    {
        $id = 222;

        $response = $this->json('delete', 'http://localhost/api/v1/device/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'failure',
            'message' => "Device does not exist.",
       ]);
    }

    public function testShowNonExistendDevice()
    {
        $id = 222;

        $response = $this->json('get', 'http://localhost/api/v1/device/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'failure',
            'message' => "Device does not exist.",
        ]);
    }

    public function testUpdateNonExistendDevice()
    {
        $id = 222;

        $response = $this->json('put', 'http://localhost/api/v1/device/' . $id, [
            'name' => 'device 2',
            'employee_id' => $this->employee_id,
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'failure',
            'message' => "Device does not exist.",
       ]);
    }

}