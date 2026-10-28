<?php

namespace App\Http\Controllers;


use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class AccountProductController extends Controller
{
    public function index(Request $request): View
    {
        return view('frontend.account.products', [
            'products' => $request->user()->products()
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('frontend.account.product-form', [
            'product' => new Product,
            'title' => 'Add Product',
            'categories' => Product::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'brands' => Product::query()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand'),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedImages = $validated['images'] ?? [];
        unset($validated['images']);

        $validated['image'] = $this->storeImages($uploadedImages, (int) $request->user()->id);
        $request->user()->products()->create($validated);

        return redirect()->route('account.my-product')->with('success', 'Thêm sản phẩm thành công.');
    }

    public function edit(Request $request, int $product): View
    {
        return view('frontend.account.product-form', [
            'product' => $this->findUserProduct($request, $product),
            'title' => 'Edit Product',
            'categories' => Product::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'brands' => Product::query()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand'),
        ]);
    }


    public function update(UpdateProductRequest $request, int $product): RedirectResponse
    {
        $product = $this->findUserProduct($request, $product);
        $validated = $request->validated();

        $uploadedImages = $validated['images'] ?? [];
        $imagesToDelete = $validated['delete_images'] ?? [];

        unset($validated['images'], $validated['delete_images']);

        $oldImages = $product->imagePaths();
        $invalidImages = array_diff($imagesToDelete, $oldImages);

        if ($invalidImages !== []) {
            throw ValidationException::withMessages([
                'delete_images' => 'Có ảnh được chọn không thuộc sản phẩm này.',
            ]);
        }

        $remainingImages = array_values(array_diff($oldImages, $imagesToDelete));

        if (count($remainingImages) + count($uploadedImages) > 3) {
            throw ValidationException::withMessages([
                'images' => 'Tổng số ảnh của sản phẩm không được vượt quá 3.',
            ]);
        }

        $newImages = $uploadedImages !== []
            ? $this->storeImages($uploadedImages, (int) $request->user()->id)
            : [];

        $validated['image'] = array_merge($remainingImages, $newImages);
        $product->update($validated);

        $this->deleteStoredImages($imagesToDelete, (int) $request->user()->id);

        return redirect()->route('account.my-product')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $product = $this->findUserProduct($request, $product);
        $this->deleteStoredImages($product->imagePaths(), (int) $request->user()->id);
        $product->delete();

        return redirect()->route('account.my-product')->with('success', 'Xóa sản phẩm thành công.');
    }

    private function findUserProduct(Request $request, int $productId): Product
    {
        return $request->user()->products()->findOrFail($productId);
    }

    /**
     * @param  array<int, UploadedFile>  $uploadedImages
     * @return array<int, string>
     */
    private function storeImages(array $uploadedImages, int $userId): array
    {
        $paths = [];

        foreach ($uploadedImages as $uploadedImage) {
            $path = $uploadedImage->store('products/' . $userId, 'public');

            if ($path === false) {
                if ($paths !== [] && !Storage::disk('public')->delete($paths)) {
                    throw new RuntimeException('Unable to clean up product images after a failed upload.');
                }

                throw new RuntimeException('Unable to store an uploaded product image.');
            }

            $paths[] = $path;
        }

        return $paths;
    }

    /**
     * @param  array<int, string>  $imagePaths
     */
    private function deleteStoredImages(array $imagePaths, int $userId): void
    {
        $ownedImagePaths = array_values(array_filter(
            $imagePaths,
            fn(string $path): bool => str_starts_with($path, 'products/' . $userId . '/')
                && !str_contains($path, '..'),
        ));

        if ($ownedImagePaths !== [] && !Storage::disk('public')->delete($ownedImagePaths)) {
            throw new RuntimeException('Unable to delete product images.');
        }
    }
}
