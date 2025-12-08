<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Quotation;
use App\Models\quotation_form;
use App\Models\QuotationService;
use App\Models\Receiving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    private $quotations;

    public function __construct(Quotation $quotations)
    {
        $this->quotations = $quotations;
    }
    // Hiển thị danh sách báo giá
    public function index()
    {
        $quotations = Quotation::orderBy('id', 'desc')->get();
        $title = 'Danh sách báo giá';
        $customers = Customers::all();
        return view('expertise.quotations.index', compact('quotations', 'title', 'customers'));
    }

    // Hiển thị form tạo mới báo giá
    public function create()
    {
        $quoteNumber = (new Receiving)->getQuoteCount('PBG', Quotation::class, 'quotation_code');
        $title = 'Tạo phiếu báo giá';
        $excludedReceptionIds = Quotation::pluck('reception_id')->toArray();
        $receivings = Receiving::whereNotIn('id', $excludedReceptionIds)->where('status', 2)->get();
        return view('expertise.quotations.create', compact('title', 'quoteNumber', 'receivings'));
    }


    // Lưu báo giá mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        // Kiểm tra nếu form_code_receiving đã tồn tại
        if (Quotation::where('quotation_code', $request->form_code_receiving)->exists()) {
            return back()->with('warning', 'Mã phiếu báo giá đã tồn tại.');
        }
        $validated = $request->validate([
            'reception_id' => 'required|integer|unique:quotations,reception_id',
            'quotation_code' => 'required|string|unique:quotations,quotation_code',
            'customer_id' => 'required|integer',
            'address' => 'nullable|string',
            'quotation_date' => 'required|date',
            'contact_person' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'required|integer',
            'contact_phone' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'services.*.service_name' => 'required|string', // Validate từng dịch vụ
            'services.*.unit' => 'nullable|string',
            'services.*.brand' => 'nullable|string',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.unit_price' => 'required|numeric|min:0',
            'services.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'services.*.note' => 'nullable|string',
        ]);

        $quotation = Quotation::create($validated);

        if ($request->has('services')) {
            foreach ($validated['services'] as $service) {
                $total = $service['quantity'] * $service['unit_price'];
                $totalWithTax = $total + ($total * ($service['tax_rate'] ?? 0) / 100);
                QuotationService::create([
                    'quotation_id' => $quotation->id, // Liên kết với bảng `quotations`
                    'service_name' => $service['service_name'],
                    'unit' => $service['unit'] ?? null,
                    'brand' => $service['brand'] ?? null,
                    'quantity' => $service['quantity'],
                    'unit_price' => $service['unit_price'],
                    'tax_rate' => $service['tax_rate'] ?? 0,
                    'total' => $totalWithTax,
                    'note' => $service['note'] ?? null,
                ]);
            }
        }
        return redirect()->route('quotations.index')->with('msg', 'Tạo báo giá thành công!');
    }

    // Hiển thị chi tiết báo giá
    public function show(Quotation $quotation)
    {
        return view('expertise.quotations.show', compact('quotation'));
    }

    // Hiển thị form chỉnh sửa báo giá
    public function edit(Quotation $quotation)
    {
        $title = 'Chi tiết báo giá';

        // Lấy tất cả các receiving
        $receivings = Receiving::all();
        // Lấy các reception_id đã tồn tại trong bảng Quotation
        $existingReceptionIds = Quotation::pluck('reception_id')->toArray();
        // Lọc ra các receiving chưa có reception_id trong bảng Quotation
        $receivings = $receivings->whereNotIn('id', $existingReceptionIds);
        // Đảm bảo rằng reception_id của Quotation hiện tại được giữ lại
        $receivings->push(Receiving::find($quotation->reception_id));
        // Lấy chi tiết của báo giá với quan hệ 'services'
        $quotation1 = Quotation::with('services')->findOrFail($quotation->id);
        //'id' là khóa duy nhất cho mỗi dịch vụ
        $quotationServices = $quotation1->services->keyBy('id');
        $customers = Customers::all();
        $users = User::all();
        $data = Quotation::all();
        $terms = quotation_form::where('user_id', Auth::id())->first();

        return view('expertise.quotations.edit', compact('quotation', 'title', 'receivings', 'quotationServices', 'customers', 'users', 'data', 'terms'));
    }


    // Cập nhật thông tin báo giá
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'reception_id' => 'required|integer|unique:quotations,reception_id,' . $quotation->id,
            'quotation_code' => 'required|string|unique:quotations,quotation_code,' . $quotation->id,
            'customer_id' => 'required|integer',
            'address' => 'nullable|string',
            'quotation_date' => 'required|date',
            'contact_person' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'required|integer',
            'contact_phone' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'services.*.id' => 'nullable|integer',
            'services.*.service_name' => 'required|string',
            'services.*.unit' => 'nullable|string',
            'services.*.brand' => 'nullable|string',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.unit_price' => 'required|string|min:0',
            'services.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'services.*.note' => 'nullable|string',
        ]);

        // Cập nhật thông tin báo giá
        $quotation->update($validated);

        // Xoá tất cả các dịch vụ cũ của báo giá này
        $quotation->services()->delete();

        // Thêm lại tất cả các dịch vụ mới
        foreach ($validated['services'] ?? [] as $service) {
            $totalWithTax = $service['quantity'] * (float)$service['unit_price'] * (1 + ($service['tax_rate'] ?? 0) / 100);
            // Tạo dịch vụ mới
            $quotation->services()->create(array_merge($service, [
                'total' => $totalWithTax
            ]));
        }
        return redirect()->route('quotations.index')->with('msg', 'Cập nhật báo giá thành công!');
    }


    // Xóa báo giá
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        $quotation->services()->delete();
        return redirect()->route('quotations.index')->with('msg', 'Xóa báo giá và các dịch vụ thành công!');
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã phiếu: ' . $data['ma'], 'name' => 'ma-phieu', 'icon' => 'po'];
        }
        if (isset($data['receiving_code']) && $data['receiving_code'] !== null) {
            $filters[] = ['value' => 'Phiếu tiếp nhận: ' . $data['receiving_code'], 'name' => 'phieu-tiep-nhan', 'icon' => 'po'];
        }
        if (isset($data['note']) && $data['note'] !== null) {
            $filters[] = ['value' => 'Ghi chú: ' . $data['note'], 'name' => 'ghi-chu', 'icon' => 'po'];
        }
        if (isset($data['customer']) && $data['customer'] !== null) {
            $filters[] = ['value' => 'Khách hàng: ' . count($data['customer']) . ' đã chọn', 'name' => 'khách hàng', 'icon' => 'user'];
        }
        if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Ngày lập phiếu: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-lap-phieu', 'icon' => 'date'];
        }
        if (isset($data['tong_tien']) && $data['tong_tien'][1] !== null) {
            $filters[] = ['value' => 'Bảo hành: ' . $data['tong_tien'][0] . ' ' . $data['tong_tien'][1], 'name' => 'tong-tien', 'icon' => 'money'];
        }
        if (isset($data['status']) && $data['status'] !== null) {
            $statusValues = [];
            if (in_array(1, $data['status'])) {
                $statusValues[] = '<span style="color: #858585;">Bảo hành</span>';
            }
            if (in_array(2, $data['status'])) {
                $statusValues[] = '<span style="color: #08AA36BF;">Dịch vụ</span>';
            }
            if (in_array(3, $data['status'])) {
                $statusValues[] = '<span style="color:rgba(67, 54, 154, 0.75);">Bảo hành dịch vụ</span>';
            }
            $filters[] = ['value' => 'Loại phiếu: ' . implode(', ', $statusValues), 'name' => 'loai-phieu', 'icon' => 'status'];
        }

        if ($request->ajax()) {
            $quotations = $this->quotations->getQuotationAjax($data);
            return response()->json([
                'data' => $quotations,
                'filters' => $filters,
            ]);
        }
        return false;
    }
    public function saveTerms(Request $request)
    {
        $user = Auth::user();

        $exist = quotation_form::where('user_id', $user->id)->first();

        if (!$exist) {
            // Nếu chưa có, tạo mới
            quotation_form::create([
                'content' => $request->content,
                'user_id' => $user->id
            ]);
        } else {
            // Nếu có rồi, cập nhật điều khoản
            $exist->update(['content' => $request->content]);
        }

        return response()->json(['msg' => 'Lưu điều khoản thành công!']);
    }
}
