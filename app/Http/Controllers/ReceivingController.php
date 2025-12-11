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
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\form;
use function Laravel\Prompts\select;

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

    /**
     * API lấy danh sách phiếu tiếp nhận
     */
    public function list(Request $request)
    {
        try {
            $data = $request->all();
            $perPage = $request->input('limit', 20);

            // 1. Chuẩn bị Subqueries (để lấy serial và tên sản phẩm gộp)
            $serialAggregate = DB::table('received_products')
                ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'received_products.serial_id')
                ->select(
                    'received_products.reception_id',
                    DB::raw('GROUP_CONCAT(DISTINCT serial_numbers.serial_code ORDER BY serial_numbers.serial_code SEPARATOR ", ") AS serial_number')
                )
                ->groupBy('received_products.reception_id');

            $productAggregate = DB::table('received_products')
                ->leftJoin('products', 'products.id', '=', 'received_products.product_id')
                ->select(
                    'received_products.reception_id',
                    DB::raw('GROUP_CONCAT(DISTINCT products.product_name ORDER BY products.product_name SEPARATOR ", ") AS product_name'),
                    DB::raw('GROUP_CONCAT(DISTINCT products.product_code ORDER BY products.product_code SEPARATOR ", ") AS product_code')
                )
                ->groupBy('received_products.reception_id');

            // 2. Bắt đầu Query chính
            $query = Receiving::query()
                ->join('users', 'receiving.user_id', '=', 'users.id')
                ->join('customers', 'receiving.customer_id', '=', 'customers.id')
                ->leftJoinSub($serialAggregate, 'agg_serials', function ($join) {
                    $join->on('agg_serials.reception_id', '=', 'receiving.id');
                })
                ->leftJoinSub($productAggregate, 'agg_products', function ($join) {
                    $join->on('agg_products.reception_id', '=', 'receiving.id');
                })
                ->select(
                    'receiving.*',
                    'users.name as username',
                    'customers.customer_name as customername',
                    DB::raw('agg_serials.serial_number as serial_number'),
                    DB::raw('agg_products.product_name as product_name'),
                    DB::raw('agg_products.product_code as product_code')
                );

            // 3. Xử lý điều kiện tìm kiếm (Search)
            if (!empty($data['search'])) {
                $query->where(function ($q) use ($data) {
                    $q->where('receiving.form_code_receiving', 'like', '%' . $data['search'] . '%')
                        ->orWhere('receiving.notes', 'like', '%' . $data['search'] . '%')
                        ->orWhere('customers.customer_name', 'like', '%' . $data['search'] . '%') // Tìm theo tên khách lun cho tiện
                        ->orWhereExists(function ($sub) use ($data) {
                            $sub->from('received_products')
                                ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'received_products.serial_id')
                                ->whereColumn('received_products.reception_id', 'receiving.id')
                                ->where('serial_numbers.serial_code', 'like', '%' . $data['search'] . '%');
                        })
                        ->orWhereExists(function ($sub) use ($data) {
                            $sub->from('received_products')
                                ->leftJoin('products', 'products.id', '=', 'received_products.product_id')
                                ->whereColumn('received_products.reception_id', 'receiving.id')
                                ->where(function ($sq) use ($data) {
                                    $sq->where('products.product_name', 'like', '%' . $data['search'] . '%')
                                        ->orWhere('products.product_code', 'like', '%' . $data['search'] . '%');
                                });
                        });
                });
            }

            // 4. Các bộ lọc khác (Filters)
            if (!empty($data['ma'])) {
                $query->where('receiving.form_code_receiving', 'like', '%' . $data['ma'] . '%');
            }

            if (!empty($data['customer'])) {
                $customerIds = is_array($data['customer']) ? $data['customer'] : [$data['customer']];
                $query->whereIn('receiving.customer_id', $customerIds);
            }

            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0])->startOfDay();
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $query->whereBetween('receiving.date_created', [$dateStart, $dateEnd]);
            }

            if (!empty($data['closed_at'][0]) && !empty($data['closed_at'][1])) {
                $dateStart = Carbon::parse($data['closed_at'][0])->startOfDay();
                $dateEnd = Carbon::parse($data['closed_at'][1])->endOfDay();
                $query->whereBetween('receiving.closed_at', [$dateStart, $dateEnd]);
            }

            if (isset($data['form_type'])) {
                $types = is_array($data['form_type']) ? $data['form_type'] : [$data['form_type']];
                $query->whereIn('receiving.form_type', $types);
            }

            if (isset($data['status'])) {
                $statuses = is_array($data['status']) ? $data['status'] : [$data['status']];
                $query->whereIn('receiving.status', $statuses);
            }

            // 5. Sắp xếp (Sorting)
            if (isset($data['sort']) && isset($data['sort'][0])) {
                // Cần thêm prefix table nếu sort theo các cột chung chung
                $sortColumn = $data['sort'][0];
                $sortDirection = $data['sort'][1] ?? 'asc';

                // Fix lỗi ambiguous column nếu sort theo id, status...
                if (in_array($sortColumn, ['id', 'status', 'created_at', 'updated_at'])) {
                    $sortColumn = 'receiving.' . $sortColumn;
                }

                $query->orderBy($sortColumn, $sortDirection);
            } else {
                $query->orderBy('receiving.id', 'desc');
            }

            // 6. Thực hiện phân trang
            $receivings = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách phiếu tiếp nhận thành công',
                'data' => $receivings
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $ex->getMessage() . ' at line ' . $ex->getLine()
            ], 500);
        }
    }

    // API lấy chi tiết phiếu tiếp nhận
    public function detail($id)
    {
        try {
            //code...
            $receiving = Receiving::with([
                'customer',
                'user',
                'receivedProducts.product',
                'receivedProducts.serial',
                'quotation',
                'returnForms'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Lấy chi tiết phiếu tiếp nhận thành công.',
                'data' => $receiving
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiếu tiếp nhận với ID: ' . $id
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $ex->getMessage()
            ], 500);
        }
    }

    //Tạo phiếu tiếp nhận
    public function add(Request $request)
    {
        // ---------------------------------------------------------
        // BƯỚC 1: VALIDATOR (CÁNH CỔNG BẢO VỆ)
        // ---------------------------------------------------------
        $validator = Validator::make($request->all(), [
            // Các trường BẮT BUỘC phải có
            'customer_id'    => 'required|exists:customers,id',
            'branch_id'      => 'required',
            'products'       => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|numeric|min:1',

            // Các trường LINH ĐỘNG (Sửa luật để không bị chặn)
            // form_type: DB cần số, nhưng nếu lỡ gửi chữ thì để 'required' thôi, đừng ép 'integer' ngay đây
            'form_type'      => 'required',

            // status: DB cần số, nhưng Postman đang gửi chữ "pending"
            // => Bỏ rule 'integer', chỉ để nullable hoặc string để cho qua cửa
            'status'         => 'nullable',

            // form_code_receiving, user_id: XÓA HẲN khỏi đây vì hệ thống tự làm
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // ---------------------------------------------------------
        // BƯỚC 2: LOGIC XỬ LÝ (CHUYỂN ĐỔI DỮ LIỆU)
        // ---------------------------------------------------------
        DB::beginTransaction();
        try {
            // 1. Xử lý Form Type (Nếu gửi chữ "Bảo hành" -> đổi thành số 1)
            // Nếu bạn gửi số 1 ngay từ đầu thì tốt, code này vẫn chạy đúng.
            $formTypeValue = $request->form_type;
            if (!is_numeric($formTypeValue)) {
                // Ví dụ map đơn giản, bạn nên quy định Client gửi số thì tốt hơn
                $formTypeValue = 1; // Mặc định về 1 nếu gửi chữ linh tinh
            }

            // 2. Xử lý Status (Nếu gửi chữ "pending" -> đổi thành số 1)
            $statusValue = 1; // Mặc định 1 (Tiếp nhận)
            if ($request->status == 'pending') $statusValue = 1;
            if ($request->status == 'processing') $statusValue = 2;
            if (is_numeric($request->status)) $statusValue = $request->status; // Nếu gửi số thì lấy số

            // 3. Sinh mã phiếu
            $tempModel = new Receiving();
            $prefix = $request->input('prefix', 'PN');
            $formCode = $tempModel->getQuoteCount($prefix, Receiving::class, 'form_code_receiving');

            // ---------------------------------------------------------
            // BƯỚC 3: LƯU VÀO DATABASE
            // ---------------------------------------------------------
            $receiving = new Receiving();
            $receiving->branch_id           = $request->branch_id;
            $receiving->form_type           = $formTypeValue; // Đã xử lý thành số
            $receiving->form_code_receiving = $formCode;      // Đã tự sinh
            $receiving->customer_id         = $request->customer_id;
            $receiving->address             = $request->address;
            $receiving->contact_person      = $request->contact_person;
            $receiving->phone               = $request->phone;
            $receiving->notes               = $request->notes;
            $receiving->user_id             = Auth::id() ?? 1; // Tự lấy ID
            $receiving->date_created        = $request->date_created ? Carbon::parse($request->date_created) : Carbon::now();
            $receiving->status              = $statusValue;    // Đã xử lý thành số
            $receiving->state               = $request->state ?? 0;

            $receiving->save();

            // Lưu sản phẩm...
            foreach ($request->products as $item) {
                ReceivedProduct::create([
                    'reception_id' => $receiving->id,
                    'product_id'   => $item['product_id'],
                    'quantity'     => $item['quantity'],
                    'serial_id'    => $item['serial_id'] ?? null,
                    'status'       => $item['status'] ?? null,
                    'note'         => $item['note'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'data' => $receiving], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * CẬP NHẬT (change)
     */
    public function change(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'   => 'required|exists:customers,id',
            'products'      => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $receiving = Receiving::findOrFail($id);

            // 1. Cập nhật thông tin phiếu chính
            $receiving->update($request->only([
                'branch_id',
                'form_type',
                'customer_id',
                'address',
                'contact_person',
                'notes',
                'phone',
                'status',
                'state',
                'closed_at'
            ]));

            if ($request->has('date_created')) {
                $receiving->date_created = Carbon::parse($request->date_created);
                $receiving->save();
            }

            // 2. Cập nhật sản phẩm (Sửa lỗi chính tả prodicts -> products)
            if ($request->has('products') && is_array($request->products)) {
                // Xóa cũ
                ReceivedProduct::where('reception_id', $id)->delete();

                // Tạo mới
                foreach ($request->products as $item) {
                    ReceivedProduct::create([
                        'reception_id'  => $receiving->id,
                        'product_id'    => $item['product_id'],
                        'quantity'      => $item['quantity'],
                        'serial_id'     => $item['serial_id'] ?? null,
                        'status'        => $item['status'] ?? null,
                        'note'          => $item['note'] ?? null,
                    ]);
                }
            }

            // 3. Commit và Return phải nằm NGOÀI vòng if sản phẩm
            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Cập nhật phiếu thành công.',
                'data'      => $receiving->fresh()->load('receivedProducts.product'),
            ], 200);
        } catch (\Exception $ex) { // Sửa Exception cho đúng namespace
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $ex->getMessage()
            ], 500);
        }
    }

    /**
     * XÓA (DESTROY)
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $receiving = Receiving::findOrFail($id);

            // 1. Xóa các sản phẩm con trước (Dù có set cascade ở DB hay không, xóa ở code vẫn an toàn hơn)
            $receiving->receivedProducts()->delete();

            // 2. Có thể cần xóa thêm các quan hệ khác như Quotation, ReturnForm nếu cần thiết
            // $receiving->quotation()->delete();
            // $receiving->returnForms()->delete();

            // 3. Xóa phiếu chính
            $receiving->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa phiếu tiếp nhận thành công.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa: ' . $e->getMessage()
            ], 500);
        }
    }
}
