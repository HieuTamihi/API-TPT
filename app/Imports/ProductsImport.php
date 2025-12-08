<?php

namespace App\Imports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Product;

class ProductsImport implements ToCollection
{
    public $duplicates = [];

    public function collection(Collection $rows)
    {
        $rows = $rows->skip(1);
        foreach ($rows as $row) {
            $product_code = $row[0] ?? null;
            if (empty($product_code)) {
                continue;
            }
            $existingProduct = Product::where('product_code', $product_code)->first();

            if ($existingProduct) {
                $this->duplicates[] = [
                    'product_id' => $existingProduct->id, 
                    'row_data'   => $row
                ];
            } else {
                Product::create([
                    'product_code' => $product_code,
                    'product_name' => $row[1] ?? null,
                    'brand'        => $row[2] ?? null,
                    'warranty'     => $row[3] ?? 0,
                ]);
            }
        }
    }
    public function getDuplicates()
    {
        return $this->duplicates;
    }
}

