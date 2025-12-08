<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImport;
use App\Models\SerialNumber;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $warehouseTransfer;
    private $warehouseTransferItem;
    public function __construct()
    {
        $this->warehouseTransfer = new WarehouseTransfer();
        $this->warehouseTransferItem = new WarehouseTransferItem();
    }
    public function index()
    {
        $title = 'Phiếu chuyển kho';
        $warehouseTransfer = $this->warehouseTransfer->orderBy('id', 'desc')->get();
        $warehouse = Warehouse::all();
        return view('expertise.warehouseTransfer.index', compact('title', 'warehouseTransfer', 'warehouse'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Phiếu chuyển kho';
        $export_code = $this->warehouseTransfer->generateExportCode();
        $warehouse = Warehouse::all();
        $products = Product::all();
        return view('expertise.warehouseTransfer.create', compact('title', 'export_code', 'warehouse', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $id = $this->warehouseTransfer->add($data);
        $this->warehouseTransferItem->addItemWarehouseTransfer($data, $id);
        return redirect()->route('warehouseTransfer.index')->with('msg', 'Tạo mới phiếu chuyển kho thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WarehouseTransfer $warehouseTransfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WarehouseTransfer $warehouseTransfer)
    {
        $title = 'Phiếu chuyển kho';
        $warehouseTransfer = WarehouseTransfer::findorFail($warehouseTransfer->id);
        $warehouse = Warehouse::all();
        $productAll = Product::all();
        $productWarehouse = WarehouseTransferItem::where('transfer_id', $warehouseTransfer->id)
            ->get()->groupBy('product_id');
        return view('expertise.warehouseTransfer.edit', compact('title', 'warehouseTransfer', 'warehouse', 'productWarehouse', 'productAll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WarehouseTransfer $warehouseTransfer)
    {
        $warehouseTransfer = WarehouseTransfer::findorFail($warehouseTransfer->id);
        $data = $request->all();

        // Chỉ cập nhật transfer_date và note của warehouse transfer
        $warehouseTransfer->transfer_date = $data['transfer_date'];
        $note = $data['note'];
        if (empty(trim($note))) {
            // Lấy danh sách mã hàng và serial từ data-test
            $productData = [];
            if (isset($data['data-test'])) {
                $uniqueProductsArray = json_decode($data['data-test'], true);
                if (is_array($uniqueProductsArray)) {
                    foreach ($uniqueProductsArray as $serial) {
                        if (isset($serial['product_id'])) {
                            $product = Product::find($serial['product_id']);
                            if ($product && $product->product_code) {
                                $productCode = $product->product_code;
                                if (!isset($productData[$productCode])) {
                                    $productData[$productCode] = [];
                                }
                                
                                if ($data['from_warehouse_id'] == 2) {
                                    // Trả bảo hành: hiển thị serial trả -> serial mượn
                                    $serialReturn = isset($serial['serial']) ? trim($serial['serial']) : '';
                                    $serialBorrow = isset($serial['serialBorrow']) ? trim($serial['serialBorrow']) : '';
                                    if ($serialReturn && $serialBorrow) {
                                        $productData[$productCode][] = "{$serialReturn} -> {$serialBorrow}";
                                    }
                                } else {
                                    // Mượn: hiển thị serial trả
                                    $serialReturn = isset($serial['serial']) ? trim($serial['serial']) : '';
                                    if ($serialReturn) {
                                        $productData[$productCode][] = $serialReturn;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Tạo chuỗi note với format khác nhau cho từng loại
            $noteParts = [];
            foreach ($productData as $productCode => $serials) {
                $serialsStr = implode(', ', array_unique($serials));
                if ($data['from_warehouse_id'] == 2) {
                    $noteParts[] = "{$productCode} ({$serialsStr})";
                } else {
                    $noteParts[] = "{$productCode} ({$serialsStr})";
                }
            }
            $noteContent = implode(', ', $noteParts);
            
            if ($data['from_warehouse_id'] == 2) {
                $note = "TRẢ BẢO HÀNH {$noteContent}";
            } else {
                $note = "MƯỢN {$noteContent}";
            }
        }
        $warehouseTransfer->note = $note;
        $warehouseTransfer->save();

        // Cập nhật note_seri của các warehouse transfer items
        $this->warehouseTransferItem->updateItemWarehouseTransfer($data, $warehouseTransfer->id);

        return redirect()->route('warehouseTransfer.index')->with('msg', 'Cập nhật phiếu chuyển kho thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WarehouseTransfer $warehouseTransfer)
    {
        //Xóa phiếu chuyển kho
        $warehouseTransfer = WarehouseTransfer::findorFail($warehouseTransfer->id);
        if ($warehouseTransfer) {
            $warehouseTransferItem = WarehouseTransferItem::where('transfer_id', $warehouseTransfer->id)->get();
            foreach ($warehouseTransferItem as $item) {
                if ($warehouseTransfer->from_warehouse_id == 1) {
                    $serial = SerialNumber::where('id', $item->serial_number_id)->first();
                    if ($serial) {
                        $serial->status = 1;
                        $serial->warehouse_id = $warehouseTransfer->from_warehouse_id;
                        $serial->save();
                        
                        // Cập nhật warehouse_id trong inventory_lookup
                        DB::table('inventory_lookup')
                            ->where('sn_id', $serial->id)
                            ->update(['warehouse_id' => $warehouseTransfer->from_warehouse_id]);
                    }
                    $item->delete();
                } else {
                    $serial = SerialNumber::where('id', $item->serial_number_id)->first();
                    // Chỉ xóa Serial nếu KHÔNG tồn tại trong ProductImport
                    $existsInImport = ProductImport::where('sn_id', $serial->id)->exists();
                    if (!$existsInImport) {
                        $serial->delete();
                    } else {
                        // Nếu đã có trong import, khôi phục về kho nguồn và trạng thái mượn
                        $serial->status = 1;
                        $serial->warehouse_id = $warehouseTransfer->from_warehouse_id;
                        $serial->save();

                        //Cập nhật tra cứu tồn kho
                        DB::table('inventory_lookup')
                            ->where('sn_id', $serial->id)
                            ->update(['warehouse_id' => $warehouseTransfer->from_warehouse_id]);
                    }
                    $borrow = SerialNumber::where('id', $item->sn_id_borrow)->first();
                    if ($borrow) {
                        $borrow->status = 5;
                        $borrow->warehouse_id = $warehouseTransfer->from_warehouse_id;
                        $borrow->save();
                        
                        // Cập nhật warehouse_id trong inventory_lookup cho serial borrow
                        DB::table('inventory_lookup')
                            ->where('sn_id', $borrow->id)
                            ->update(['warehouse_id' => $warehouseTransfer->from_warehouse_id]);
                    }
                    $item->delete();
                }
            }
            $warehouseTransfer->delete();
            return redirect()->route('warehouseTransfer.index')->with('msg', 'Xóa phiếu chuyển kho thành công!');
        } else {
            return redirect()->route('warehouseTransfer.index')->with('warning', 'Xóa phiếu chuyển kho thất bại!');
        }
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã: ' . $data['ma'], 'name' => 'ma', 'icon' => 'po'];
        }
        if (isset($data['note']) && $data['note'] !== null) {
            $filters[] = ['value' => 'Ghi chú: ' . $data['note'], 'name' => 'ghi-chu', 'icon' => 'po'];
        }
        if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Ngày lập phiếu: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-lap-phieu', 'icon' => 'date'];
        }
        if (isset($data['kho_chuyen']) && $data['kho_chuyen'] !== null) {
            $filters[] = ['value' => 'Kho chuyển: ' . count($data['kho_chuyen']) . ' đã chọn', 'name' => 'kho-chuyen', 'icon' => 'user'];
        }
        if (isset($data['kho_nhan']) && $data['kho_nhan'] !== null) {
            $filters[] = ['value' => 'Kho nhận: ' . count($data['kho_nhan']) . ' đã chọn', 'name' => 'kho-nhan', 'icon' => 'user'];
        }
        if (isset($data['status']) && $data['status'] !== null) {
            $statusValues = [];
            if (in_array(1, $data['status'])) {
                $statusValues[] = '<span style="color:08AA36BF;">Hoàn thành</span>';
            }
            if (in_array(0, $data['status'])) {
                $statusValues[] = '<span style="color:#dc3545;">Huỷ</span>';
            }
            $filters[] = ['value' => 'Trạng thái: ' . implode(', ', $statusValues), 'name' => 'trang-thai', 'icon' => 'status'];
        }
        if ($request->ajax()) {
            $warehouse = $this->warehouseTransfer->getAllWarehouseTransfer($data);
            return response()->json([
                'data' => $warehouse,
                'filters' => $filters,
            ]);
        }
        return false;
    }
}
