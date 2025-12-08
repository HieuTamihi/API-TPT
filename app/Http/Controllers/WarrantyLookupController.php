<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\warrantyHistory;
use App\Models\warrantyLookup;
use Illuminate\Http\Request;

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
}
