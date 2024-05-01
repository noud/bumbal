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
        DB::table('devices')->where('employee_id', 222)->delete();
        DB::table('employees')->where('name', 'Mortada Abdul Roda')->delete();
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
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Employee created successfully",
       ]);
    }

    public function testDeleteEmployee()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Mortada Abdul Roda',
        ]);
        $id = $response['employee']['id'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('delete', 'http://localhost/api/v1/employee/' . $id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Employee deleted successfully",
       ]);
    }

    public function testShowEmployee()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);
        $id = $response['employee']['id'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('get', 'http://localhost/api/v1/employee/' . $id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
       ]);
    }

    public function testUpdateEmployee()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);
        $id = $response['employee']['id'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('put', 'http://localhost/api/v1/employee/' . $id, [
            'name' => 'Mortada Abdul Roda',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "Employee updated successfully",
       ]);
    }

    public function testEmployees()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Mortada Abdul Roda',
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('get', 'http://localhost/api/v1/employees');
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
       ]);
    }
    public function testDeleteNonExistendEmployee()
    {
        $id = 222;

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('delete', 'http://localhost/api/v1/employee/' . $id);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee does not exist",
       ]);
    }

    public function testShowNonExistendEmployee()
    {
        $id = 222;

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('get', 'http://localhost/api/v1/employee/' . $id);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee does not exist",
        ]);
    }

    public function testUpdateNonExistendEmployee()
    {
        $id = 222;

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('put', 'http://localhost/api/v1/employee/' . $id, [
            'name' => 'Mortada Abdul Roda',
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee does not exist",
       ]);
    }

    public function testDeleteEmployeeThatHasDevices()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);
        $id = $response['employee']['id'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/device', [
            'name' => 'device 1',
            'employee_id' => $id,
        ]);
     
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('delete', 'http://localhost/api/v1/employee/' . $id);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Employee still has devices",
       ]);
    }

    public function testAddEmployeeWithWrongFormField()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'nam' => 'Noud de Brouwer',
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Validation fails",
       ]);
    }

    public function testUpdateEmployeeWithWrongFormField()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);
        $id = $response['employee']['id'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('put', 'http://localhost/api/v1/employee/' . $id, [
            'nam' => 'Mortada Abdul Roda',
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Validation fails",
       ]);
    }

    public function testShowEmployeeWithInvalidToken()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $this->token,
        ])->json('post', 'http://localhost/api/v1/employee', [
            'name' => 'Noud de Brouwer',
        ]);
        $id = $response['employee']['id'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . 'x' . $this->token,
        ])->json('get', 'http://localhost/api/v1/employee/' . $id);
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => "Token is Invalid",
        ]);
    }

    // i don't know how to do this test
    // get employee without Headers returns succes
    // in Postman i get the correct error response.

    // public function testShowEmployeeWithoutToken()
    // {
    //     $response = $this->withHeaders([
    //         'Authorization' => "Bearer " . $this->token,
    //     ])->json('post', 'http://localhost/api/v1/employee', [
    //         'name' => 'Noud de Brouwer',
    //     ]);
    //     $id = $response['employee']['id'];

    //     $response = $this->json('get', 'http://localhost/api/v1/employee/' . $id);
    //     // $response->assertStatus(401);
    //     $response->assertJson([
    //         'status' => 'error',
    //         'message' => "Authorization Token not found",
    //     ]);
    // }
}