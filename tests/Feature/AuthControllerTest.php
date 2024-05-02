<?php

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class AuthControllerTest extends TestCase {
    /**
     * @var string
     */
    public $token = '';

    public function setUp() :void
    {
        parent::setUp();
        Artisan::call('migrate');
        DB::table('users')->where('name', 'noud5')->delete();
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
    public function testRegister()
    {
        $response = $this->json('post', 'http://localhost/api/v1/register', [
            'name' => 'noud5',
            'email' => 'noud5@home.nl',
            'password' => 'test1234',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => "User created successfully",
       ]);
    }

    public function testLogin()
    {
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
    }

    public function testRegisterWithWrongFormField()
    {
        $response = $this->json('post', 'http://localhost/api/v1/register', [
            'nam' => 'noud5',
            'email' => 'noud5@home.nl',
            'password' => 'test1234',
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Validation fails",
       ]);
    }

    public function testLoginWithWrongFormField()
    {
        $response = $this->json('post', 'http://localhost/api/v1/register', [
            'name' => 'noud3',
            'email' => 'noud3@home.nl',
            'password' => 'test1234',
        ]);

        $response = $this->json('post', 'http://localhost/api/v1/login', [
            'emai' => 'noud3@home.nl',
            'password' => 'test1234',
        ]);       
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => "Validation fails",
       ]);
    }

}