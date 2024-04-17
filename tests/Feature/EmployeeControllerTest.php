<?php

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class EmployeeControllerTest extends TestCase {
    /**
     * @var string
     */
    public $token = '';

    public function setUp() :void
    {
        parent::setUp();
        Artisan::call('migrate');
        DB::table('users')->where('name', 'noud5')->delete();
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
    }
    
    public function tearDown() :void
    {
        parent::tearDown();
    }

    public function testAddEmployee()
    {
        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Employee created successfully",
       ]);
    }

    public function testDeleteEmployee()
    {
        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $id = $response['employee']['id'];

        $response = $this->json('delete', 'http://localhost/api/v1/employee/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Employee deleted successfully",
       ]);
    }

    public function testShowEmployee()
    {
        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $id = $response['employee']['id'];

        $response = $this->json('get', 'http://localhost/api/v1/employee/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
       ]);
    }

    public function testUpdateEmployee()
    {
        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $id = $response['employee']['id'];

        $response = $this->json('put', 'http://localhost/api/v1/employee/' . $id, [
            'name' => 'Mortada Abdul Roda',
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Employee updated successfully",
       ]);
    }

    public function testEmployees()
    {
        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response = $this->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Mortada Abdul Roda',
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response = $this->json('get', 'http://localhost/api/v1/employees', [
            'Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
       ]);
    }
    public function testDeleteNonExistendEmployee()
    {
        $id = 222;

        $response = $this->json('delete', 'http://localhost/api/v1/employee/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee does not exist.",
       ]);
    }

    public function testShowNonExistendEmployee()
    {
        $id = 222;

        $response = $this->json('get', 'http://localhost/api/v1/employee/' . $id
        , ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee does not exist.",
        ]);
    }

    public function testUpdateNonExistendEmployee()
    {
        $id = 222;

        $response = $this->json('put', 'http://localhost/api/v1/employee/' . $id, [
            'name' => 'Mortada Abdul Roda',
        ], ['Authorization' => 'Bearer ' . $this->token]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee does not exist.",
       ]);
    }

}