<?php

namespace App\Services;

use App\Exports\ProductsExport;
use App\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductExportService
{
    /**
     * @param  Collection<int, Product>  $products
     */
    public function pdf(Collection $products): Responsable
    {
        return Pdf::view('pdf.products', [
            'products' => $products,
        ])
            ->driver('dompdf')
            ->format('a4')
            ->download('products.pdf');
    }

    /**
     * @param  Collection<int, Product>  $products
     */
    public function excel(Collection $products): BinaryFileResponse
    {
        return Excel::download(new ProductsExport($products), 'products.xlsx');
    }
}
