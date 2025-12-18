<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\warrantyHistory;
use App\Models\warrantyLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WarrantyLookupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $warrantyLookup;
    public function __construct(warrantyLookup $warrantyLookup)
    {
        $this->warrantyLookup = $warrantyLookup;
    }
    public function index()
    {
        $title = "Tra cứu bảo hành";

        // Sử dụng pagination trực tiếp từ database để tối ưu performance
        $warranty = warrantyLookup::with(['product', 'serialNumber', 'customer', 'warrantyHistories.receiving'])
            ->orderby('id', 'DESC')
            ->paginate(25);

        // Group dữ liệu cho trang hiện tại
        $grouped = $warranty->groupBy(function ($item) {
            return $item->sn_id . '_' . $item->export_id; // group theo cặp sn_id + export_id
        })->map(function ($items) {
            $first = $items->first()->replicate();

            $first->name_warranty = $items->filter(function ($item) {
                return !empty($item->name_warranty);
            })->map(function ($item) {
                $warrantyText = $item->warranty == 0 ? 'không bảo hành' : $item->warranty . ' tháng';
                return $item->name_warranty . ": " . $warrantyText;
            })->join('| ');

            $first->status_string = $items->map(function ($item) {
                if ($item->status == 0) {
                    $statusText = 'Còn bảo hành';
                } elseif ($item->status == 1) {
                    $statusText = 'Hết bảo hành';
                } elseif ($item->status == 2) {
                    $statusText = 'Bảo hành DV';
                } else {
                    $statusText = 'Không xác định';
                }

                if (!empty($item->name_expire_date)) {
                    return $item->name_expire_date . ": " . $statusText;
                } elseif (!empty($item->name_warranty)) {
                    return $item->name_warranty . ": " . $statusText;
                }

                return null;
            })->filter()->join('| ');

            $first->name_expire_date = $items->map(function ($item) {
                if (!empty($item->name_expire_date)) {
                    return $item->name_expire_date . ": " . $item->warranty_extra . " tháng";
                }
                return null;
            })->filter()->join('| ');

            return $first;
        });

        // Tạo paginator mới với grouped data
        $grouped = $grouped->values();
        $originalWarranty = $warranty; // Lưu pagination gốc
        $warranty = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped,
            $originalWarranty->total(), // Sử dụng total từ pagination gốc
            $originalWarranty->perPage(),
            $originalWarranty->currentPage(),
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );


        $customers = Customers::all();
        return view('expertise.warrantyLookup.index', compact('title', 'warranty', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(warrantyLookup $warrantyLookup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $title = "Tra cứu bảo hành";
        $warrantyLookup = warrantyLookup::with(['product', 'warrantyHistories'])
            ->where("sn_id", $id)->first();
        // dd($grouped);
        $warrantyHistory = warrantyHistory::with(['warrantyLookup', 'receiving', 'returnForm', 'productReturn'])
            ->whereHas('warrantyLookup', function ($query) use ($id) {
                $query->where('sn_id', $id);
            })
            ->orderBy('id', 'desc')
            ->get();

        // dd($warrantyHistory);
        // $warrantyHistory = $grouped->warrantyHistories;
        return view('expertise.warrantyLookup.edit', compact('title', 'warrantyLookup', 'warrantyHistory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, warrantyLookup $warrantyLookup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(warrantyLookup $warrantyLookup)
    {
        //
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã hàng: ' . $data['ma'], 'name' => 'ma-hang', 'icon' => 'po'];
        }
        if (isset($data['ten']) && $data['ten'] !== null) {
            $filters[] = ['value' => 'Tên hàng: ' . $data['ten'], 'name' => 'ten-hang', 'icon' => 'product'];
        }
        if (isset($data['brand']) && $data['brand'] !== null) {
            $filters[] = ['value' => 'Hãng: ' . $data['brand'], 'name' => 'hang', 'icon' => 'brand'];
        }
        if (isset($data['sn']) && $data['sn'] !== null) {
            $filters[] = ['value' => 'Serial: ' . $data['sn'], 'name' => 'serial', 'icon' => 'sn'];
        }
        if (isset($data['customer']) && $data['customer'] !== null) {
            $filters[] = ['value' => 'Khách hàng: ' . count($data['customer']) . ' đã chọn', 'name' => 'khach-hang', 'icon' => 'customer'];
        }
        if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Ngày xuất hàng: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-xuat-hang', 'icon' => 'date'];
        }
        if (isset($data['date_expired']) && $data['date_expired'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date_expired'][0]));
            $date_end = date("d/m/Y", strtotime($data['date_expired'][1]));
            $filters[] = ['value' => 'Ngày kích hoạt BHDV: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-kich-hoat-bhdv', 'icon' => 'date'];
        }
        if (isset($data['status']) && $data['status'] !== null) {
            $statusValues = [];
            if (in_array(0, $data['status'])) {
                $statusValues[] = '<span style="color: #858585;">Còn bảo hành</span>';
            }
            if (in_array(1, $data['status'])) {
                $statusValues[] = '<span style="color: #08AA36BF;">Hết bảo hành</span>';
            }
            $filters[] = ['value' => 'Tình trạng: ' . implode(', ', $statusValues), 'name' => 'tinh-trang', 'icon' => 'status'];
        }
        if (isset($data['bao_hanh']) && $data['bao_hanh'][1] !== null) {
            $filters[] = ['value' => 'Bảo hành: ' . $data['bao_hanh'][0] . ' ' . $data['bao_hanh'][1], 'name' => 'bao-hanh', 'icon' => 'money'];
        }
        if (isset($data['bao_hanh_dich_vu']) && $data['bao_hanh_dich_vu'][1] !== null) {
            $filters[] = ['value' => 'Bảo hành dịch vụ: ' . $data['bao_hanh_dich_vu'][0] . ' ' . $data['bao_hanh_dich_vu'][1], 'name' => 'bao-hanh-dich-vu', 'icon' => 'money'];
        }

        if ($request->ajax()) {
            $warrantyLookup = $this->warrantyLookup->getWarranAjax($data);
            return response()->json([
                'data' => $warrantyLookup->items(),
                'pagination' => [
                    'current_page' => $warrantyLookup->currentPage(),
                    'last_page' => $warrantyLookup->lastPage(),
                    'per_page' => $warrantyLookup->perPage(),
                    'total' => $warrantyLookup->total(),
                    'from' => $warrantyLookup->firstItem(),
                    'to' => $warrantyLookup->lastItem(),
                ],
                'filters' => $filters,
            ]);
        }
        return false;
    }

    /**
     * API Check bảo hành (Đã tích hợp)
     */
    public function checkWarranty(Request $request)
    {
        // 1. Validate: Yêu cầu bắt buộc có 'keyword'
        $request->validate([
            'keyword' => 'required|string'
        ]);

        $keyword = trim($request->keyword);

        // 2. Query tìm kiếm: Tìm trong bảng Serial HOẶC bảng Product
        $warranties = WarrantyLookup::query()
            ->with([
                'product:id,product_name,product_code', // Nhớ dùng đúng tên cột
                'serialNumber:id,serial_code',          // Nhớ dùng đúng tên cột
                'customer:id,customer_name'
            ])
            ->where(function ($query) use ($keyword) {
                // Điều kiện 1: Trùng Serial Code
                $query->whereHas('serialNumber', function ($q) use ($keyword) {
                    $q->where('serial_code', $keyword);
                })
                    // Điều kiện 2: HOẶC Trùng Product Code
                    ->orWhereHas('product', function ($q) use ($keyword) {
                        $q->where('product_code', $keyword);
                    });
            })
            ->orderBy('created_at', 'desc') // Sắp xếp mới nhất lên đầu
            ->get();

        // 3. Nếu không tìm thấy dữ liệu nào
        if ($warranties->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin bảo hành cho từ khóa: ' . $keyword
            ], 404);
        }

        // 4. Nhóm kết quả theo Serial Number (sn_id)
        // Lý do: Nếu tìm theo Mã SP, sẽ có nhiều thiết bị khác nhau.
        // Mỗi thiết bị cần gom lịch sử lại thành một cụm.
        $groupedData = $warranties->groupBy('sn_id');

        // 5. Format dữ liệu trả về
        $result = $groupedData->map(function ($items) {
            // Lấy thông tin chung từ bản ghi mới nhất của serial này
            $info = $items->first();

            return [
                'device_info' => [
                    'serial_code'  => $info->serialNumber->serial_code ?? 'N/A',
                    'product_name' => $info->product->product_name ?? 'N/A',
                    'product_code' => $info->product->product_code ?? 'N/A',
                    'customer'     => $info->customer->customer_name ?? 'Khách lẻ',
                ],
                'history' => $items->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'warranty_name' => $item->name_warranty,
                        'status_text'   => $this->mapStatus($item->status),
                        'start_date'    => $item->export_return_date,
                        'end_date'      => $item->warranty_expire_date,
                        'is_active'     => $item->warranty_expire_date > now(), // Kiểm tra còn hạn
                    ];
                })->values() // Reset key array
            ];
        })->values(); // Reset key array của group

        return response()->json([
            'success' => true,
            'search_keyword' => $keyword,
            'total_devices_found' => $result->count(),
            'data' => $result
        ]);
    }

    // Hàm phụ để chuyển đổi trạng thái số sang chữ
    private function mapStatus($status)
    {
        return match ((int)$status) {
            1 => 'Nhập hàng (Trong kho)',
            2 => 'Xuất hàng',
            3 => 'Tiếp nhận',
            4 => 'Trả hàng',
            5 => 'Đang mượn',
            default => 'Không xác định',
        };
    }

    /**
     * Danh sách bảo hành + tìm kiếm + lọc + sắp xếp + phân trang + group theo S/N
     */
    public function list(Request $request): JsonResponse
    {
        $inputData = [];

        // Tìm kiếm chung
        if ($request->filled('search')) {
            $inputData['search'] = $request->search;
        }

        // Lọc chi tiết
        if ($request->filled('ma'))     $inputData['ma'] = $request->ma;
        if ($request->filled('ten'))    $inputData['ten'] = $request->ten;
        if ($request->filled('brand'))  $inputData['brand'] = $request->brand;
        if ($request->filled('sn'))     $inputData['sn'] = $request->sn;

        // Lọc khách hàng (mảng id)
        if ($request->filled('customer')) {
            $inputData['customer'] = $request->customer;
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            $inputData['status'] = $request->status;
        }

        // Lọc ngày bán (export_return_date)
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $inputData['date'] = [$request->get('date_from'), $request->get('date_to')];
        }

        // Lọc ngày hết hạn bảo hành (return_date)
        if ($request->filled('date_expired_from') || $request->filled('date_expired_to')) {
            $inputData['date_expired'] = [
                $request->get('date_expired_from'),
                $request->get('date_expired_to')
            ];
        }

        // Lọc thời gian bảo hành (tháng)
        if ($request->filled('warranty_min') || $request->filled('warranty_max')) {
            $inputData['bao_hanh'] = [
                $request->get('warranty_min', 0),
                $request->get('warranty_max', 999)
            ];
        }

        // Lọc bảo hành dịch vụ (tháng)
        if ($request->filled('service_min') || $request->filled('service_max')) {
            $inputData['bao_hanh_dich_vu'] = [
                $request->get('service_min', 0),
                $request->get('service_max', 999)
            ];
        }

        // Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortMap = [
                'code'       => 'products.product_code',
                'brand'      => 'products.brand',
                'serial'     => 'serial_numbers.serial_code',
                'customer'   => 'customers.customer_name',
                'sell_date'  => 'warranty_lookup.export_return_date',
            ];
            $field = $sortMap[$request->sort_by] ?? 'warranty_lookup.id';
            $inputData['sort'] = [$field, $request->sort_dir];
        }

        // Query giống method getWarranAjax
        $query = warrantyLookup::join('products', 'products.id', '=', 'warranty_lookup.product_id')
            ->join('serial_numbers', 'serial_numbers.id', '=', 'warranty_lookup.sn_id')
            ->join('customers', 'customers.id', '=', 'warranty_lookup.customer_id')
            ->select(
                'warranty_lookup.*',
                'serial_numbers.serial_code as sericode',
                'serial_numbers.id as serial_id',
                'products.product_code',
                'products.product_name',
                'products.brand',
                'customers.customer_name as customername'
            );

        // Tìm kiếm chung
        if (!empty($inputData['search'])) {
            $search = $inputData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('products.product_code', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%")
                    ->orWhere('products.brand', 'like', "%{$search}%")
                    ->orWhere('customers.customer_name', 'like', "%{$search}%")
                    ->orWhere('serial_numbers.serial_code', 'like', "%{$search}%")
                    ->orWhere('warranty_lookup.name_warranty', 'like', "%{$search}%")
                    ->orWhere('warranty_lookup.name_expire_date', 'like', "%{$search}%");
            });
        }

        // Lọc chi tiết
        $filterable = [
            'ma'    => 'products.product_code',
            'ten'   => 'products.product_name',
            'brand' => 'products.brand',
            'sn'    => 'serial_numbers.serial_code',
        ];
        foreach ($filterable as $key => $field) {
            if (!empty($inputData[$key])) {
                $query->where($field, 'like', "%{$inputData[$key]}%");
            }
        }

        // Lọc khách hàng
        if (!empty($inputData['customer'])) {
            $query->whereIn('warranty_lookup.customer_id', $inputData['customer']);
        }

        // Lọc trạng thái
        if (!empty($inputData['status'])) {
            $query->whereIn('warranty_lookup.status', $inputData['status']);
        }

        // Lọc ngày bán
        if (!empty($inputData['date'][0]) && !empty($inputData['date'][1])) {
            $start = Carbon::parse($inputData['date'][0])->startOfDay();
            $end = Carbon::parse($inputData['date'][1])->endOfDay();
            $query->whereBetween('warranty_lookup.export_return_date', [$start, $end]);
        }

        // Lọc ngày hết hạn
        if (!empty($inputData['date_expired'][0]) && !empty($inputData['date_expired'][1])) {
            $start = Carbon::parse($inputData['date_expired'][0])->startOfDay();
            $end = Carbon::parse($inputData['date_expired'][1])->endOfDay();
            $query->whereBetween('warranty_lookup.return_date', [$start, $end]);
        }

        // Lọc thời gian bảo hành
        if (isset($inputData['bao_hanh'])) {
            $query->whereBetween('warranty_lookup.warranty', $inputData['bao_hanh']);
        }

        // Lọc bảo hành dịch vụ
        if (isset($inputData['bao_hanh_dich_vu'])) {
            $query->whereBetween('warranty_lookup.warranty_extra', $inputData['bao_hanh_dich_vu']);
        }

        // Sắp xếp
        if (isset($inputData['sort'])) {
            $query->orderBy($inputData['sort'][0], $inputData['sort'][1]);
        } else {
            $query->orderBy('warranty_lookup.id', 'desc');
        }

        // === PHÂN TRANG (Logic Mới) ===
        $perPage = $request->get('per_page', 20);
        $warranties = $query->paginate($perPage); // Paginate chuẩn của Laravel

        // Map dữ liệu từng dòng (Không Group)
        $data = $warranties->getCollection()->map(function ($item) {

            // Tính hạn bảo hành cho dòng này
            $expiryStatus = '';
            $isExpired = true;

            if ($item->warranty_expire_date) {
                $expireDate = Carbon::parse($item->warranty_expire_date);
                if ($expireDate->isFuture()) {
                    $diff = Carbon::now()->diff($expireDate);
                    $expiryStatus = "Còn {$diff->y} năm {$diff->m} tháng {$diff->d} ngày";
                    $isExpired = false;
                } else {
                    $diff = $expireDate->diff(Carbon::now());
                    $expiryStatus = "Hết hạn ({$diff->m} tháng {$diff->d} ngày trước)";
                }
            } else {
                $expiryStatus = "Không có thời hạn";
            }

            return [
                'id'               => $item->id, // ID duy nhất của dòng bảo hành
                'code'             => $item->product_code,
                'brand'            => $item->brand,
                'serial'           => $item->sericode,
                'customer'         => $item->customername,
                'sell_date'        => $item->export_return_date ? Carbon::parse($item->export_return_date)->format('d/m/Y') : '',

                // Thông tin cụ thể của dòng này
                'warranty_info'    => "{$item->name_warranty} ({$item->warranty} tháng)",

                'expiry_status'    => $expiryStatus,
                'is_expired'       => $isExpired,
                'activation_date'  => '', // Hoặc logic riêng nếu có
                'service_warranty' => $item->warranty_extra ? "BH thêm: {$item->warranty_extra} tháng" : '',

                // Trạng thái hiển thị
                'status'           => $isExpired ? 'Hết bảo hành' : 'Còn bảo hành',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'current_page' => $warranties->currentPage(),
                'last_page'    => $warranties->lastPage(),
                'per_page'     => $warranties->perPage(),
                'total'        => $warranties->total(), // Số lượng bản ghi thực tế (sẽ là 7)
            ]
        ]);
    }

    /**
     * Chi tiết một serial (tất cả bảo hành của S/N đó)
     */
    public function detail($serial_id): JsonResponse
    {
        $items = warrantyLookup::with(['product', 'serialNumber', 'customer'])
            ->where('sn_id', $serial_id)
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }

        // Gộp dữ liệu giống list
        $first = $items->first();
        $warrantyInfo = $items->map(fn($i) => "{$i->name_warranty}: {$i->warranty} tháng")->join(' | ');
        $statusText = $items->map(fn($i) => "{$i->name_warranty}: " . ($i->status == 1 ? 'Hết bảo hành' : 'Còn bảo hành'))->join(' | ');

        return response()->json([
            'success' => true,
            'data' => [
                'serial_id'        => $serial_id,
                'serial_code'      => $first->serialNumber->serial_code,
                'product_code'     => $first->product->product_code,
                'product_name'     => $first->product->product_name,
                'brand'            => $first->product->brand,
                'customer'         => $first->customer->customer_name,
                'sell_date'        => $first->export_return_date ? Carbon::parse($first->export_return_date)->format('d/m/Y') : '',
                'warranty_info'    => $warrantyInfo,
                'status'           => $statusText,
                'all_records'      => $items, // nếu cần chi tiết từng dòng
            ]
        ]);
    }

    /**
     * Tạo mới bảo hành (thường là khi xuất hàng)
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'sn_id'              => 'required|exists:serial_numbers,id',
            'customer_id'        => 'required|exists:customers,id',
            'export_return_date' => 'required|date',
            'warranty'           => 'required|integer',
            'name_warranty'      => 'required|string',
            'warranty_extra'     => 'nullable|integer',
            'status'             => 'required|integer',
        ]);

        $warranty = warrantyLookup::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Thêm bảo hành thành công',
            'data'    => $warranty
        ], 201);
    }

    /**
     * Cập nhật bảo hành
     */
    public function change(Request $request, $id): JsonResponse
    {
        $warranty = warrantyLookup::findOrFail($id);

        $request->validate([
            'warranty'           => 'required|integer',
            'name_warranty'      => 'required|string',
            'warranty_extra'     => 'nullable|integer',
            'status'             => 'required|integer',
            'return_date'        => 'nullable|date',
        ]);

        $warranty->update($request->only([
            'warranty',
            'name_warranty',
            'warranty_extra',
            'status',
            'return_date'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bảo hành thành công',
            'data'    => $warranty
        ]);
    }

    /**
     * Xóa bảo hành
     */
    public function delete($id): JsonResponse
    {
        $warranty = warrantyLookup::findOrFail($id);
        $warranty->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bảo hành thành công'
        ]);
    }

    /**
     * Lấy danh sách khách hàng để filter
     */
    public function customers(): JsonResponse
    {
        $customers = Customers::select('id', 'customer_name', 'customer_code')
            ->orderBy('customer_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }
}
