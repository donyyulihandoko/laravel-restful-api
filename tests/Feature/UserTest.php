<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user register success', function(){

    $data = [
        'username' => 'dony',
        'password' => 'password',
        'name' => 'dony yuli handoko',
        
    ];

    $response = $this->post(route('api.users.register'), $data);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'username',
                'name',
            ]
        ])->assertJson([
            'data' => [
                'username' => 'dony',
                'name' => 'dony yuli handoko',
            ]
        ]);
});


test('user register failed', function(){

    $data = [
        'username' => '',
        'password' => '',
        'name' => '',
        
    ];

    $response = $this->post(route('api.users.register'), $data);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'username' => ["The username field is required."],
                'password' => ["The password field is required."],
                'name' => ["The name field is required."]
            ]
        ]);
});

test('user register failed username already registered', function(){

    User::factory()->create(['username' => 'dony']);


    // 2. Coba registrasi ulang dengan username yang sama
    $response = $this->post(route('api.users.register'), [
        'username' => 'dony',
        'password' => 'password',
        'name' => 'dony yuli handoko',
        
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'username' => ['The username has already been taken.'],
            ]
        ]);
});


test('user login success', function(){
    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password')
    ]);

    $response = $this->post(route('api.users.login'), [
            'username' => 'test', 
            'password' => 'password'
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'test',
                'name' => 'test'
            ]
        ]);

    $user = User::where('username', 'test')->first();
    $this->assertNotNull($user->token);
});

test('user login failed', function(){

    $response = $this->post(route('api.users.login'), [
            'username' => 'test', 
            'password' => 'password'
        ]);

    $response->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'username or password wrong'
                ]
            ]
        ]);
});

test('get current user success', function(){

    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password'),
        'token' => 'token'
    ]);

    $response = $this->get(route('api.user.get'), [
        "Authorization" => 'token'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'test',
                'name' => 'test',
                'token' => 'token'
            ]
        ]);
});

test('get current user unauthorized', function(){

    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password'),
        'token' => 'token'
    ]);

    $response = $this->get(route('api.user.get'));

    $response->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
});

test('get current user invalid token', function(){

    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password'),
        'token' => 'token'
    ]);

    $response = $this->get(route('api.user.get'), [
        "Authorization" => 'salah'
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
});

test('update password user success', function(){
    $oldUser = User::factory()->create([
            'name' => 'test',
            'username' => 'test',
            'password' => Hash::make('password'),
            'token' => 'token'
        ]);
    $header = ["Authorization" => 'token'];
    $body = ['password' => 'newPassword'];

    $response = $this->patch(route('api.users.update'), $body, $header);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'test',
                'name' => 'test',
                'token' => 'token'
            ]
        ]);

    $newUser = User::where('username', 'test')->first();
    $this->assertNotEquals($oldUser->password, $newUser->password);
    

});

test('update name user success', function(){
    $oldUser = User::factory()->create([
            'name' => 'test',
            'username' => 'test',
            'password' => Hash::make('password'),
            'token' => 'token'
        ]);
    $header = ["Authorization" => 'token'];
    $body = ['name' => 'dony'];

    $response = $this->patch(route('api.users.update'), $body, $header);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'test',
                'name' => 'dony',
                'token' => 'token'
            ]
        ]);

    $newUser = User::where('username', 'test')->first();
    $this->assertNotEquals($oldUser->name, $newUser->name);
    

});

// test('update name user success', function(){
//     $oldUser = User::factory()->create([
//             'name' => 'test',
//             'username' => 'test',
//             'password' => Hash::make('password'),
//             'token' => 'token'
//         ]);
//     $header = ["Authorization" => 'token'];
//     $body = ['name' => 'dony'];

//     $response = $this->patch(route('api.users.update'), $body, $header);

//     $response->assertStatus(200)
//         ->assertJson([
//             'data' => [
//                 'username' => 'test',
//                 'name' => 'dony',
//                 'token' => 'token'
//             ]
//         ]);

//     $newUser = User::where('username', 'test')->first();
//     $this->assertNotEquals($oldUser->name, $newUser->name);
    

// });

test('user logout success', function(){
    User::factory()->create([
            'name' => 'test',
            'username' => 'test',
            'password' => Hash::make('password'),
            'token' => 'token'
        ]);
    $header = ["Authorization" => 'token'];

    $response = $this->delete(route('api.users.logout'), headers:$header);
    
    $response->assertStatus(200)
        ->assertJson([
            'data' => true
        ]);
    
    $user = User::where('username', 'test')->first();
    $this->assertNull($user->token);
});


test('user logout failed', function(){
    User::factory()->create([
            'name' => 'test',
            'username' => 'test',
            'password' => Hash::make('password'),
            'token' => 'token'
        ]);
    $header = ["Authorization" => 'salah'];

    $response = $this->delete(route('api.users.logout'), headers:$header);
    
    $response->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
});




