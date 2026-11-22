<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('create contact success', function () {
    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password'),
        'token' => 'token',
    ]);

    $contact = [
        'first_name' => 'user test',
        'last_name' => 'test',
        'email' => 'user@test.com',
        'phone' => '090099000',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'token',
    ])->post(route('api.contacts.create'), $contact);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'id' => 1,
                'first_name' => 'user test',
                'last_name' => 'test',
                'email' => 'user@test.com',
                'phone' => '090099000',
            ],
        ]);
});


test('create contact failed', function () {
    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password'),
        'token' => 'token',
    ]);

    $contact = [
        'first_name' => '',
        'last_name' => 'test',
        'email' => 'user@test.com',
        'phone' => '090099000',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'token',
    ])->post(route('api.contacts.create'), $contact);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                
                'first_name' => [
                    "The first name field is required."
                ],
                
            ],
        ]);
});

test('create contact unautorized', function () {
    User::factory()->create([
        'name' => 'test',
        'username' => 'test',
        'password' => Hash::make('password'),
        'token' => 'token',
    ]);

    $contact = [
        'first_name' => 'user test',
        'last_name' => 'test',
        'email' => 'user@test.com',
        'phone' => '090099000',
    ];

    $response = $this->post(route('api.contacts.create'), $contact);

    $response->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
});

test('get contact success', function(){

    $user = User::factory()->create([
            'name' => 'test',
            'username' => 'test',
            'password' => Hash::make('password'),
            'token' => 'token',
        ]);

    $contact = Contact::factory()->create([
        'first_name' => 'user',
        'last_name' => 'test',
        'email' => 'user@test.com',
        'phone' => '08889889099',
        'user_id' => $user->id
    ]);

    $response = $this->withHeaders(['Authorization' => 'token'])
        ->get(route('api.contacts.get', $contact));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'first_name' => 'user',
                'last_name' => 'test',
                'email' => 'user@test.com',
                'phone' => '08889889099',
            ]
        ]);
});

// test('get contact failed', function(){

//     $user = User::factory()->create([
//             'name' => 'test',
//             'username' => 'test',
//             'password' => Hash::make('password'),
//             'token' => 'token',
//         ]);

//     $contact = Contact::factory()->create([
//         'first_name' => 'user',
//         'last_name' => 'test',
//         'email' => 'user@test.com',
//         'phone' => '08889889099',
//         'user_id' => $user->id
//     ]);

//     $response = $this->withHeaders(['Authorization' => 'token'])
//         ->get(route('api.contacts.get', $contact));

//     $response->assertStatus(400)
//         ->assertJson([
//             'errors' => [
//                 'message' => [
//                     'not found'
//                 ]
//             ]
//         ]);
// });

test('get contact unauthorized', function(){

    $user = User::factory()->create([
            'name' => 'test',
            'username' => 'test',
            'password' => Hash::make('password'),
            'token' => 'token',
        ]);

    $contact = Contact::factory()->create([
        'first_name' => 'user',
        'last_name' => 'test',
        'email' => 'user@test.com',
        'phone' => '08889889099',
        'user_id' => $user->id
    ]);

    $response = $this->get(route('api.contacts.get', $contact));

    $response->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
});

test('contact update success', function(){
    $user = User::factory()->create([
            'token' => 'token',
        ]);

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $newContact = [
        'first_name' => 'user update',
        'last_name' => 'test update',
        'email' => 'userupdate@test.com',
        'phone' => '08889889099',
    ];

    $response = $this->withHeaders(['Authorization' => 'token'])
        ->patch(route('api.contacts.get', $contact), $newContact);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'first_name' => 'user update',
                'last_name' => 'test update',
                'email' => 'userupdate@test.com',
                'phone' => '08889889099',
            ]
        ]);

});

it('contact update failed', function(){
    $user = User::factory()->create([
            'token' => 'token',
        ]);

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $newContact = [
        'first_name' => '',
        'last_name' => 'test update',
        'email' => 'userupdate@test.com',
        'phone' => '08889889099',
    ];

    $response = $this->withHeaders(['Authorization' => 'token'])
        ->patch(route('api.contacts.get', $contact), $newContact);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
});

it('delete contact success', function(){
    $user = User::factory()->create([
            'token' => 'token',
        ]);

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $response = $this->withHeaders(['Authorization' => 'token'])
        ->delete(route('api.contacts.get', $contact));
    
    $response->assertStatus(200)
        ->assertJson([
            'data' => true
        ]);
});
