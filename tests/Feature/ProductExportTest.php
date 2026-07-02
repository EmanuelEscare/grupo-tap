<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ProductExportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ProductExportTest extends TestCase
{
    public function test_it_exports_products_to_pdf(): void
    {
        $response = app(ProductExportService::class)
            ->pdf($this->products())
            ->toResponse(request());

        TestResponse::fromBaseResponse($response)
            ->assertSuccessful()
            ->assertDownload('products.pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_it_exports_products_to_excel(): void
    {
        $response = app(ProductExportService::class)
            ->excel($this->products());

        TestResponse::fromBaseResponse($response)
            ->assertSuccessful()
            ->assertDownload('products.xlsx');

        $path = $response->getFile()->getPathname();

        $this->assertFileExists($path);
        $this->assertSame('PK', file_get_contents($path, false, null, 0, 2));

        @unlink($path);
    }

    /**
     * @return Collection<int, Product>
     */
    private function products(): Collection
    {
        return collect([
            new Product([
                'code' => 'PROD-000001',
                'name' => 'Producto de prueba',
                'brand' => 'Marca Uno',
                'price' => 99.99,
                'created_at' => Carbon::parse('2026-07-01 10:30:00'),
            ]),
        ]);
    }
}
