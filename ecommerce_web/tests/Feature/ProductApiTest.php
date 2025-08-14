<?php


namespace Tests\Feature;


use App\Models\User;
use App\Models\Product;
use Tests\TestCase;


class ProductApiTest extends TestCase
{
    protected function authenticateAdmin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);


        $this->actingAs($admin);


        return $admin;
    }


    /** @test */
    public function admin_can_create_product()
    {
        $this->authenticateAdmin();


        $productName = 'Unique Product ' . uniqid();


        $response = $this->postJson('/api/products', [
            'name' => $productName,
            'description' => 'Product description',
            'price' => 99.99,
            'category_id' => 1,
            'stock' => 10,
        ]);


        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => $productName]);


        $this->assertDatabaseHas('products', ['name' => $productName]);
    }


    /** @test */
    public function admin_can_update_product()
    {
        $this->authenticateAdmin();


        $product = Product::factory()->create([
            'name' => 'Old Product Name',
        ]);


        $newName = 'Updated Product Name ' . uniqid();


        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => $newName,
        ]);


        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $newName]);


        $this->assertDatabaseHas('products', ['name' => $newName]);
    }
   


    /** @test */
    public function admin_can_delete_product()
    {
        $this->authenticateAdmin();


        $product = Product::factory()->create([
            'name' => 'To Be Deleted ' . uniqid(),
        ]);


        $response = $this->deleteJson("/api/products/{$product->id}");


        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Delete product successfully',
                 ]);


        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }


    /** @test */
    public function admin_can_list_product()
    {
        $this->authenticateAdmin();


        Product::factory()->count(3)->create();


        $response = $this->getJson('/api/products');


        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }


    /** @test */
    public function admin_can_view_single_product()
    {
        $this->authenticateAdmin();


        $product = Product::factory()->create([
            'name' => 'View Test ' . uniqid(),
        ]);


        $response = $this->getJson("/api/products/{$product->id}");


        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $product->name]);
    }
}

