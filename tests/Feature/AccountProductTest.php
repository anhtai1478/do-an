<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_my_products(): void
    {
        $this->get(route('account.my-product'))
            ->assertRedirect(route('login'));
    }

    public function test_member_sees_empty_message_when_they_have_no_products(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('account.my-product'))
            ->assertOk()
            ->assertViewIs('frontend.account.products')
            ->assertSeeText('Bạn chưa có mặt hàng nào.');
    }

    public function test_member_sees_only_their_products_and_the_first_image(): void
    {
        $member = User::factory()->create();
        $otherMember = User::factory()->create();
        Product::factory()->for($member)->create([
            'name' => 'Áo khoác',
            'price' => 1000,
            'image' => ['products/coat-front.jpg', 'products/coat-back.jpg'],
        ]);
        Product::factory()->for($otherMember)->create([
            'name' => 'Sản phẩm của người khác',
        ]);

        $this->actingAs($member)
            ->get(route('account.my-product'))
            ->assertOk()
            ->assertSeeText('Áo khoác')
            ->assertSee('table-responsive cart_info', false)
            ->assertSee('table-condensed account-products-table', false)
            ->assertSee('<td class="cart_product image">', false)
            ->assertSee('storage/products/coat-front.jpg', false)
            ->assertDontSee('storage/products/coat-back.jpg', false)
            ->assertDontSeeText('Sản phẩm của người khác')
            ->assertSeeText('$1,000.00')
            ->assertSeeText('Edit')
            ->assertSeeText('Delete')
            ->assertSee('account-products-actions', false)
            ->assertSee('account-products-delete-form', false);
    }

    public function test_member_sees_discounted_price_when_product_is_on_sale(): void
    {
        $member = User::factory()->create();
        Product::factory()->for($member)->create([
            'name' => 'Discounted product',
            'price' => '1000.00',
            'is_on_sale' => true,
            'discount_percent' => 25,
        ]);

        $this->actingAs($member)
            ->get(route('account.my-product'))
            ->assertOk()
            ->assertSeeText('$1,000.00')
            ->assertSeeText('-25%')
            ->assertSeeText('$750.00')
            ->assertSee('account-products-original-price', false)
            ->assertSee('account-products-sale-price', false);
    }

    public function test_member_sees_regular_price_when_product_is_not_on_sale(): void
    {
        $member = User::factory()->create();
        Product::factory()->for($member)->create([
            'name' => 'Regular product',
            'price' => '1000.00',
            'is_on_sale' => false,
            'discount_percent' => 25,
        ]);

        $this->actingAs($member)
            ->get(route('account.my-product'))
            ->assertOk()
            ->assertSeeText('$1,000.00')
            ->assertDontSeeText('-25%')
            ->assertDontSee('account-products-sale-price', false);
    }
}
