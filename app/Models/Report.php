<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    public function mergeProductData($filter, $data)
    {
        $mergedData = [];

        // Xử lý imports
        foreach ($data['imports'] as $import) {
            $productId = $import->id;
            $mergedData[$productId] = [
                'id' => $productId,
                'product_import' => $import->product_import,
                'product_export' => 0,
                'product_code' => $import->product_code,
                'product_name' => $import->product_name,
            ];
        }

        // Xử lý exports
        foreach ($data['exports'] as $export) {
            $productId = $export->id;
            if (isset($mergedData[$productId])) {
                $mergedData[$productId]['product_export'] = $export->product_export;
            } else {
                $mergedData[$productId] = [
                    'id' => $productId,
                    'product_import' => 0,
                    'product_export' => $export->product_export,
                    'product_code' => $export->product_code,
                    'product_name' => $export->product_name,
                ];
            }
        }

        // Áp dụng bộ lọc search cho product_code và product_name
        if (!empty($filter['search'])) {
            $mergedData = array_filter($mergedData, function ($item) use ($filter) {
                return strpos(strtolower($item['product_code']), strtolower($filter['search'])) !== false ||
                    strpos(strtolower($item['product_name']), strtolower($filter['search'])) !== false;
            });
        }

        // Áp dụng các bộ lọc cho product_code và product_name từ filterableFields
        $filterableFields = [
            'ma' => 'product_code',
            'ten' => 'product_name',
        ];

        foreach ($filterableFields as $key => $field) {
            if (!empty($filter[$key])) {
                $searchTerm = strtolower($filter[$key]); // Make search term case insensitive.
                $mergedData = array_filter($mergedData, function ($item) use ($searchTerm, $field) {
                    return strpos(strtolower($item[$field]), $searchTerm) !== false;
                });
            }
        }
        // Lọc theo số lượng nhập và xuất
        if (isset($filter['so_luong_nhap'][0]) && isset($filter['so_luong_nhap'][1])) {
            $operator = $filter['so_luong_nhap'][0];
            $value = $filter['so_luong_nhap'][1];

            $mergedData = array_filter($mergedData, function ($item) use ($operator, $value) {
                // So sánh với product_import
                if ($operator === '>=') {
                    return $item['product_import'] >= $value;
                } elseif ($operator === '<=') {
                    return $item['product_import'] <= $value;
                }
                return true; // Trả về true nếu không có toán tử phù hợp
            });
        }
        if (isset($filter['so_luong_xuat'][0]) && isset($filter['so_luong_xuat'][1])) {
            $operator = $filter['so_luong_xuat'][0];
            $value = $filter['so_luong_xuat'][1];
            $mergedData = array_filter($mergedData, function ($item) use ($operator, $value) {
                if ($operator === '>=') {
                    return $item['product_export'] >= $value;
                } elseif ($operator === '<=') {
                    return $item['product_export'] <= $value;
                }
            });
        }
        if (isset($filter['sort']) && is_array($filter['sort']) && count($filter['sort']) === 2) {
            $sortField = $filter['sort'][0];
            $sortOrder = strtolower($filter['sort'][1]);

            usort($mergedData, function ($a, $b) use ($sortField, $sortOrder) {
                if (!isset($a[$sortField], $b[$sortField])) {
                    return 0;
                }

                if ($a[$sortField] == $b[$sortField]) {
                    return 0;
                }

                $comparison = $a[$sortField] <=> $b[$sortField];
                return $sortOrder === 'asc' ? $comparison : -$comparison;
            });
        }
        // Trả về mảng kết quả đã lọc
        return array_values($mergedData);
    }

    public function getProductsAjax($data = null)
    {
        // Hàm xử lý điều kiện lọc theo type (tháng, quý, năm)
        $applyFilters = function ($query, $data, $dateField) {
            if (isset($data['type']) && (isset($data['month']) || isset($data['quarter']) || isset($data['year']))) {
                if ($data['type'] === 'thang' && isset($data['month']) && isset($data['year'])) {
                    $query->whereYear($dateField, $data['year'])
                        ->whereMonth($dateField, $data['month']);
                } elseif ($data['type'] === 'quy' && isset($data['quarter']) && isset($data['year'])) {
                    $startMonth = (($data['quarter'] - 1) * 3) + 1;
                    $endMonth = $startMonth + 2;
                    $query->whereYear($dateField, $data['year'])
                        ->whereBetween(DB::raw("MONTH($dateField)"), [$startMonth, $endMonth]);
                } elseif ($data['type'] === 'nam' && isset($data['year'])) {
                    $query->whereYear($dateField, $data['year']);
                }
            }
        };

        // Truy vấn nhập (giữ tất cả sản phẩm)
        $imports = DB::table('products')
            ->leftJoin('product_import', 'products.id', '=', 'product_import.product_id')
            ->leftJoin('imports', 'product_import.import_id', '=', 'imports.id')
            ->where(function ($query) use ($applyFilters, $data) {
                $applyFilters($query, $data, 'imports.date_create');
            });
            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0]);
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $imports->whereBetween('imports.date_create', [$dateStart, $dateEnd]);
            }
            $imports = $imports->select(
                'products.id',
                'products.product_code',
                'products.product_name',
                DB::raw('COUNT(product_import.product_id) as product_import')
            )
            ->groupBy('products.id', 'products.product_code', 'products.product_name')
            ->get();

        // Truy vấn xuất (giữ tất cả sản phẩm)
        $exports = DB::table('products')
            ->leftJoin('product_export', 'products.id', '=', 'product_export.product_id')
            ->leftJoin('exports', 'product_export.export_id', '=', 'exports.id')
            ->where(function ($query) use ($applyFilters, $data) {
                $applyFilters($query, $data, 'exports.date_create');
            });
            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0]);
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $exports->whereBetween('exports.date_create', [$dateStart, $dateEnd]);
            }
            $exports=$exports->select(
                'products.id',
                'products.product_code',
                'products.product_name',
                DB::raw('COUNT(product_export.product_id) as product_export')
            )
            ->groupBy('products.id', 'products.product_code', 'products.product_name')
            ->get();

        // Trả về kết quả
        return compact('imports', 'exports');
    }

    public function getAjaxReceiptReturn($data = null)
    {
        // Hàm xử lý điều kiện lọc theo type (tháng, quý, năm)
        $applyFilters = function ($query, $data, $dateField) {
            if (isset($data['type']) && (isset($data['month']) || isset($data['quarter']) || isset($data['year']))) {
                if ($data['type'] === 'thang' && isset($data['month']) && isset($data['year'])) {
                    $query->whereYear($dateField, $data['year'])
                        ->whereMonth($dateField, $data['month']);
                } elseif ($data['type'] === 'quy' && isset($data['quarter']) && isset($data['year'])) {
                    $startMonth = (($data['quarter'] - 1) * 3) + 1;
                    $endMonth = $startMonth + 2;
                    $query->whereYear($dateField, $data['year'])
                        ->whereBetween(DB::raw("MONTH($dateField)"), [$startMonth, $endMonth]);
                } elseif ($data['type'] === 'nam' && isset($data['year'])) {
                    $query->whereYear($dateField, $data['year']);
                }
            }
        };

        // Truy vấn nhập (giữ tất cả sản phẩm)
        $imports = DB::table('products')
            ->leftJoin('received_products', 'products.id', '=', 'received_products.product_id')
            ->leftJoin('receiving', 'received_products.reception_id', '=', 'receiving.id')
            ->where(function ($query) use ($applyFilters, $data) {
                $applyFilters($query, $data, 'receiving.date_created');
            });
            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0]);
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $imports->whereBetween('receiving.date_created', [$dateStart, $dateEnd]);
            }
            $imports= $imports->select(
                'products.id',
                'products.product_code',
                'products.product_name',
                DB::raw('COUNT(received_products.product_id) as product_import')
            )
            ->groupBy('products.id', 'products.product_code', 'products.product_name')
            ->get();

        // Truy vấn xuất (giữ tất cả sản phẩm)
        $exports = DB::table('products')
            ->leftJoin('product_returns', 'products.id', '=', 'product_returns.product_id')
            ->leftJoin('return_form', 'product_returns.return_form_id', '=', 'return_form.id')
            ->where(function ($query) use ($applyFilters, $data) {
                $applyFilters($query, $data, 'return_form.date_created');
            });
            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0]);
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $exports->whereBetween('return_form.date_created', [$dateStart, $dateEnd]);
            }
            $exports=$exports->select(
                'products.id',
                'products.product_code',
                'products.product_name',
                DB::raw('COUNT(product_returns.product_id) as product_export')
            )
            ->groupBy('products.id', 'products.product_code', 'products.product_name')
            ->get();

        // Trả về kết quả
        return compact('imports', 'exports');
    }


    public function countReceiptReturn($data)
    {
        $totals = [
            'total_import' => 0,
            'total_export' => 0,
        ];

        foreach ($data as $item) {
            $totals['total_import'] += $item['product_import'] ?? 0;
            $totals['total_export'] += $item['product_export'] ?? 0;
        }

        return $totals;
    }

    public function getAjaxRPQuotation($data = null)
    {
        // Hàm xử lý điều kiện lọc theo type (tháng, quý, năm)
        $applyFilters = function ($query, $data, $dateField) {
            if (isset($data['type']) && (isset($data['month']) || isset($data['quarter']) || isset($data['year']))) {
                if ($data['type'] === 'thang' && isset($data['month']) && isset($data['year'])) {
                    $query->whereYear($dateField, $data['year'])
                        ->whereMonth($dateField, $data['month']);
                } elseif ($data['type'] === 'quy' && isset($data['quarter']) && isset($data['year'])) {
                    $startMonth = (($data['quarter'] - 1) * 3) + 1;
                    $endMonth = $startMonth + 2;
                    $query->whereYear($dateField, $data['year'])
                        ->whereBetween(DB::raw("MONTH($dateField)"), [$startMonth, $endMonth]);
                } elseif ($data['type'] === 'nam' && isset($data['year'])) {
                    $query->whereYear($dateField, $data['year']);
                }
            }
        };
        $quotations = Quotation::join('receiving', 'receiving.id', 'quotations.reception_id')
            ->join('return_form', 'return_form.reception_id', 'receiving.id')
            ->join('customers', 'customers.id', 'quotations.customer_id')
            ->where(function ($query) use ($applyFilters, $data) {
                $applyFilters($query, $data, 'quotations.quotation_date');
            })
            ->select('quotations.*', 'receiving.form_code_receiving as form_code_receiving', 'receiving.status as status_recei', 'return_form.status', 'customers.customer_name');
        if (!empty($data['search'])) {
            $quotations->where(function ($query) use ($data) {
                $query->where('quotation_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('form_code_receiving', 'like', '%' . $data['search'] . '%');
            });
        }
        $filterableFields = [
            'ma' => 'quotation_code',
            'receiving_code' => 'form_code_receiving',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $quotations->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (isset($data['customer'])) {
            $quotations = $quotations->whereIn('quotations.customer_id', $data['customer']);
        }
        if (isset($data['status'])) {
            $quotations = $quotations->whereIn('receiving.status', $data['status']);
        }
        if (!empty($data['date'][0]) && !empty($data['date'][1])) {
            $dateStart = Carbon::parse($data['date'][0]);
            $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
            $quotations->whereBetween('quotations.quotation_date', [$dateStart, $dateEnd]);
        }
        if (isset($data['tong_tien'][0]) && isset($data['tong_tien'][1])) {
            $quotations = $quotations->where('quotations.total_amount', $data['tong_tien'][0], $data['tong_tien'][1]);
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $quotations = $quotations->orderBy($data['sort'][0], $data['sort'][1]);
        }
        return $quotations = $quotations->get();
    }
    public function statusCountTotal($quotations)
    {
        $statusCounts = $quotations->groupBy('status_recei')->map(function ($group) {
            return $group->count();
        });

        $totalAmounts = $quotations->groupBy('status_recei')->map(function ($group) {
            return $group->sum('total_amount');
        });
        return compact('statusCounts', 'totalAmounts');
    }
}
