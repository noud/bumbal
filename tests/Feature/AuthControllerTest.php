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
        // $this->assertEquals(200, $this->response->status());
        $response->assertStatus(200);
        // ->assertJson([
    //     $this->seeJsonEquals([
    //         'status' => 'success',
    //         'message' => "User created successfully",
    //         'user' => [
    //                 'name',
    //                 'email',
    //                 'updated_at',
    //                 'created_at',
    //                 'id',
    //             ],
    //         'authorisation' => [
    //             'token',
    //             'type' => 'bearer',
    //         ]
    //    ]);
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
        // dd($token);
    }
}