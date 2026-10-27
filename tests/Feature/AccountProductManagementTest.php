<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_a_product_with_an_image(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();

        $response = $this->actingAs($member)->post(route('account.products.store'), [
            'name' => 'Colorblock Scuba',
            'price' => '59.00',
            'category' => 'Clothing',
            'brand' => 'Demo Brand',
            'is_on_sale' => '1',
            'discount_percent' => '20',
            'company_profile' => 'Demo Company',
            'description' => 'Product description',
            'images' => [$this->productImage()],
        ]);

        $response->assertRedirect(route('account.my-product'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'user_id' => $member->id,
            'name' => 'Colorblock Scuba',
            'price' => '59.00',
            'category' => 'Clothing',
            'brand' => 'Demo Brand',
            'is_on_sale' => 1,
            'discount_percent' => 20,
            'company_profile' => 'Demo Company',
        ]);

        $product = Product::query()->where('user_id', $member->id)->firstOrFail();
        $this->assertCount(1, $product->image);
        Storage::disk('public')->assertExists($product->image[0]);
    }

    public function test_member_sees_the_product_fields_when_opening_the_create_form(): void
    {
        $member = User::factory()->create();
        Product::factory()->for(User::factory()->create())->create([
            'category' => 'Clothing',
            'brand' => 'Demo Brand',
        ]);

        $this->actingAs($member)
            ->get(route('account.add-product'))
            ->assertOk()
            ->assertViewIs('frontend.account.product-form')
            ->assertSeeText('Create product!')
            ->assertSee('name="category"', false)
            ->assertSee('name="brand"', false)
            ->assertSee('name="is_on_sale"', false)
            ->assertSee('name="discount_percent"', false)
            ->assertSee('name="company_profile"', false)
            ->assertSee('name="images[]"', false)
            ->assertSee('name="description"', false)
            ->assertSee('<option value="Clothing">', false)
            ->assertSee('<option value="Demo Brand">', false);
    }

    public function test_member_cannot_create_a_product_with_an_invalid_image(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();

        $this->actingAs($member)
            ->from(route('account.add-product'))
            ->post(route('account.products.store'), [
                'name' => 'Invalid image product',
                'price' => '59.00',
                'images' => [UploadedFile::fake()->create('document.txt', 10, 'text/plain')],
            ])
            ->assertRedirect(route('account.add-product'))
            ->assertSessionHasErrors('images.0');

        $this->assertDatabaseCount('products', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_member_can_open_their_product_edit_form(): void
    {
        $member = User::factory()->create();
        $product = Product::factory()->for($member)->create([
            'name' => 'Colorblock Scuba',
            'category' => 'Clothing',
            'brand' => 'Demo Brand',
            'is_on_sale' => true,
            'discount_percent' => 25,
            'company_profile' => 'Demo Company',
        ]);

        $this->actingAs($member)
            ->get(route('account.products.edit', $product))
            ->assertOk()
            ->assertViewIs('frontend.account.product-form')
            ->assertSeeText('Update product!')
            ->assertSee('blog-post-area', false)
            ->assertSee('signup-form', false)
            ->assertSee('name="category"', false)
            ->assertSee('name="brand"', false)
            ->assertSee('name="is_on_sale"', false)
            ->assertSee('name="discount_percent"', false)
            ->assertSee('name="company_profile"', false)
            ->assertSee('value="Colorblock Scuba"', false)
            ->assertSee('value="Clothing"', false)
            ->assertSee('value="Demo Brand"', false)
            ->assertSee('value="Demo Company"', false)
            ->assertSee('value="25"', false)
            ->assertSee('value="1" selected', false);
    }

    public function test_member_can_update_their_product_and_replace_its_images(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();
        $oldImage = 'products/'.$member->id.'/old.png';
        Storage::disk('public')->put($oldImage, 'old image');
        $product = Product::factory()->for($member)->create([
            'image' => [$oldImage],
        ]);

        $response = $this->actingAs($member)->put(route('account.products.update', $product), [
            'name' => 'Updated product',
            'price' => '88.50',
            'category' => 'Shoes',
            'brand' => 'Updated Brand',
            'is_on_sale' => '1',
            'discount_percent' => '15',
            'company_profile' => 'Updated Company',
            'description' => 'Updated description',
            'images' => [$this->productImage()],
        ]);

        $response->assertRedirect(route('account.my-product'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated product',
            'price' => '88.50',
            'category' => 'Shoes',
            'brand' => 'Updated Brand',
            'is_on_sale' => 1,
            'discount_percent' => 15,
            'company_profile' => 'Updated Company',
            'description' => 'Updated description',
        ]);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($product->fresh()->image[0]);
    }

    public function test_member_cannot_create_a_product_with_discount_over_100_percent(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->from(route('account.add-product'))
            ->post(route('account.products.store'), [
                'name' => 'Invalid discount product',
                'price' => '59.00',
                'discount_percent' => 101,
            ])
            ->assertRedirect(route('account.add-product'))
            ->assertSessionHasErrors('discount_percent');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_member_can_update_product_without_replacing_its_images(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();
        $image = 'products/'.$member->id.'/existing.png';
        Storage::disk('public')->put($image, 'product image');
        $product = Product::factory()->for($member)->create(['image' => [$image]]);

        $this->actingAs($member)
            ->put(route('account.products.update', $product), [
                'name' => 'Updated name',
                'price' => '80',
                'description' => 'Updated description',
            ])
            ->assertRedirect(route('account.my-product'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated name',
        ]);
        Storage::disk('public')->assertExists($image);
    }

    public function test_member_cannot_update_another_members_product(): void
    {
        $member = User::factory()->create();
        $owner = User::factory()->create();
        $product = Product::factory()->for($owner)->create();

        $this->actingAs($member)
            ->put(route('account.products.update', $product), [
                'name' => 'Changed by another member',
                'price' => '1',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
            'user_id' => $owner->id,
        ]);
    }

    public function test_member_can_delete_their_product_and_its_images(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();
        $image = 'products/'.$member->id.'/to-delete.png';
        Storage::disk('public')->put($image, 'product image');
        $product = Product::factory()->for($member)->create(['image' => [$image]]);

        $this->actingAs($member)
            ->delete(route('account.products.destroy', $product))
            ->assertRedirect(route('account.my-product'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_member_cannot_delete_another_members_product(): void
    {
        $member = User::factory()->create();
        $owner = User::factory()->create();
        $product = Product::factory()->for($owner)->create();

        $this->actingAs($member)
            ->delete(route('account.products.destroy', $product))
            ->assertNotFound();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'user_id' => $owner->id,
        ]);
    }

    private function productImage(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'product.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aHF8AAAAASUVORK5CYII='),
        );
    }
}
