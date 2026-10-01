<?php

use App\Models\Category;

test('test category resource api test', function(){
    $category = Category::factory()->create();

    $this->get("api/categories/$category->id")
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'created_at' => $category->created_at->toJSON(),
                'updated_at' => $category->updated_at->toJSON()
            ]
        ]);
});

test('category collection resource api test', function(){
    $categories = Category::factory(2)->create();

    $this->get('api/categories')
        ->assertStatus(200)
         ->assertJson([
            'data' => [
               [
                'id' => $categories[0]->id,
                'name' => $categories[0]->name,
                'created_at' => $categories[0]->created_at->toJSON(),
                'updated_at' => $categories[0]->updated_at->toJSON()
               ],
                 [
                'id' => $categories[1]->id,
                'name' => $categories[1]->name,
                'created_at' => $categories[1]->created_at->toJSON(),
                'updated_at' => $categories[1]->updated_at->toJSON()
               ]

            ]
        ]);
});

test('category collection resource api update test', function(){
    $categories = Category::factory(2)->create();

    $this->get('api/categories-custom')
        ->assertStatus(200)
         ->assertJson([
            'total' => 2,
            'data' => [
               [
                'id' => $categories[0]->id,
                'name' => $categories[0]->name,
                // 'created_at' => $categories[0]->created_at->toJSON(),
                // 'updated_at' => $categories[0]->updated_at->toJSON()
               ],
                 [
                'id' => $categories[1]->id,
                'name' => $categories[1]->name,
                // 'created_at' => $categories[1]->created_at->toJSON(),
                // 'updated_at' => $categories[1]->updated_at->toJSON()
               ]

            ]
        ]);
});