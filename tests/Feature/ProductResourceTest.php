<?php

use App\Models\Product;

test('product resource api test', function(){
    Product::factory(5)->create();
    $product = Product::first();

    $this->get("api/products/$product->id")
        ->assertStatus(200)
        ->assertJson([
            'value' => [
                'name' => $product->name,
                'category' => [
                    'id' => $product->category->id, 
                    'name' => $product->category->name
                ],
                'price' => $product->price,
                'stock' => $product->stock,
                'created_at' => $product->created_at->toJSON(),
                'updated_at' => $product->updated_at->toJSON()
            ]
        ]);

});

test('test product collection wrap', function(){
    Product::factory(10)->create();

    $response = $this->get('api/products')
        ->assertStatus(200);
});

test('product resource paginate test', function(){
    Product::factory(10)->create();

    $response = $this->get('api/products-paging')
        ->assertStatus(200);

    $this->assertNotNull($response->json('links'));
    $this->assertNotNull($response->json('meta'));
    $this->assertNotNull($response->json('data'));
});

test('test additional dinamis resource', function(){
    $product = Product::factory()->create();
    $response = $this->get('api/products-debug/' . $product->id)
        ->assertStatus(200);
    
    $this->assertNotNull($response->json('server_time'));
});