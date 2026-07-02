<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\AuditLogService;
use App\Services\ProductExportService;
use App\Support\ApiResponse;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->orderByDesc('created_at')
            ->paginate(15);

        return ApiResponse::success('Products retrieved', [
            'items' => ProductListResource::collection($products->getCollection())->resolve($request),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = Product::query()->create($request->validated());

        return ApiResponse::success(
            'Product created',
            ProductResource::make($product)->resolve($request),
            201
        );
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $product = $this->findProduct($id);

        if (! $product) {
            return ApiResponse::error('Product not found', [], 404);
        }

        return ApiResponse::success(
            'Product retrieved',
            ProductResource::make($product)->resolve($request)
        );
    }

    public function update(ProductUpdateRequest $request, string $id, AuditLogService $auditLogs): JsonResponse
    {
        $product = $this->findProduct($id);

        if (! $product) {
            return ApiResponse::error('Product not found', [], 404);
        }

        $before = $auditLogs->productSnapshot($product);

        $product->fill($request->validated());
        $product->save();
        $product->refresh();

        $auditLogs->recordProductChange(
            $product,
            'update',
            $before,
            $auditLogs->productSnapshot($product),
            $request->user()
        );

        return ApiResponse::success(
            'Product updated',
            ProductResource::make($product)->resolve($request)
        );
    }

    public function destroy(Request $request, string $id, AuditLogService $auditLogs): JsonResponse
    {
        $product = $this->findProduct($id);

        if (! $product) {
            return ApiResponse::error('Product not found', [], 404);
        }

        $auditLogs->recordProductChange(
            $product,
            'delete',
            $auditLogs->productSnapshot($product),
            null,
            $request->user()
        );

        $product->delete();

        return ApiResponse::success('Product deleted');
    }

    public function exportPdf(ProductExportService $exports): Responsable
    {
        $products = Product::query()
            ->orderBy('created_at')
            ->get();

        return $exports->pdf($products);
    }

    public function exportExcel(ProductExportService $exports): BinaryFileResponse
    {
        $products = Product::query()
            ->orderBy('created_at')
            ->get();

        return $exports->excel($products);
    }

    private function findProduct(string $id): ?Product
    {
        return Product::query()->find($id);
    }
}
