<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Exports;
use App\Models\Imports;
use App\Models\InventoryLookup;
use App\Models\Product;
use App\Models\ProductExport;
use App\Models\ProductImport;
use App\Models\Quotation;
use App\Models\ReceivedProduct;
use App\Models\Receiving;
use App\Models\Report;
use App\Models\ReturnForm;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $reports;

    public function __construct()
    {
        $this->reports = new Report();
    }
    public function index()
    {
        //
    }

    public function reportOverview()
    {
        $title = 'Tổng quát báo cáo';
        // Báo cáo phiếu xuất nhập
        $phieuNhap = Imports::count();
        $phieuXuat = Exports::count();
        $fromDateExIm = collect([Imports::min('date_create'), Exports::min('date_create')])->filter()->min();
        $toDateExIm = collect([Imports::max('date_create'), Exports::max('date_create')])->filter()->max();
        //Báo cáo tồn kho
        $tonKho = SerialNumber::whereIn('status', [1, 5])->count();
        $hangMuon = SerialNumber::where('status', 5)->count();
        $toiHanBT = InventoryLookup::where('status', 1)->count();
        $fromDateInventory = collect([
            SerialNumber::where('status', 1)->min('created_at'),
            InventoryLookup::where('status', 1)->min('created_at')
        ])->filter()->min();
        $toDateInventory = collect([
            SerialNumber::where('status', 1)->max('created_at'),
            InventoryLookup::where('status', 1)->max('created_at')
        ])->filter()->max();
        //Báo cáo hàng xuất nhập
        $hangNhap = ProductImport::count();
        $hangXuat = ProductExport::count();
        $fromDateProductExIm = collect([ProductImport::min('created_at'), ProductExport::min('created_at')])->filter()->min();
        $toDateProductExIm = collect([ProductImport::max('created_at'), ProductExport::max('created_at')])->filter()->max();
        //Bao cao hang tiep nhan va tra hang
        $hangTiepNhan = ReceivedProduct::count();
        $hangTraHang = SerialNumber::where('status', 4)->count();
        $fromDateReceiveReturn = collect([ReceivedProduct::min('created_at'), SerialNumber::where('status', 4)->min('created_at')])->filter()->min();
        $toDateReceiveReturn = collect([ReceivedProduct::max('created_at'), SerialNumber::where('status', 4)->max('created_at')])->filter()->max();
        //Báo cáo phiếu báo giá
        $phieuHoanThanh = ReturnForm::where('status', 1)->count();
        $phieuKhongDongY = ReturnForm::where('status', 2)->count();
        $tongTienHoanThanh = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
            ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
            ->where('return_form.status', 1)
            ->sum('quotations.total_amount');
        $tongTienKhongDongY = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
            ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
            ->where('return_form.status', 2)
            ->sum('quotations.total_amount');
        $dates = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
            ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
            ->selectRaw('MIN(quotations.created_at) as min_date, MAX(quotations.created_at) as max_date')
            ->first();
        $fromDateQuotation = $dates->min_date;
        $toDateQuotation = $dates->max_date;

        //Báo cáo phiếu tiếp nhận và trả hàng
        $phieuTiepNhan = Receiving::count();
        $phieuTraHang = ReturnForm::count();
        $phieuChuaXL = Receiving::where('state', 1)->count();
        $phieuQuaHan = Receiving::where('state', 2)->count();
        $fromDatePhieuTNTH = collect([
            Receiving::min('created_at'),
            ReturnForm::min('created_at'),
            Receiving::where('state', 1)->min('created_at'),
            Receiving::where('state', 2)->min('created_at')
        ])->filter()->min();
        $toDatePhieuTNTH = collect([
            Receiving::max('created_at'),
            ReturnForm::max('created_at'),
            Receiving::where('state', 1)->max('created_at'),
            Receiving::where('state', 2)->max('created_at')
        ])->filter()->max();
        return view('reports.overview', compact(
            'title',
            'phieuNhap',
            'phieuXuat',
            'tonKho',
            'hangMuon',
            'toiHanBT',
            'hangNhap',
            'hangXuat',
            'hangTiepNhan',
            'hangTraHang',
            'phieuHoanThanh',
            'phieuKhongDongY',
            'tongTienHoanThanh',
            'tongTienKhongDongY',
            'phieuTiepNhan',
            'phieuTraHang',
            'phieuChuaXL',
            'phieuQuaHan',
            'fromDateExIm',
            'toDateExIm',
            'fromDateInventory',
            'toDateInventory',
            'fromDateProductExIm',
            'toDateProductExIm',
            'fromDateReceiveReturn',
            'toDateReceiveReturn',
            'fromDateQuotation',
            'toDateQuotation',
            'fromDatePhieuTNTH',
            'toDatePhieuTNTH'
        ));
    }

    public function reportOverviewApp()
    {
        return response()->json([
            'title' => 'Tổng quát báo cáo',
            'phieuNhap' => Imports::count(),
            'phieuXuat' => Exports::count(),
            'fromDateExIm' => collect([Imports::min('date_create'), Exports::min('date_create')])->filter()->min(),
            'toDateExIm' => collect([Imports::max('date_create'), Exports::max('date_create')])->filter()->max(),
            'tonKho' => SerialNumber::whereIn('status', [1, 5])->count(),
            'hangMuon' => SerialNumber::where('status', 5)->count(),
            'toiHanBT' => InventoryLookup::where('status', 1)->count(),
            'fromDateInventory' => collect([
                SerialNumber::where('status', 1)->min('created_at'),
                InventoryLookup::where('status', 1)->min('created_at')
            ])->filter()->min(),
            'toDateInventory' => collect([
                SerialNumber::where('status', 1)->max('created_at'),
                InventoryLookup::where('status', 1)->max('created_at')
            ])->filter()->max(),
            'hangNhap' => ProductImport::count(),
            'hangXuat' => ProductExport::count(),
            'fromDateProductExIm' => collect([ProductImport::min('created_at'), ProductExport::min('created_at')])->filter()->min(),
            'toDateProductExIm' => collect([ProductImport::max('created_at'), ProductExport::max('created_at')])->filter()->max(),
            'hangTiepNhan' => ReceivedProduct::count(),
            'hangTraHang' => SerialNumber::where('status', 4)->count(),
            'fromDateReceiveReturn' => collect([ReceivedProduct::min('created_at'), SerialNumber::where('status', 4)->min('created_at')])->filter()->min(),
            'toDateReceiveReturn' => collect([ReceivedProduct::max('created_at'), SerialNumber::where('status', 4)->max('created_at')])->filter()->max(),
            'phieuHoanThanh' => ReturnForm::where('status', 1)->count(),
            'phieuKhongDongY' => ReturnForm::where('status', 2)->count(),
            'tongTienHoanThanh' => ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                ->where('return_form.status', 1)
                ->sum('quotations.total_amount'),
            'tongTienKhongDongY' => ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                ->where('return_form.status', 2)
                ->sum('quotations.total_amount'),
            'fromDateQuotation' => ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                ->selectRaw('MIN(quotations.created_at) as min_date, MAX(quotations.created_at) as max_date')
                ->first()->min_date,
            'toDateQuotation' => ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                ->selectRaw('MIN(quotations.created_at) as min_date, MAX(quotations.created_at) as max_date')
                ->first()->max_date,
            'phieuTiepNhan' => Receiving::count(),
            'phieuTraHang' => ReturnForm::count(),
            'phieuChuaXL' => Receiving::where('state', 1)->count(),
            'phieuQuaHan' => Receiving::where('state', 2)->count(),
            'fromDatePhieuTNTH' => collect([
                Receiving::min('created_at'),
                ReturnForm::min('created_at'),
                Receiving::where('state', 1)->min('created_at'),
                Receiving::where('state', 2)->min('created_at')
            ])->filter()->min(),
            'toDatePhieuTNTH' => collect([
                Receiving::max('created_at'),
                ReturnForm::max('created_at'),
                Receiving::where('state', 1)->max('created_at'),
                Receiving::where('state', 2)->max('created_at')
            ])->filter()->max()
        ]);
    }

    public function reportExportImport()
    {
        $title = 'Báo cáo hàng xuất nhập';
        $products = Product::with(['imports', 'exports'])
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'total_import' => $product->imports->sum('quantity'),
                    'total_export' => $product->exports->sum('quantity'),
                ];
            });
        return view('reports.export_import', compact('title', 'products'));
    }

    /**
     * API Báo cáo Xuất Nhập (Đã thêm bộ lọc)
     */
    public function reportExportImportApp(Request $request)
    {
        // 1. Khởi tạo query Sản phẩm
        $query = Product::query();

        // 2. Lọc theo Mã hoặc Tên hàng (nếu có)
        if ($request->filled('ma')) {
            $query->where('product_code', 'like', '%' . $request->ma . '%');
        }
        if ($request->filled('ten')) {
            $query->where('product_name', 'like', '%' . $request->ten . '%');
        }

        // 3. Lấy ngày lọc (nếu có) để lọc trong quan hệ
        $dateStart = $request->input('date.0');
        $dateEnd   = $request->input('date.1');

        // 4. Eager Load và Tính toán
        // Lưu ý: Logic này giả định relation 'imports' và 'exports' trỏ tới bảng chi tiết có cột 'quantity'
        $products = $query->with(['imports' => function ($q) use ($dateStart, $dateEnd) {
            // Nếu muốn lọc ngày, cần join bảng imports cha (tùy cấu trúc model của bạn)
            // Tạm thời giữ nguyên logic lấy hết nếu chưa xử lý ngày sâu
            if ($dateStart && $dateEnd) {
                $q->whereBetween('created_at', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59']);
            }
        }, 'exports' => function ($q) use ($dateStart, $dateEnd) {
            if ($dateStart && $dateEnd) {
                $q->whereBetween('created_at', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59']);
            }
        }])->get();

        // 5. Map dữ liệu & Tính tổng
        $data = $products->map(function ($product) {
            return [
                'product_id'   => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'total_import' => (int) $product->imports->sum('quantity'),
                'total_export' => (int) $product->exports->sum('quantity'),
            ];
        });

        // 6. LỌC THEO SỐ LƯỢNG (Logic quan trọng đang thiếu)
        // Sử dụng Collection filter sau khi đã tính tổng

        // Lọc Số lượng Nhập
        if ($request->filled('so_luong_nhap')) {
            $min = (int) ($request->so_luong_nhap[0] ?? 0);
            // Nếu max rỗng thì lấy vô cùng lớn
            $max = ($request->so_luong_nhap[1] !== '' && $request->so_luong_nhap[1] !== null)
                ? (int) $request->so_luong_nhap[1]
                : 999999999;

            $data = $data->filter(function ($item) use ($min, $max) {
                return $item['total_import'] >= $min && $item['total_import'] <= $max;
            });
        }

        // Lọc Số lượng Xuất
        if ($request->filled('so_luong_xuat')) {
            $min = (int) ($request->so_luong_xuat[0] ?? 0);
            $max = ($request->so_luong_xuat[1] !== '' && $request->so_luong_xuat[1] !== null)
                ? (int) $request->so_luong_xuat[1]
                : 999999999;

            $data = $data->filter(function ($item) use ($min, $max) {
                return $item['total_export'] >= $min && $item['total_export'] <= $max;
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $data->values() // Reset key mảng (0, 1, 2...) để JSON đẹp
        ]);
    }

    public function reportReceiptReturn()
    {
        $title = 'Báo cáo hàng tiếp nhận - trả hàng';
        $products = Product::with(['receivedProducts', 'returnProducts'])
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'total_receive' => $product->receivedProducts->sum('quantity'),
                    'total_return' => $product->returnProducts->sum('quantity'),
                ];
            });
        return view('reports.receipt_return', compact('title', 'products'));
    }

    /**
     * API Báo cáo Tiếp nhận - Trả hàng (Đã tích hợp bộ lọc)
     */
    public function reportReceiptReturnApp(Request $request)
    {
        // 1. Khởi tạo query Sản phẩm
        $query = Product::query();

        // 2. Lọc theo Mã hoặc Tên hàng (nếu có)
        if ($request->filled('ma')) {
            $query->where('product_code', 'like', '%' . $request->ma . '%');
        }
        if ($request->filled('ten')) {
            $query->where('product_name', 'like', '%' . $request->ten . '%');
        }

        // 3. Lấy khoảng thời gian lọc (nếu có)
        $dateStart = $request->input('date.0');
        $dateEnd   = $request->input('date.1');

        // 4. Tính tổng số lượng Tiếp nhận và Trả hàng (kèm điều kiện ngày tháng)
        // Sử dụng withSum để tối ưu hiệu năng thay vì lấy hết về rồi sum
        
        // Tính tổng Tiếp nhận (receivedProducts)
        $query->withSum(['receivedProducts' => function ($q) use ($dateStart, $dateEnd) {
            if ($dateStart && $dateEnd) {
                // Giả sử lọc theo ngày tạo phiếu/dòng
                $q->whereBetween('created_at', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59']);
            }
        }], 'quantity');

        // Tính tổng Trả hàng (returnProducts)
        $query->withSum(['returnProducts' => function ($q) use ($dateStart, $dateEnd) {
            if ($dateStart && $dateEnd) {
                $q->whereBetween('created_at', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59']);
            }
        }], 'quantity');

        // Lấy dữ liệu từ DB
        $products = $query->get();

        // 5. Format dữ liệu trả về
        // Laravel withSum sẽ tạo ra thuộc tính: {relation}_sum_{column}
        $data = $products->map(function ($product) {
            return [
                'product_id'    => $product->id,
                'product_code'  => $product->product_code,
                'product_name'  => $product->product_name,
                // received_products_sum_quantity là tên mặc định Laravel sinh ra
                'total_receive' => (int) ($product->received_products_sum_quantity ?? 0),
                // return_products_sum_quantity
                'total_return'  => (int) ($product->return_products_sum_quantity ?? 0),
            ];
        });

        // 6. Lọc theo khoảng Số lượng (Sau khi đã tính tổng)
        
        // Lọc số lượng Tiếp nhận (Dùng param 'so_luong_nhap' tương ứng với filter frontend)
        if ($request->filled('so_luong_nhap')) {
            $min = (int) ($request->so_luong_nhap[0] ?? 0);
            $max = ($request->so_luong_nhap[1] !== '' && $request->so_luong_nhap[1] !== null) 
                    ? (int) $request->so_luong_nhap[1] 
                    : 999999999;

            $data = $data->filter(function ($item) use ($min, $max) {
                return $item['total_receive'] >= $min && $item['total_receive'] <= $max;
            });
        }

        // Lọc số lượng Trả hàng (Dùng param 'so_luong_xuat' tương ứng với filter frontend)
        if ($request->filled('so_luong_xuat')) {
            $min = (int) ($request->so_luong_xuat[0] ?? 0);
            $max = ($request->so_luong_xuat[1] !== '' && $request->so_luong_xuat[1] !== null) 
                    ? (int) $request->so_luong_xuat[1] 
                    : 999999999;

            $data = $data->filter(function ($item) use ($min, $max) {
                return $item['total_return'] >= $min && $item['total_return'] <= $max;
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $data->values() // Reset key mảng về 0,1,2...
        ]);
    } 

    public function reportQuotation()
    {
        $title = 'Báo cáo phiếu báo giá';
        $quotations = Quotation::join('receiving', 'receiving.id', 'quotations.reception_id')
            ->join('return_form', 'return_form.reception_id', 'receiving.id')
            ->select('quotations.*', 'return_form.status')
            ->get();
        $customers = Customers::all();
        return view('reports.quotation', compact('title', 'quotations', 'customers'));
    }

    public function reportQuotationApp()
    {
        $quotations = Quotation::join('receiving', 'receiving.id', 'quotations.reception_id')
            ->join('return_form', 'return_form.reception_id', 'receiving.id')
            ->join('customers', 'receiving.customer_id', 'customers.id')
            ->select('quotations.*', 'receiving.status as status_return', 'receiving.form_code_receiving', 'customers.customer_name')
            ->get();

        // Kiểm tra dữ liệu có bị rỗng không
        if ($quotations->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có dữ liệu phiếu báo giá!',
                'quotations' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'quotations' => $quotations
        ]);
    }


    public function filterReportOverview(Request $request)
    {
        $dataValue = $request->dataValue;
        $dataName = $request->dataName;
        $response = [];
        if ($dataName == 'phieuXN') {
            if ($dataValue == 0) {
                $phieuNhap = Imports::count();
                $phieuXuat = Exports::count();
                $fromDateExIm = collect([Imports::min('date_create'), Exports::min('date_create')])->filter()->min();
                $toDateExIm = collect([Imports::max('date_create'), Exports::max('date_create')])->filter()->max();
                $response = [
                    'phieuNhap' => $phieuNhap,
                    'phieuXuat' => $phieuXuat,
                    'ngayBatDau' => $fromDateExIm,
                    'ngayKetThuc' => $toDateExIm
                ];
            }
            if ($dataValue == 1) {
                //Lấy dữ liệu của tháng này
                $phieuNhap = Imports::whereMonth('date_create', date('m'))->whereYear('date_create', date('Y'))->count();
                $phieuXuat = Exports::whereMonth('date_create', date('m'))->whereYear('date_create', date('Y'))->count();
                $fromDateExIm = date('Y-m-01');
                $toDateExIm = date('Y-m-t');
                $response = [
                    'phieuNhap' => $phieuNhap,
                    'phieuXuat' => $phieuXuat,
                    'ngayBatDau' => $fromDateExIm,
                    'ngayKetThuc' => $toDateExIm
                ];
            }
            if ($dataValue == 2) {
                //lấy dữ liệu của tháng trước
                $phieuNhap = Imports::whereMonth('date_create', date('m', strtotime('-1 month')))->whereYear('date_create', date('Y', strtotime('-1 month')))->count();
                $phieuXuat = Exports::whereMonth('date_create', date('m', strtotime('-1 month')))->whereYear('date_create', date('Y', strtotime('-1 month')))->count();
                $fromDateExIm = date('Y-m-01', strtotime('first day of last month'));
                $toDateExIm = date('Y-m-t', strtotime('last day of last month'));
                $response = [
                    'phieuNhap' => $phieuNhap,
                    'phieuXuat' => $phieuXuat,
                    'ngayBatDau' => $fromDateExIm,
                    'ngayKetThuc' => $toDateExIm
                ];
            }
            if ($dataValue == 3) {
                //lấy dữ liệu của 3 tháng trước
                $phieuNhap = Imports::whereMonth('date_create', date('m', strtotime('-3 month')))->whereYear('date_create', date('Y', strtotime('-3 month')))->count();
                $phieuXuat = Exports::whereMonth('date_create', date('m', strtotime('-3 month')))->whereYear('date_create', date('Y', strtotime('-3 month')))->count();
                $fromDateExIm = date('Y-m-01', strtotime('first day of -3 months'));
                $toDateExIm = date('Y-m-t', strtotime('last day of -1 months'));
                $response = [
                    'phieuNhap' => $phieuNhap,
                    'phieuXuat' => $phieuXuat,
                    'ngayBatDau' => $fromDateExIm,
                    'ngayKetThuc' => $toDateExIm
                ];
            }
        }
        if ($dataName == 'phieuTNTH') {
            if ($dataValue == 0) {
                $phieuTiepNhan = Receiving::count();
                $phieuTraHang = ReturnForm::count();
                $phieuChuaXL = Receiving::where('state', 1)->count();
                $phieuQuaHan = Receiving::where('state', 2)->count();
                $fromDatePhieuTNTH = collect([
                    Receiving::min('created_at'),
                    ReturnForm::min('created_at'),
                    Receiving::where('state', 1)->min('created_at'),
                    Receiving::where('state', 2)->min('created_at')
                ])->filter()->min();
                $toDatePhieuTNTH = collect([
                    Receiving::max('created_at'),
                    ReturnForm::max('created_at'),
                    Receiving::where('state', 1)->max('created_at'),
                    Receiving::where('state', 2)->max('created_at')
                ])->filter()->max();
                $response = [
                    'phieuTiepNhan' => $phieuTiepNhan,
                    'phieuTraHang' => $phieuTraHang,
                    'phieuChuaXL' => $phieuChuaXL,
                    'phieuQuaHan' => $phieuQuaHan,
                    'ngayBatDau' => $fromDatePhieuTNTH,
                    'ngayKetThuc' => $toDatePhieuTNTH
                ];
            }
            if ($dataValue == 1) {
                //Lấy dữ liệu của tháng này
                $phieuTiepNhan = Receiving::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $phieuTraHang = ReturnForm::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $phieuChuaXL = Receiving::where('state', 1)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $phieuQuaHan = Receiving::where('state', 2)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $fromDatePhieuTNTH = date('Y-m-01');
                $toDatePhieuTNTH = date('Y-m-t');
                $response = [
                    'phieuTiepNhan' => $phieuTiepNhan,
                    'phieuTraHang' => $phieuTraHang,
                    'phieuChuaXL' => $phieuChuaXL,
                    'phieuQuaHan' => $phieuQuaHan,
                    'ngayBatDau' => $fromDatePhieuTNTH,
                    'ngayKetThuc' => $toDatePhieuTNTH
                ];
            }
            if ($dataValue == 2) {
                //Lấy dữ liệu của tháng trước
                $phieuTiepNhan = Receiving::whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $phieuTraHang = ReturnForm::whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $phieuChuaXL = Receiving::where('state', 1)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $phieuQuaHan = Receiving::where('state', 2)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $fromDatePhieuTNTH = date('Y-m-01', strtotime('first day of last month'));
                $toDatePhieuTNTH = date('Y-m-t', strtotime('last day of last month'));
                $response = [
                    'phieuTiepNhan' => $phieuTiepNhan,
                    'phieuTraHang' => $phieuTraHang,
                    'phieuChuaXL' => $phieuChuaXL,
                    'phieuQuaHan' => $phieuQuaHan,
                    'ngayBatDau' => $fromDatePhieuTNTH,
                    'ngayKetThuc' => $toDatePhieuTNTH
                ];
            }
            if ($dataValue == 3) {
                //lấy dữ liệu của 3 tháng trước
                $phieuTiepNhan = Receiving::whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $phieuTraHang = ReturnForm::whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $phieuChuaXL = Receiving::where('state', 1)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $phieuQuaHan = Receiving::where('state', 2)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $fromDatePhieuTNTH = date('Y-m-01', strtotime('first day of -3 months'));
                $toDatePhieuTNTH = date('Y-m-t', strtotime('last day of -1 months'));
                $response = [
                    'phieuTiepNhan' => $phieuTiepNhan,
                    'phieuTraHang' => $phieuTraHang,
                    'phieuChuaXL' => $phieuChuaXL,
                    'phieuQuaHan' => $phieuQuaHan,
                    'ngayBatDau' => $fromDatePhieuTNTH,
                    'ngayKetThuc' => $toDatePhieuTNTH
                ];
            }
        }
        if ($dataName == 'baoCaoTK') {
            if ($dataValue == 0) {
                $tonKho = SerialNumber::whereIn('status', [1, 5])->count();
                $hangMuon = SerialNumber::where('status', 5)->count();
                $toiHanBT = InventoryLookup::where('status', 1)->count();
                $fromDateInventory = collect([
                    SerialNumber::where('status', 1)->min('created_at'),
                    InventoryLookup::where('status', 1)->min('created_at')
                ])->filter()->min();
                $toDateInventory = collect([
                    SerialNumber::where('status', 1)->max('created_at'),
                    InventoryLookup::where('status', 1)->max('created_at')
                ])->filter()->max();
                $response = [
                    'tonKho' => $tonKho,
                    'hangMuon' => $hangMuon,
                    'toiHanBT' => $toiHanBT,
                    'ngayBatDau' => $fromDateInventory,
                    'ngayKetThuc' => $toDateInventory
                ];
            }
            if ($dataValue == 1) {
                //Lấy dữ liệu của tháng này
                $tonKho = SerialNumber::whereIn('status', [1, 5])->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $hangMuon = SerialNumber::where('status', 5)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $toiHanBT = InventoryLookup::where('status', 1)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $fromDateInventory = date('Y-m-01');
                $toDateInventory = date('Y-m-t');
                $response = [
                    'tonKho' => $tonKho,
                    'hangMuon' => $hangMuon,
                    'toiHanBT' => $toiHanBT,
                    'ngayBatDau' => $fromDateInventory,
                    'ngayKetThuc' => $toDateInventory
                ];
            }
            if ($dataValue == 2) {
                //Lấy dữ liệu của tháng trước
                $tonKho = SerialNumber::whereIn('status', [1, 5])->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $hangMuon = SerialNumber::where('status', 5)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $toiHanBT = InventoryLookup::where('status', 1)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $fromDateInventory = date('Y-m-01', strtotime('first day of last month'));
                $toDateInventory = date('Y-m-t', strtotime('last day of last month'));
                $response = [
                    'tonKho' => $tonKho,
                    'hangMuon' => $hangMuon,
                    'toiHanBT' => $toiHanBT,
                    'ngayBatDau' => $fromDateInventory,
                    'ngayKetThuc' => $toDateInventory
                ];
            }
            if ($dataValue == 3) {
                //lấy dữ liệu của 3 tháng trước
                $tonKho = SerialNumber::whereIn('status', [1, 5])->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $hangMuon = SerialNumber::where('status', 5)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $toiHanBT = InventoryLookup::where('status', 1)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $fromDateInventory = date('Y-m-01', strtotime('first day of -3 months'));
                $toDateInventory = date('Y-m-t', strtotime('last day of -1 months'));
                $response = [
                    'tonKho' => $tonKho,
                    'hangMuon' => $hangMuon,
                    'toiHanBT' => $toiHanBT,
                    'ngayBatDau' => $fromDateInventory,
                    'ngayKetThuc' => $toDateInventory
                ];
            }
        }
        if ($dataName == 'hangXN') {
            if ($dataValue == 0) {
                $hangNhap = ProductImport::count();
                $hangXuat = ProductExport::count();
                $fromDateProductExIm = collect([ProductImport::min('created_at'), ProductExport::min('created_at')])->filter()->min();
                $toDateProductExIm = collect([ProductImport::max('created_at'), ProductExport::max('created_at')])->filter()->max();
                $response = [
                    'hangNhap' => $hangNhap,
                    'hangXuat' => $hangXuat,
                    'ngayBatDau' => $fromDateProductExIm,
                    'ngayKetThuc' => $toDateProductExIm
                ];
            }
            if ($dataValue == 1) {
                //Lấy dữ liệu của tháng này
                $hangNhap = ProductImport::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $hangXuat = ProductExport::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $fromDateProductExIm = date('Y-m-01');
                $toDateProductExIm = date('Y-m-t');
                $response = [
                    'hangNhap' => $hangNhap,
                    'hangXuat' => $hangXuat,
                    'ngayBatDau' => $fromDateProductExIm,
                    'ngayKetThuc' => $toDateProductExIm
                ];
            }
            if ($dataValue == 2) {
                //Lấy dữ liệu của tháng trước
                $hangNhap = ProductImport::whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $hangXuat = ProductExport::whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $fromDateProductExIm = date('Y-m-01', strtotime('first day of last month'));
                $toDateProductExIm = date('Y-m-t', strtotime('last day of last month'));
                $response = [
                    'hangNhap' => $hangNhap,
                    'hangXuat' => $hangXuat,
                    'ngayBatDau' => $fromDateProductExIm,
                    'ngayKetThuc' => $toDateProductExIm
                ];
            }
            if ($dataValue == 3) {
                //lấy dữ liệu của 3 tháng trước
                $hangNhap = ProductImport::whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $hangXuat = ProductExport::whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $fromDateProductExIm = date('Y-m-01', strtotime('first day of -3 months'));
                $toDateProductExIm = date('Y-m-t', strtotime('last day of -1 months'));
                $response = [
                    'hangNhap' => $hangNhap,
                    'hangXuat' => $hangXuat,
                    'ngayBatDau' => $fromDateProductExIm,
                    'ngayKetThuc' => $toDateProductExIm
                ];
            }
        }
        if ($dataName == "hangTNTH") {
            if ($dataValue == 0) {
                $hangTiepNhan = ReceivedProduct::count();
                $hangTraHang = SerialNumber::where('status', 4)->count();
                $fromDateReceiveReturn = collect([ReceivedProduct::min('created_at'), SerialNumber::where('status', 4)->min('created_at')])->filter()->min();
                $toDateReceiveReturn = collect([ReceivedProduct::max('created_at'), SerialNumber::where('status', 4)->max('created_at')])->filter()->max();
                $response = [
                    'hangTiepNhan' => $hangTiepNhan,
                    'hangTra' => $hangTraHang,
                    'ngayBatDau' => $fromDateReceiveReturn,
                    'ngayKetThuc' => $toDateReceiveReturn
                ];
            }
            if ($dataValue == 1) {
                //Lấy dữ liệu của tháng này
                $hangTiepNhan = ReceivedProduct::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $hangTraHang = SerialNumber::where('status', 4)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $fromDateReceiveReturn = date('Y-m-01');
                $toDateReceiveReturn = date('Y-m-t');
                $response = [
                    'hangTiepNhan' => $hangTiepNhan,
                    'hangTra' => $hangTraHang,
                    'ngayBatDau' => $fromDateReceiveReturn,
                    'ngayKetThuc' => $toDateReceiveReturn
                ];
            }
            if ($dataValue == 2) {
                //Lấy dữ liệu của tháng trước
                $hangTiepNhan = ReceivedProduct::whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $hangTraHang = SerialNumber::where('status', 4)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $fromDateReceiveReturn = date('Y-m-01', strtotime('first day of last month'));
                $toDateReceiveReturn = date('Y-m-t', strtotime('last day of last month'));
                $response = [
                    'hangTiepNhan' => $hangTiepNhan,
                    'hangTra' => $hangTraHang,
                    'ngayBatDau' => $fromDateReceiveReturn,
                    'ngayKetThuc' => $toDateReceiveReturn
                ];
            }
            if ($dataValue == 3) {
                //lấy dữ liệu của 3 tháng trước
                $hangTiepNhan = ReceivedProduct::whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $hangTraHang = SerialNumber::where('status', 4)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $fromDateReceiveReturn = date('Y-m-01', strtotime('first day of -3 months'));
                $toDateReceiveReturn = date('Y-m-t', strtotime('last day of -1 months'));
                $response = [
                    'hangTiepNhan' => $hangTiepNhan,
                    'hangTra' => $hangTraHang,
                    'ngayBatDau' => $fromDateReceiveReturn,
                    'ngayKetThuc' => $toDateReceiveReturn
                ];
            }
        }
        if ($dataName == "phieuBG") {
            if ($dataValue == 0) {
                $phieuHoanThanh = ReturnForm::where('status', 1)->count();
                $phieuKhongDongY = ReturnForm::where('status', 2)->count();
                $tongTienHoanThanh = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 1)
                    ->sum('quotations.total_amount');
                $tongTienKhongDongY = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 2)
                    ->sum('quotations.total_amount');
                $dates = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->selectRaw('MIN(quotations.created_at) as min_date, MAX(quotations.created_at) as max_date')
                    ->first();
                $fromDateQuotation = $dates->min_date;
                $toDateQuotation = $dates->max_date;
                $response = [
                    'phieuHoanThanh' => $phieuHoanThanh,
                    'phieuKhongDongY' => $phieuKhongDongY,
                    'tongTienHoanThanh' => $tongTienHoanThanh,
                    'tongTienKhongDongY' => $tongTienKhongDongY,
                    'ngayBatDau' => $fromDateQuotation,
                    'ngayKetThuc' => $toDateQuotation
                ];
            }
            if ($dataValue == 1) {
                //Lấy dữ liệu của tháng này
                $phieuHoanThanh = ReturnForm::where('status', 1)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $phieuKhongDongY = ReturnForm::where('status', 2)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
                $tongTienHoanThanh = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 1)
                    ->whereMonth('quotations.created_at', date('m'))
                    ->whereYear('quotations.created_at', date('Y'))
                    ->sum('quotations.total_amount');
                $tongTienKhongDongY = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 2)
                    ->whereMonth('quotations.created_at', date('m'))
                    ->whereYear('quotations.created_at', date('Y'))
                    ->sum('quotations.total_amount');
                $dates = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->whereMonth('quotations.created_at', date('m'))
                    ->whereYear('quotations.created_at', date('Y'))
                    ->selectRaw('MIN(quotations.created_at) as min_date, MAX(quotations.created_at) as max_date')
                    ->first();
                $fromDate = date('Y-m-01');
                $toDate = date('Y-m-t');
                $response = [
                    'phieuHoanThanh' => $phieuHoanThanh,
                    'phieuKhongDongY' => $phieuKhongDongY,
                    'tongTienHoanThanh' => $tongTienHoanThanh,
                    'tongTienKhongDongY' => $tongTienKhongDongY,
                    'ngayBatDau' => $fromDate,
                    'ngayKetThuc' => $toDate
                ];
            }
            if ($dataValue == 2) {
                //Lấy dữ liệu của tháng trước
                $phieuHoanThanh = ReturnForm::where('status', 1)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $phieuKhongDongY = ReturnForm::where('status', 2)->whereMonth('created_at', date('m', strtotime('-1 month')))->whereYear('created_at', date('Y', strtotime('-1 month')))->count();
                $tongTienHoanThanh = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 1)
                    ->whereMonth('quotations.created_at', date('m', strtotime('-1 month')))
                    ->whereYear('quotations.created_at', date('Y', strtotime('-1 month')))
                    ->sum('quotations.total_amount');
                $tongTienKhongDongY = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 2)
                    ->whereMonth('quotations.created_at', date('m', strtotime('-1 month')))
                    ->whereYear('quotations.created_at', date('Y', strtotime('-1 month')))
                    ->sum('quotations.total_amount');
                $fromDate = date('Y-m-01', strtotime('first day of last month'));
                $toDate = date('Y-m-t', strtotime('last day of last month'));
                $response = [
                    'phieuHoanThanh' => $phieuHoanThanh,
                    'phieuKhongDongY' => $phieuKhongDongY,
                    'tongTienHoanThanh' => $tongTienHoanThanh,
                    'tongTienKhongDongY' => $tongTienKhongDongY,
                    'ngayBatDau' => $fromDate,
                    'ngayKetThuc' => $toDate
                ];
            }
            if ($dataValue == 3) {
                //lấy dữ liệu của 3 tháng trước
                $phieuHoanThanh = ReturnForm::where('status', 1)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $phieuKhongDongY = ReturnForm::where('status', 2)->whereMonth('created_at', date('m', strtotime('-3 month')))->whereYear('created_at', date('Y', strtotime('-3 month')))->count();
                $tongTienHoanThanh = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 1)
                    ->whereMonth('quotations.created_at', date('m', strtotime('-3 month')))
                    ->whereYear('quotations.created_at', date('Y', strtotime('-3 month')))
                    ->sum('quotations.total_amount');
                $tongTienKhongDongY = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                    ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                    ->where('return_form.status', 2)
                    ->whereMonth('quotations.created_at', date('m', strtotime('-3 month')))
                    ->whereYear('quotations.created_at', date('Y', strtotime('-3 month')))
                    ->sum('quotations.total_amount');
                $fromDate = date('Y-m-01', strtotime('first day of -3 months'));
                $toDate = date('Y-m-t', strtotime('last day of -1 months'));
                $response = [
                    'phieuHoanThanh' => $phieuHoanThanh,
                    'phieuKhongDongY' => $phieuKhongDongY,
                    'tongTienHoanThanh' => $tongTienHoanThanh,
                    'tongTienKhongDongY' => $tongTienKhongDongY,
                    'ngayBatDau' => $fromDate,
                    'ngayKetThuc' => $toDate
                ];
            }
        }
        return response()->json([
            'details' => $response
        ]);
    }

    public function filterReportPeriodTime(Request $request)
    {
        $date_start = $request->date_start ? $request->date_start . ' 00:00:00' : null;
        $date_end = $request->date_end ? $request->date_end . ' 23:59:59' : null;
        $dataName = $request->dataName;
        $response = [];
        if ($dataName == 'phieuXN') {
            $phieuNhap = Imports::whereBetween('date_create', [$date_start, $date_end])->count();
            $phieuXuat = Exports::whereBetween('date_create', [$date_start, $date_end])->count();
            $response = [
                'phieuNhap' => $phieuNhap,
                'phieuXuat' => $phieuXuat,
                'ngayBatDau' => $date_start,
                'ngayKetThuc' => $date_end
            ];
        }
        if ($dataName == "phieuTNTH") {
            $phieuTiepNhan = Receiving::whereBetween('created_at', [$date_start, $date_end])->count();
            $phieuTraHang = ReturnForm::whereBetween('created_at', [$date_start, $date_end])->count();
            $phieuChuaXL = Receiving::where('state', 1)->whereBetween('created_at', [$date_start, $date_end])->count();
            $phieuQuaHan = Receiving::where('state', 2)->whereBetween('created_at', [$date_start, $date_end])->count();
            $response = [
                'phieuTiepNhan' => $phieuTiepNhan,
                'phieuTraHang' => $phieuTraHang,
                'phieuChuaXL' => $phieuChuaXL,
                'phieuQuaHan' => $phieuQuaHan,
                'ngayBatDau' => $date_start,
                'ngayKetThuc' => $date_end
            ];
        }
        if ($dataName == 'baoCaoTK') {
            $tonKho = SerialNumber::whereIn('status', [1, 5])->whereBetween('created_at', [$date_start, $date_end])->count();
            $hangMuon = SerialNumber::where('status', 5)->whereBetween('created_at', [$date_start, $date_end])->count();
            $toiHanBT = InventoryLookup::where('status', 1)->whereBetween('created_at', [$date_start, $date_end])->count();
            $response = [
                'tonKho' => $tonKho,
                'hangMuon' => $hangMuon,
                'toiHanBT' => $toiHanBT,
                'ngayBatDau' => $date_start,
                'ngayKetThuc' => $date_end
            ];
        }
        if ($dataName == 'hangXN') {
            $hangNhap = ProductImport::whereBetween('created_at', [$date_start, $date_end])->count();
            $hangXuat = ProductExport::whereBetween('created_at', [$date_start, $date_end])->count();
            $response = [
                'hangNhap' => $hangNhap,
                'hangXuat' => $hangXuat,
                'ngayBatDau' => $date_start,
                'ngayKetThuc' => $date_end
            ];
        }
        if ($dataName == "hangTNTH") {
            $hangTiepNhan = ReceivedProduct::whereBetween('created_at', [$date_start, $date_end])->count();
            $hangTraHang = SerialNumber::where('status', 4)->whereBetween('created_at', [$date_start, $date_end])->count();
            $response = [
                'hangTiepNhan' => $hangTiepNhan,
                'hangTra' => $hangTraHang,
                'ngayBatDau' => $date_start,
                'ngayKetThuc' => $date_end
            ];
        }
        if ($dataName == "phieuBG") {
            $phieuHoanThanh = ReturnForm::where('status', 1)->whereBetween('created_at', [$date_start, $date_end])->count();
            $phieuKhongDongY = ReturnForm::where('status', 2)->whereBetween('created_at', [$date_start, $date_end])->count();
            $tongTienHoanThanh = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                ->where('return_form.status', 1)
                ->whereBetween('return_form.created_at', [$date_start, $date_end])
                ->sum('quotations.total_amount');
            $tongTienKhongDongY = ReturnForm::join('receiving', 'return_form.reception_id', '=', 'receiving.id')
                ->join('quotations', 'receiving.id', '=', 'quotations.reception_id')
                ->where('return_form.status', 2)
                ->whereBetween('return_form.created_at', [$date_start, $date_end])
                ->sum('quotations.total_amount');
            $response = [
                'phieuHoanThanh' => $phieuHoanThanh,
                'phieuKhongDongY' => $phieuKhongDongY,
                'tongTienHoanThanh' => $tongTienHoanThanh,
                'tongTienKhongDongY' => $tongTienKhongDongY,
                'ngayBatDau' => $date_start,
                'ngayKetThuc' => $date_end
            ];
        }
        return response()->json([
            'details' => $response
        ]);
    }

    public function filterExportImport(Request $request)
    {
        $data = $request->all();
        $filters = [];
        $currentMonth = null;
        $currentQuarter = null;
        $currentYear = null;
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã hàng: ' . $data['ma'], 'name' => 'ma-hang', 'icon' => 'po'];
        }
        if (isset($data['ten']) && $data['ten'] !== null) {
            $filters[] = ['value' => 'Mã hàng: ' . $data['ten'], 'name' => 'ten-hang', 'icon' => 'po'];
        }
        if (isset($data['so_luong_nhap']) && $data['so_luong_nhap'][1] !== null) {
            $filters[] = ['value' => 'Số lượng nhập ' . $data['so_luong_nhap'][0] . ' ' . $data['so_luong_nhap'][1], 'name' => 'so-luong-nhap', 'icon' => 'money'];
        }
        if (isset($data['so_luong_xuat']) && $data['so_luong_xuat'][1] !== null) {
            $filters[] = ['value' => 'Số lượng xuất ' . $data['so_luong_xuat'][0] . ' ' . $data['so_luong_xuat'][1], 'name' => 'so-luong-xuat', 'icon' => 'money'];
        }
        // Xử lý dữ liệu từ $data
        if (isset($data['type']) && isset($data['month']) || isset($data['quarter']) || isset($data['year'])) {
            $value = ''; // Giá trị mặc định
            if ($data['type'] === 'thang' && isset($data['month']) && isset($data['year'])) {
                $value = 'THÁNG ' . $data['month'] . '/' . $data['year'];
                $currentMonth = $data['month'];
                $currentYear = $data['year'];
            } elseif ($data['type'] === 'quy' && isset($data['quarter']) && isset($data['year'])) {
                $value = 'QUÝ ' . $data['quarter'] . '/' . $data['year'];
                $currentQuarter = $data['quarter'];
                $currentYear = $data['year'];
            } elseif ($data['type'] === 'nam' && isset($data['year'])) {
                $value = 'NĂM ' . $data['year'];
                $currentYear = $data['year'];
            }
            $filters[] = ['value' => $value];
        } else if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Khoảng thời gian: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay', 'icon' => 'date'];
        }
        if ($request->ajax()) {
            $reports = $this->reports->getProductsAjax($data);
            $result = $this->reports->mergeProductData($data, $reports);
            return response()->json([
                'data' => $result,
                'filters' => $filters,
            ]);
        }
        return false;
    }
    public function filterReceiptReturn(Request $request)
    {
        $data = $request->all();
        $filters = [];
        $currentMonth = null;
        $currentQuarter = null;
        $currentYear = null;
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã hàng: ' . $data['ma'], 'name' => 'ma-hang', 'icon' => 'po'];
        }
        if (isset($data['ten']) && $data['ten'] !== null) {
            $filters[] = ['value' => 'Tên hàng: ' . $data['ten'], 'name' => 'ten-hang', 'icon' => 'po'];
        }
        if (isset($data['so_luong_nhap']) && $data['so_luong_nhap'][1] !== null) {
            $filters[] = ['value' => 'Hàng tiếp nhận ' . $data['so_luong_nhap'][0] . ' ' . $data['so_luong_nhap'][1], 'name' => 'hang-tiep-nhan', 'icon' => 'money'];
        }
        if (isset($data['so_luong_xuat']) && $data['so_luong_xuat'][1] !== null) {
            $filters[] = ['value' => 'hàng đã trả ' . $data['so_luong_xuat'][0] . ' ' . $data['so_luong_xuat'][1], 'name' => 'hang-da-tra', 'icon' => 'money'];
        }
        // Xử lý dữ liệu từ $data
        if (isset($data['type']) && isset($data['month']) || isset($data['quarter']) || isset($data['year'])) {
            $value = ''; // Giá trị mặc định
            if ($data['type'] === 'thang' && isset($data['month']) && isset($data['year'])) {
                $value = 'THÁNG ' . $data['month'] . '/' . $data['year'];
                $currentMonth = $data['month'];
                $currentYear = $data['year'];
            } elseif ($data['type'] === 'quy' && isset($data['quarter']) && isset($data['year'])) {
                $value = 'QUÝ ' . $data['quarter'] . '/' . $data['year'];
                $currentQuarter = $data['quarter'];
                $currentYear = $data['year'];
            } elseif ($data['type'] === 'nam' && isset($data['year'])) {
                $value = 'NĂM ' . $data['year'];
                $currentYear = $data['year'];
            }
            $filters[] = ['value' => $value];
        } else if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Khoảng thời gian: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay', 'icon' => 'date'];
        }
        if ($request->ajax()) {
            $reports = $this->reports->getAjaxReceiptReturn($data);
            $result = $this->reports->mergeProductData($data, $reports);
            $count = $this->reports->countReceiptReturn($result);
            return response()->json([
                'data' => $result,
                'count' => $count,
                'filters' => $filters,
            ]);
        }
        return false;
    }
    public function filterQuotation(Request $request)
    {
        $data = $request->all();
        $filters = [];
        $currentMonth = null;
        $currentQuarter = null;
        $currentYear = null;
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã phiếu: ' . $data['ma'], 'name' => 'ma-phieu', 'icon' => 'po'];
        }
        if (isset($data['receiving_code']) && $data['receiving_code'] !== null) {
            $filters[] = ['value' => 'Phiếu tiếp nhận: ' . $data['receiving_code'], 'name' => 'phieu-tiep-nhan', 'icon' => 'po'];
        }
        if (isset($data['customer']) && $data['customer'] !== null) {
            $filters[] = ['value' => 'Khách hàng: ' . count($data['customer']) . ' đã chọn', 'name' => 'khách hàng', 'icon' => 'user'];
        }
        if (isset($data['tong_tien']) && $data['tong_tien'][1] !== null) {
            $filters[] = ['value' => 'Bảo hành: ' . $data['tong_tien'][0] . ' ' . $data['tong_tien'][1], 'name' => 'tong-tien', 'icon' => 'money'];
        }
        if (isset($data['status']) && $data['status'] !== null) {
            $statusValues = [];
            if (in_array(3, $data['status'])) {
                $statusValues[] = '<span style="color:08AA36BF;">Hoàn thành</span>';
            }
            if (in_array(4, $data['status'])) {
                $statusValues[] = '<span style="color:#dc3545;">Không đồng ý</span>';
            }
            $filters[] = ['value' => 'Tình trạng: ' . implode(', ', $statusValues), 'name' => 'tinh-trang', 'icon' => 'status'];
        }

        // Xử lý dữ liệu từ $data
        if (isset($data['type']) && isset($data['month']) || isset($data['quarter']) || isset($data['year'])) {
            $value = ''; // Giá trị mặc định
            if ($data['type'] === 'thang' && isset($data['month']) && isset($data['year'])) {
                $value = 'THÁNG ' . $data['month'] . '/' . $data['year'];
                $currentMonth = $data['month'];
                $currentYear = $data['year'];
            } elseif ($data['type'] === 'quy' && isset($data['quarter']) && isset($data['year'])) {
                $value = 'QUÝ ' . $data['quarter'] . '/' . $data['year'];
                $currentQuarter = $data['quarter'];
                $currentYear = $data['year'];
            } elseif ($data['type'] === 'nam' && isset($data['year'])) {
                $value = 'NĂM ' . $data['year'];
                $currentYear = $data['year'];
            }
            $filters[] = ['value' => $value];
        } else if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Khoảng thời gian: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay', 'icon' => 'date'];
        }
        if ($request->ajax()) {
            $result = $this->reports->getAjaxRPQuotation($data);
            $countTotal = $this->reports->statusCountTotal($result);
            return response()->json([
                'data' => $result,
                'countTotal' => $countTotal,
                'filters' => $filters,
            ]);
        }
        return false;
    }
}
