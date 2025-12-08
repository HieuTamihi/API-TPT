<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Product;
use App\Models\ReceivedProduct;
use App\Models\Receiving;
use App\Models\ReturnForm;
use App\Models\SerialNumber;
use App\Models\User;
use App\Models\warrantyLookup;
use App\Models\WarrantyReceived;
use App\Notifications\ReceiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    // Display a listing of the receiving records

    private $receivings;

    public function __construct()
    {
        $this->receivings = new Receiving();
    }

    public function index()
    {
        $receivings = Receiving::orderBy('id', 'desc')->get();
        $title = 'Phiếu tiếp nhận';
        $customers = Customers::all();
        return view('expertise.receivings.index', compact('receivings', 'title', 'customers'));
    }

    // Show the form for creating a new receiving record
    public function create()
    {
        $title = 'Tạo phiếu tiếp nhận';
        $products = Product::all();
        $quoteNumber = (new Receiving)->getQuoteCount('PTN', Receiving::class, 'form_code_receiving');
        $customers = Customers::all();
        $products = Product::all();
        return view('expertise.receivings.create', compact('title', 'products', 'quoteNumber', 'customers', 'products'));
    }

    // Store a newly created receiving record in storage
    public function store(Request $request)
    {
        // Kiểm tra nếu form_code_receiving đã tồn tại
        if (Receiving::where('form_code_receiving', $request->form_code_receiving)->exists()) {
            return back()->with('warning', 'Mã phiếu tiếp nhận đã tồn tại.');
        }
        $validated = $request->validate([
            'form_type' => 'required|min:1',
            'form_type.*' => 'in:1,2,3',
            'form_code_receiving' => 'required|string|unique:receiving,form_code_receiving',
            'customer_id' => 'required|integer',
            'address' => 'nullable|string',
            'date_created' => 'required|date',
            'contact_person' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'required|integer',
            'phone' => 'nullable|string',
            'closed_at' => 'nullable|date',
            'status' => 'nullable|integer',
            'state' => 'nullable|integer',
        ]);
        $validated['branch_id'] = 1;
        // dd($request->all());

        // Tạo phiếu tiếp nhận
        $receiving = Receiving::create($validated);
        Artisan::call('receiving:update-status');
        DB::beginTransaction();
        try {
            foreach ($request->input('product_id') as $product) {
                $serialId = null;
                $existSeri = SerialNumber::where('serial_code', $product['serial'])->first();
                // Nếu serial_id rỗng, tạo serial mới với status = 6
                if (!$existSeri) {
                    $newSerial = SerialNumber::create([
                        'product_id' => $product['product_id'],
                        'status' => 2,
                        'serial_code' => $product['serial'],
                    ]);
                    $serialId = $newSerial->id;
                } else {
                    $serialId = $existSeri->id;
                }
                $receivedProduct = ReceivedProduct::create([
                    'reception_id' => $receiving->id,
                    'product_id' => $product['product_id'],
                    'quantity' => 1,
                    'serial_id' => $serialId,
                    'status' => 0,
                    'note' => '',
                ]);

                // Lưu thông tin bảo hành cho từng serial
                $hasInsertedFull = false;
                foreach ($product['id_seri'] as $index => $serial) {
                    $nameWarranty = $product['name_warranty'][$index] ?? 'Toàn bộ';

                    // Nếu là "Toàn bộ" và đã insert rồi thì bỏ qua
                    if ($nameWarranty === 'Toàn bộ') {
                        if ($hasInsertedFull) {
                            continue;
                        }
                        $hasInsertedFull = true;
                    }

                    // Nếu có bảo hành thì insert
                    if ($nameWarranty != null) {
                        WarrantyReceived::create([
                            'product_received_id' => $receivedProduct->id,
                            'name_warranty' => $nameWarranty,
                            'state_recei' => $product['warranty'][$index] ?? null,
                            'note' => $product['note_seri'][$index] ?? null,
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return redirect()->route('receivings.index')->with('msg', 'Tạo phiếu tiếp nhận thành công.');
    }


    // Display the specified receiving record
    public function show(Receiving $receiving)
    {
        return view('expertise.receivings.show', compact('receiving'));
    }

    // Show the form for editing the specified receiving record
    public function edit(Receiving $receiving)
    {
        $title = 'Chi tiết phiếu tiếp nhận';
        $products_all = Product::all();
        $customers = Customers::all();
        $receivedProducts = ReceivedProduct::with(['product', 'serial', 'serial.exports'])
            ->where('reception_id', $receiving->id)
            ->get()
            ->groupBy('product_id');
        $users = User::all();
        $data = Receiving::all();
        return view('expertise.receivings.edit', compact('receiving', 'receivedProducts', 'products_all', 'customers', 'title', 'users', 'data'));
    }

    // Update the specified receiving record in storage
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'form_type' => 'required|min:1',
            'form_type.*' => 'in:1,2,3',
            'form_code_receiving' => 'required|string|unique:receiving,form_code_receiving,' . $id,
            'customer_id' => 'required|integer',
            'address' => 'nullable|string',
            'date_created' => 'required|date',
            'contact_person' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'required|integer',
            'phone' => 'nullable|string',
            'closed_at' => 'nullable|date',
            'status' => 'nullable|integer',
            'state' => 'nullable|integer',
        ]);
        // dd($validated);
        // Find the receiving record to update
        $receiving = Receiving::findOrFail($id);

        // Update the receiving record
        $receiving->update($validated);
        Artisan::call('receiving:update-status');

        DB::beginTransaction();
        try {
            // Xóa dữ liệu cũ trước khi cập nhật mới
            $receivedProducts = $receiving->receivedProducts;
            foreach ($receivedProducts as $receivedProduct) {
                $receivedProduct->warrantyReceived()->delete();
                $receivedProduct->delete();
            }

            // Duyệt qua danh sách sản phẩm từ request
            foreach ($request->input('product_id') as $product) {
                $serialId = null;
                $existSeri = SerialNumber::where('serial_code', $product['serial'])->first();
                // Nếu serial_id rỗng, tạo serial mới với status = 6
                if (!$existSeri) {
                    $newSerial = SerialNumber::create([
                        'product_id' => $product['product_id'],
                        'status' => 2, // Trạng thái mới
                        'serial_code' => $product['serial'], // Tạo mã serial tạm thời
                    ]);

                    $serialId = $newSerial->id;
                } else {
                    $serialId = $existSeri->id;
                }
                // Tạo sản phẩm tiếp nhận
                $receivedProduct = ReceivedProduct::create([
                    'reception_id' => $receiving->id,
                    'product_id' => $product['product_id'],
                    'quantity' => 1,
                    'serial_id' => $serialId,
                    'status' => 0,
                    'note' => '',
                ]);

                // Lưu thông tin bảo hành cho từng serial nếu tồn tại
                if (!empty($product['id_seri'])) {
                    foreach ($product['id_seri'] as $index => $serial) {
                        WarrantyReceived::create([
                            'product_received_id' => $receivedProduct->id,
                            'name_warranty' => $product['name_warranty'][$index] ?? null,
                            'state_recei' => $product['warranty'][$index] ?? null,
                            'note' => $product['note_seri'][$index] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('receivings.index')->with('warning', 'Có lỗi xảy ra khi cập nhật phiếu tiếp nhận.');
        }

        return redirect()->route('receivings.index')->with('msg', 'Cập nhật phiếu tiếp nhận thành công.');
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Tìm phiếu tiếp nhận
            $receiving = Receiving::findOrFail($id);

            // Kiểm tra nếu có returnForms hoặc quotation thì không được xoá
            if ($receiving->returnForms || $receiving->quotation) {
                DB::rollBack();
                return redirect()->route('receivings.index')->with('msg', 'Không thể xóa phiếu tiếp nhận vì đã có đơn trả hàng hoặc báo giá liên quan.');
            }

            // Xóa tất cả sản phẩm tiếp nhận và bảo hành liên quan
            foreach ($receiving->receivedProducts as $receivedProduct) {
                if ($receivedProduct->serial_id) {
                    // Kiểm tra xem serial có tồn tại trong phiếu xuất hàng không
                    $hasExport = \App\Models\ProductExport::where('sn_id', $receivedProduct->serial_id)->exists();
                    
                    // Kiểm tra xem serial có tồn tại trong phiếu tiếp nhận khác không
                    $hasOtherReceiving = ReceivedProduct::where('serial_id', $receivedProduct->serial_id)
                        ->where('id', '!=', $receivedProduct->id)
                        ->exists();
                    
                    // Chỉ xóa serial nếu không có trong phiếu xuất hàng và không có trong phiếu tiếp nhận khác
                    if (!$hasExport && !$hasOtherReceiving) {
                        SerialNumber::find($receivedProduct->serial_id)?->delete();
                    }
                }
                
                $receivedProduct->warrantyReceived()->delete();
                $receivedProduct->delete();
            }

            // Xóa phiếu tiếp nhận
            $receiving->delete();

            DB::commit();
            return redirect()->route('receivings.index')->with('msg', 'Xóa phiếu tiếp nhận thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('receivings.index')->with('warning', 'Có lỗi xảy ra khi xóa phiếu tiếp nhận.');
        }
    }


    // Remove the specified receiving record from storage
    // public function destroy(Receiving $receiving)
    // {
    //     $receivedProducts = $receiving->receivedProducts;

    //     // Duyệt qua từng sản phẩm và cập nhật trạng thái của serial number
    //     foreach ($receivedProducts as $product) {
    //         $serialNumber = $product->serial;
    //         if ($serialNumber) {
    //             $serialNumber->update(['status' => 2]);
    //         }
    //     }

    //     // Xóa bản ghi receiving
    //     $receiving->delete();

    //     return redirect()->route('receivings.index')->with('success', 'Receiving record deleted successfully.');
    // }
    public function getReceiving(Request $request)
    {
        $receivingId = $request->selectedId;
        $receiving = Receiving::with('customer')->find($receivingId);
        $receivedProduct = ReceivedProduct::with('product', 'serial', 'warrantyReceived')->where('reception_id', $receivingId)->get();
        $products = Product::all();
        if ($receiving) {
            return response()->json([
                'success' => true,
                'data' => $receiving,
                'product' => $receivedProduct,
                'productData' => $products,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'pha hoai khong a'
            ]);
        }
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã phiếu: ' . $data['ma'], 'name' => 'ma-phieu', 'icon' => 'po'];
        }
        if (isset($data['note']) && $data['note'] !== null) {
            $filters[] = ['value' => 'Ghi chú: ' . $data['note'], 'name' => 'ghi-chu', 'icon' => 'po'];
        }
        if (isset($data['serial']) && $data['serial'] !== null) {
            $filters[] = ['value' => 'S/N: ' . $data['serial'], 'name' => 'serial', 'icon' => 'po'];
        }
        if (isset($data['product_name']) && $data['product_name'] !== null) {
            $filters[] = ['value' => 'Tên sản phẩm: ' . $data['product_name'], 'name' => 'ten-san-pham', 'icon' => 'po'];
        }
        if (isset($data['product_code']) && $data['product_code'] !== null) {
            $filters[] = ['value' => 'Mã sản phẩm: ' . $data['product_code'], 'name' => 'ma-san-pham', 'icon' => 'po'];
        }
        if (isset($data['customer']) && $data['customer'] !== null) {
            $filters[] = ['value' => 'Khách hàng: ' . count($data['customer']) . ' đã chọn', 'name' => 'khach-hang', 'icon' => 'user'];
        }
        if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Ngày lập phiếu: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-lap-phieu', 'icon' => 'date'];
        }
        if (isset($data['closed_at']) && $data['closed_at'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['closed_at'][0]));
            $date_end = date("d/m/Y", strtotime($data['closed_at'][1]));
            $filters[] = ['value' => 'Ngày đóng phiếu: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-dong-phieu', 'icon' => 'date'];
        }
        // Hàm hỗ trợ tạo filter từ mảng trạng thái
        function generateStatusFilter($data, $key, $statusMapping, $label, $name)
        {
            if (isset($data[$key]) && $data[$key] !== null) {
                $statusValues = [];
                foreach ($data[$key] as $status) {
                    if (isset($statusMapping[$status])) {
                        $statusValues[] = '<span style="color: ' . $statusMapping[$status]['color'] . ';">' . $statusMapping[$status]['label'] . '</span>';
                    }
                }
                if (!empty($statusValues)) {
                    return ['value' => $label . ': ' . implode(', ', $statusValues), 'name' => $name, 'icon' => 'status'];
                }
            }
            return null;
        }
        // Loại phiếu
        $formTypeMapping = [
            1 => ['label' => 'Bảo hành', 'color' => '#858585'],
            2 => ['label' => 'Dịch vụ', 'color' => '#08AA36BF'],
            3 => ['label' => 'Bảo hành dịch vụ', 'color' => '#08AA36BF'],
        ];
        $formTypeFilter = generateStatusFilter($data, 'form_type', $formTypeMapping, 'Loại phiếu', 'loai-phieu');
        if ($formTypeFilter) {
            $filters[] = $formTypeFilter;
        }
        // Hàng tiếp nhận
        $brandTypeMapping = [
            1 => ['label' => 'Nội bộ', 'color' => '#858585'],
            2 => ['label' => 'Bên ngoài', 'color' => '#08AA36BF'],
        ];
        $brandTypeFilter = generateStatusFilter($data, 'brand_type', $brandTypeMapping, 'Trạng thái', 'trang-thai');
        if ($brandTypeFilter) {
            $filters[] = $brandTypeFilter;
        }
        // Tình trạng
        $statusMapping = [
            1 => ['label' => 'Tiếp nhận', 'color' => '#858585'],
            2 => ['label' => 'Xử lý', 'color' => '#08AA36BF'],
            3 => ['label' => 'Hoàn thành', 'color' => '#08AA36BF'],
            4 => ['label' => 'Khách không đồng ý', 'color' => '#08AA36BF'],
        ];
        $statusFilter = generateStatusFilter($data, 'status', $statusMapping, 'Tình trạng', 'tinh-trang');
        if ($statusFilter) {
            $filters[] = $statusFilter;
        }
        // Trạng thái
        $stateMapping = [
            2 => ['label' => 'Quá hạn', 'color' => '#858585'],
            1 => ['label' => 'Chưa xử lý', 'color' => '#08AA36BF'],
            0 => ['label' => 'Blank', 'color' => '#08AA36BF'],
        ];
        $stateFilter = generateStatusFilter($data, 'state', $stateMapping, 'Trạng thái', 'trang-thai');
        if ($stateFilter) {
            $filters[] = $stateFilter;
        }

        if ($request->ajax()) {
            $receivings = $this->receivings->getReceiAjax($data);
            return response()->json([
                'data' => $receivings,
                'filters' => $filters,
            ]);
        }
        return false;
    }
    public function updateStatus(Request $request)
    {
        $status = $request->input('status');
        $recei = $request->input('recei');
        $returndata = $request->input('returndata');
        try {
            // Cập nhật Receiving
            if ($recei) {
                $receiving = Receiving::findOrFail($recei);
                $receiving->status = $status;
                // Nếu status != 1, đặt state = 0
                if ($status != 1) {
                    $receiving->state = 0;
                }
                $receiving->save();
            }
            // Cập nhật ReturnForm nếu tồn tại
            if ($returndata) {
                $returnForm = ReturnForm::findOrFail($returndata);
                // Điều chỉnh status theo logic yêu cầu
                if ($status == 3) {
                    $returnForm->status = 1;
                } elseif ($status == 4) {
                    $returnForm->status = 2;
                } else {
                    $returnForm->status = $status;
                }
                $returnForm->save();
            }
            return response()->json(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công', 'id' => $recei]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateStatusNoitifi($id, Request $request)
    {
        $receiving = Receiving::findOrFail($id); // Lấy phiếu tiếp nhận
        $receiving->status = $request->status;  // Cập nhật trạng thái
        $receiving->save();

        // Gửi thông báo đến người dùng
        $users = User::all(); // Có thể lọc ra nhóm người dùng cụ thể nếu cần
        foreach ($users as $user) {
            $user->notify(new ReceiNotification($receiving, '', $request->status));
        }

        return redirect()->route('receivings.index')->with('success', 'Trạng thái đã được cập nhật và thông báo đã được gửi.');
    }

    // Tìm warranty theo product_id và serial
    public function warrantyLookup(Request $request)
    {
        $productId = $request->input('product');
        $serial = $request->input('serial');

        $sn_id = SerialNumber::where('serial_code', $serial)->first();
        if ($sn_id) {
            // Truy vấn dữ liệu từ bảng warranty_lookup
            $warranty = warrantyLookup::with('serialNumber.exports.export')
                ->where('product_id', $productId)
                ->where('sn_id', $sn_id->id)
                ->get();

            if ($warranty) {
                return response()->json([
                    'warranty' => $warranty,
                ]);
            } else {
                return response()->json([
                    'message' => 'Không tìm thấy thông tin bảo hành',
                ], 404);
            }
        }
    }
    public static function warrantyLookupById($productId, $serial)
    {
        $sn_id = SerialNumber::where('serial_code', $serial)->first();
        if ($sn_id) {
            // Truy vấn dữ liệu từ bảng warranty_lookup
            $warranty = DB::table('warranty_lookup')
                ->where('product_id', $productId)
                ->where('sn_id', $sn_id->id)
                ->get();
            if ($warranty) {
                return $warranty;
            }
        }
    }
}
