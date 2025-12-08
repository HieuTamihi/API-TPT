<?php

namespace App\Http\Controllers;

use App\Models\InventoryLookup;
use App\Models\ProductExport;
use App\Models\ProductImport;
use App\Models\SerialNumber;
use App\Models\WarehouseTransferItem;
use App\Models\ProductReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SerialNumberController extends Controller
{
    public function checkSN(Request $request)
    {
        $productId = $request->input('product_id');
        $serial = $request->input('serial');
        $qty = $request->input('qty');
        $exists = false;
        $sn = SerialNumber::where('serial_code', $serial)
            ->where('product_id', $productId)->first();
        $availableQty = 0;
        if ($request->nameModal == "XH") {
            if ($sn) {
                $availableQty = InventoryLookup::where('product_id', $productId)
                    ->where('sn_id', $sn->id)
                    ->sum('remaining_quantity');

                $exists = $sn->status == 1 && $availableQty >= ($request->qty ?? 1);
            } else {
                // Không có serial: kiểm tra tồn kho tổng
                $lookup = InventoryLookup::where('product_id', $productId)
                    ->where('sn_id', 0)
                    ->sum('remaining_quantity');
                $availableQty = (int)$lookup ?? 0;
                $exists = $availableQty >= $qty;
            }
        }
        if ($request->nameModal == "CXH") {
            if ($request->serial) {
                $sn = SerialNumber::where('serial_code', $request->serial)
                    ->where('product_id', $productId)->first();

                if (!$sn) {
                    // SN không tồn tại trong hệ thống
                    return response()->json([
                        'exists' => false,
                        'remaining_quantity' => 0,
                        'available_quantity' => 0,
                    ]);
                }

                // --- Sản phẩm có serial ---
                $lookup = InventoryLookup::where('product_id', $productId)
                    ->where('sn_id', $sn->id)
                    ->first();

                $isInExport = ProductExport::where('product_id', $productId)
                    ->where('sn_id', $sn->id)
                    ->where('export_id', $request->import_id)
                    ->exists();

                $alreadyExportedQty = ProductExport::where('product_id', $productId)
                    ->where('sn_id', $sn->id)
                    ->where('export_id', $request->import_id)
                    ->sum('quantity');

                $availableQty = ($lookup ? $lookup->remaining_quantity : 0) + $alreadyExportedQty;

                if ($isInExport) {
                    $exists = true;
                } else {
                    $exists = $sn->status == 1 && $availableQty > 0;
                }
            } else {
                // --- Sản phẩm không serial ---
                $lookupQty = InventoryLookup::where('product_id', $productId)
                    ->where('sn_id', 0)
                    ->sum('remaining_quantity');

                $exportedQty = ProductExport::where('export_id', $request->import_id)
                    ->where('product_id', $productId)
                    ->where('sn_id', 0)
                    ->sum('quantity');

                $availableQty = $lookupQty + $exportedQty;
            }
        }
        if ($request->nameModal == "PCK") {
            if ($request->warehouse_id == 1) {
                $exists = SerialNumber::where('serial_code', $serial)
                    ->where('product_id', $productId)
                    ->where('status', 1)
                    ->exists();
            }
            if ($request->warehouse_id == 2) {
                $serial_borrow = $request->input('serial_borrow') ?? $request->input('serialBorrow');
                if ($serial_borrow) {
                    $serial_borrow = strtoupper(trim($serial_borrow));
                }
                // Hợp lệ nếu serial không tồn tại HOẶC tồn tại nhưng đang ở kho bảo hành (2) và đúng product_id
                $serialRecord = SerialNumber::where('serial_code', $serial)
                    ->where('product_id', $productId)
                    ->first();
                $existsSerial = !$serialRecord || ((int)$serialRecord->warehouse_id === 2);

                // Cho phép serial mượn có status = 5
                // hoặc status = 2 nếu đã được dùng làm serial thay thế trong phiếu trả hàng
                $existsSerialBorrow = false;
                if ($serial_borrow) {
                    $borrowSn = SerialNumber::where('serial_code', $serial_borrow)->first();
                    if ($borrowSn) {
                        if ((int)$borrowSn->status === 5) {
                            $existsSerialBorrow = true;
                        } elseif ((int)$borrowSn->status === 2) {
                            // Chấp nhận nếu đang ở kho bảo hành hoặc đã dùng làm serial thay thế
                            $existsSerialBorrow = ((int)$borrowSn->warehouse_id === 2)
                                || ProductReturn::where('replacement_serial_number_id', $borrowSn->id)->exists();
                        }
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'existsSerial' => $existsSerial,
                    'existsSerialBorrow' => $existsSerialBorrow
                ]);
            }
        }

        return response()->json([
            'exists' => $exists,
            'remaining_quantity' => $lookup?->remaining_quantity ?? 0,
            'available_quantity' => $availableQty ?? 0,
            'serial_exists' => $sn ? true : false,
            'enough_stock' => $availableQty >= ($qty ?? 1),
        ]);
    }

    public function checkSNImport(Request $request)
    {
        $productId = $request->input('product_id');
        $serialNumber = $request->input('serial_number');
        // dd($request->all());
        // Kiểm tra số serial trong cơ sở dữ liệu
        if ($request->nameModal == "XH") {
            // Kiểm tra trong bảng serial_numbers
            $exists = SerialNumber::where('serial_code', $serialNumber)
                ->where('product_id', $productId)
                ->where('status', 1)
                ->exists();
            if (!$exists) {
                return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ.']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
            }
        }
        if ($request->nameModal == "TN") {
            if ($request->branch_id == 1) {
                $seri = $this->getSerial($serialNumber);
                if (!$seri) {
                    return response()->json(['status' => 'error', 'message' => 'Số serial không tồn tại.']);
                }
                // Logic đặc biệt cho form_type = 3
                if ($request->form_type == 3) {
                    $seriWithStatus4Or5 = $this->getSerialWithStatus($serialNumber, [4, 5]);
                    if (!$seriWithStatus4Or5) {
                        return response()->json(['status' => 'error', 'message' => 'Số serial không nằm trong diện bảo hành dịch vụ.']);
                    }
                    $seri = $seriWithStatus4Or5;
                }
                $warranty = $this->getWarranty($seri->id, $request->product_id);
                if (!$warranty) {
                    return response()->json(['status' => 'error', 'message' => 'Không tìm thấy thông tin bảo hành.']);
                }

                return $this->evaluateWarrantyByFormType($warranty->status, $request->form_type);
            }
            if ($request->branch_id == 2) {
                $seri = $this->getSerial($serialNumber);
                if ($seri) {
                    return response()->json(['status' => 'error', 'message' => 'Số serial thuộc trong kho, không phải hàng bên ngoài.']);
                } else {
                    return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
                }
            }
        }
        if ($request->nameModal == "PCK") {
            if ($request->warehouse == 1) {
                // Kiểm tra trong bảng serial_numbers
                $exists = SerialNumber::where('serial_code', $serialNumber)
                    ->where('product_id', $productId)
                    ->where('status', 1)
                    ->exists();
                if (!$exists) {
                    return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ.']);
                } else {
                    return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
                }
            } else if ($request->warehouse == 2) {
                // Hợp lệ nếu serial không tồn tại HOẶC tồn tại nhưng đang ở kho bảo hành (2) và đúng product_id
                $serialRecord = SerialNumber::where('serial_code', $serialNumber)
                    ->where('product_id', $productId)
                    ->first();
                if (!$serialRecord || ((int)$serialRecord->warehouse_id === 2)) {
                    return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
                }
                return response()->json(['status' => 'error', 'message' => 'Số serial tồn tại nhưng không nằm trong kho Bảo hành.']);
            }
        }
        if ($request->nameModal == "CPCK") {
            if ($request->warehouse == 1) {
                // Lấy ID của serial cần kiểm tra
                $serial = SerialNumber::where('serial_code', $serialNumber)
                    ->first();

                if (!$serial) {
                    return response()->json(['status' => 'error', 'message' => 'Số serial không tồn tại.']);
                }

                // Kiểm tra nếu serial có status = 1
                $existsInSerials = $serial->status == 1;

                // Kiểm tra nếu serial đã có trong WarehouseTransferItem
                $existsInTransfer = WarehouseTransferItem::where('serial_number_id', $serial->id)->exists();

                if (!$existsInSerials && !$existsInTransfer) {
                    return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ.']);
                } else {
                    return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
                }
            } else if ($request->warehouse == 2) {
                // Lấy serial trong DB
                $serial = SerialNumber::where('serial_code', $serialNumber)
                    ->first();

                // Nếu serial không tồn tại trong DB → Hợp lệ
                if (!$serial) {
                    return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
                }

                // Kiểm tra nếu serial đã có trong WarehouseTransferItem và có transfer_id trùng với warehouseTransferId
                $existsInTransfer = WarehouseTransferItem::where('serial_number_id', $serial->id)
                    ->where('transfer_id', $request->warehouseTransferId) // Kiểm tra transfer_id trùng
                    ->exists();

                if ($existsInTransfer) {
                    return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
                }

                // Nếu serial tồn tại nhưng không thuộc warehouseTransferId → Không hợp lệ
                return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ.']);
            }
        }
    }

    public function checkSNImportBorrow(Request $request)
    {
        $serialNumber = $request->input('serial_number');
        $serialNumber = $serialNumber ? strtoupper(trim($serialNumber)) : $serialNumber;
        $productId = $request->input('product_id') ?? $request->input('productId');
        if ($request->nameModal == "PCK") {
            if ($request->warehouse == 2) {
                // Kiểm tra trong bảng serial_numbers
                $snQuery = SerialNumber::where('serial_code', $serialNumber);
                if ($productId) {
                    $snQuery->where('product_id', (int)$productId);
                }
                $sn = $snQuery->first();
                $isValid = false;
                if ($sn) {
                    // Nếu có truyền product_id nhưng không khớp thì coi như không hợp lệ
                    if ($productId && (int)$sn->product_id !== (int)$productId) {
                        return response()->json(['status' => 'error', 'message' => 'Serial không thuộc sản phẩm này.']);
                    }
                    if ((int)$sn->status === 5) {
                        $isValid = true;
                    } elseif ((int)$sn->status === 2) {
                        // Hợp lệ nếu đang ở kho BH hoặc đã dùng làm serial thay thế
                        $isValid = ((int)$sn->warehouse_id === 2)
                            || ProductReturn::where('replacement_serial_number_id', $sn->id)->exists();
                    }
                }
                if (!$isValid) {
                    return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ hoặc không tồn tại.']);
                }
                return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
            }
        }
        // if ($request->nameModal == "CPCK") {
        //     if ($request->warehouse == 2) {
        //         // Lấy ID của serial cần kiểm tra
        //         $serial = SerialNumber::where('serial_code', $serialNumber)
        //             ->first();

        //         if (!$serial) {
        //             return response()->json(['status' => 'error', 'message' => 'Số serial không tồn tại.']);
        //         }

        //         // Kiểm tra nếu serial có status = 5 hoặc status = 2 hợp lệ
        //         $existsInSerials = ((int)$serial->status === 5)
        //             || (((int)$serial->status === 2) && (((int)$serial->warehouse_id === 2)
        //                 || ProductReturn::where('replacement_serial_number_id', $serial->id)->exists()));

        //         // Kiểm tra nếu serial đã có trong WarehouseTransferItem
        //         $existsInTransfer = WarehouseTransferItem::where('sn_id_borrow', $serial->id)->exists();

        //         if (!$existsInSerials && !$existsInTransfer) {
        //             return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ.']);
        //         } else {
        //             return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
        //         }
        //     }
        // }
    }
    private function getSerialWarranty($serialCode)
    {
        return SerialNumber::with('warrantyLookups')->where('serial_code', $serialCode)->first();
    }
    private function getSerial($serialCode)
    {
        return DB::table('serial_numbers')->where('serial_code', $serialCode)->first();
    }

    public function getSerialWithStatus($serialNumber, $statuses)
    {
        return SerialNumber::where('serial_code', $serialNumber)
            ->whereIn('status', (array) $statuses) // Chấp nhận một giá trị hoặc mảng
            ->first();
    }

    private function getWarranty($serialId, $productId = null)
    {
        $query = DB::table('warranty_lookup')->where('sn_id', $serialId);
        if ($productId) {
            $query->where('product_id', $productId);
        }
        return $query->first();
    }
    private function evaluateWarrantyByFormType($status, $formType)
    {
        if ($formType == 1) { // Loại phiếu bảo hành
            if ($status == 0) {
                return response()->json(['status' => 'success', 'message' => 'Số serial còn bảo hành.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Số serial hết bảo hành.']);
            }
        }

        if ($formType == 2) { // Loại phiếu dịch vụ
            if ($status == 1) {
                return response()->json(['status' => 'success', 'message' => 'Số serial hết bảo hành.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Số serial còn bảo hành.']);
            }
        }

        if ($formType == 3) { // Loại phiếu bảo hành dịch vụ
            if ($status == 1) {
                return response()->json(['status' => 'error', 'message' => 'Số serial hết bảo hành.']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Số serial còn bảo hành.']);
            }
        }
    }

    public function checkSerial(Request $request)
    {
        $branchId = $request->branch_id;
        $formType = $request->form_type;
        $serialData = $request->serial_data;
        $results = [];

        foreach ($serialData as $data) {
            $serialNumber = $data['serial'];
            $productId = $data['productId'];
            $valid = false; // Mặc định là không hợp lệ
            $message = '';

            if ($branchId == 1) {
                $seri = $this->getSerial($serialNumber);
                if (!$seri) {
                    $message = 'Số serial không tồn tại.';
                } elseif ($formType == 3) {
                    $seriWithStatus4Or5 = $this->getSerialWithStatus($serialNumber, [4, 5]);
                    if (!$seriWithStatus4Or5) {
                        $message = 'Số serial không nằm trong diện bảo hành dịch vụ.';
                    } else {
                        $seri = $seriWithStatus4Or5;
                        $valid = true;
                    }
                } else {
                    $warranty = $this->getWarranty($seri->id, $productId);

                    if (!in_array($formType, [1, 2])) {
                        $message = 'Loại biểu mẫu không hợp lệ.';
                    } elseif (!$warranty) {
                        $message = 'Không tìm thấy thông tin bảo hành.';
                    } else {
                        $isValidType1 = ($formType == 1 && $warranty->status == 0);
                        $isValidType2 = ($formType == 2 && $warranty->status == 1);
                        if ($isValidType1 || $isValidType2) {
                            $valid = true;
                        } else {
                            $message = 'Sản phẩm còn bảo hành.';
                        }
                    }
                }
            } elseif ($branchId == 2) {
                $seri = $this->getSerial($serialNumber);
                if ($seri) {
                    $message = 'Số serial thuộc trong kho, không phải hàng bên ngoài.';
                } else {
                    $valid = true;
                }
            }

            $results[] = [
                'serial' => $serialNumber,
                'valid' => $valid,
                'message' => $message,
            ];
        }

        return response()->json(['status' => 'success', 'serials' => $results]);
    }

    public function checkSerialNumbers(Request $request)
    {
        $productId = $request->input('productId');
        $serialNumbers = $request->input('serialNumbers', []);

        // Tách các serial numbers hợp lệ và không hợp lệ
        $validSerials = [];
        $invalidSerials = [];

        foreach ($serialNumbers as $serial) {
            $exists = SerialNumber::where('product_id', $productId)
                ->where('serial_code', $serial)
                ->where('status', 1)
                ->exists();

            if ($exists) {
                $validSerials[] = $serial;
            } else {
                $invalidSerials[] = $serial;
            }
        }

        return response()->json([
            'exists' => $validSerials,
            'invalid' => $invalidSerials,
        ]);
    }


    public function checkSNReplace(Request $request)
    {
        $seriRecord = SerialNumber::where('serial_code', $request->serialNumber)
            ->where('product_id', $request->product_id)
            ->whereIn('status', [1, 5])
            ->whereIn('warehouse_id', [1, 2])
            ->first();

        if ($seriRecord) {
            return response()->json(['status' => 'success', 'message' => 'Số serial hợp lệ.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Số serial không hợp lệ.']);
        }
    }

    public function checkSerials(Request $request)
    {
        $formType = $request->input('form_type');
        $serialData = $request->input('serials');
        $warranty = $request->input('warranty');

        // dd($request->all());

        $sericheck = SerialNumber::find($serialData);

        if ($sericheck) {
            $warranty = DB::table('warranty_lookup')->where('sn_id', $sericheck->id)->where('id', $warranty)->first();
            if ($warranty) {
                if ($formType == 1) {
                    if ($warranty->status == 0) {
                        return response()->json(['status' => 'success', 'message' => 'Số serial còn bảo hành.']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Số serial hết bảo hành.']);
                    }
                }
                if ($formType == 2) {
                    if ($warranty->status == 1) {
                        return response()->json(['status' => 'success', 'message' => 'Số serial hết bảo hành.']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Số serial còn bảo hành.']);
                    }
                }
                if ($formType == 3) {
                    if ($warranty->status == 2) {
                        return response()->json(['status' => 'success', 'message' => 'Số serial bảo hành dịch vụ.']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Số serial không bảo hành dịch vụ.']);
                    }
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Không tìm thấy thông tin bảo hành.']);
            }
        } else {
        }
        return false;
    }

    public function checkAllWarranty(Request $request)
    {
        $formType = $request->input('form_type');
        $serialData = $request->input('serials'); // Nhận mảng serials
        $warrantyId = $request->input('warranty');

        if (!is_array($serialData) || empty($serialData)) {
            return response()->json(['status' => 'error', 'message' => 'Danh sách serials không hợp lệ.']);
        }

        $results = [];
        foreach ($serialData as $serial) {
            $serialCheck = SerialNumber::where('serial', $serial)->first(); // Giả sử có cột serial

            if ($serialCheck) {
                $warranty = DB::table('warranty_lookup')
                    ->where('sn_id', $serialCheck->id)
                    ->where('id', $warrantyId)
                    ->first();

                if ($warranty) {
                    if ($formType == 1 && $warranty->status == 0) {
                        $results[] = ['serial' => $serial, 'status' => 'success', 'message' => 'Còn bảo hành'];
                    } elseif ($formType == 2 && $warranty->status == 1) {
                        $results[] = ['serial' => $serial, 'status' => 'success', 'message' => 'Hết bảo hành'];
                    } elseif ($formType == 3 && $warranty->status == 2) {
                        $results[] = ['serial' => $serial, 'status' => 'success', 'message' => 'Bảo hành dịch vụ'];
                    } else {
                        $results[] = ['serial' => $serial, 'status' => 'error', 'message' => 'Trạng thái không phù hợp'];
                    }
                } else {
                    $results[] = ['serial' => $serial, 'status' => 'error', 'message' => 'Không tìm thấy thông tin bảo hành'];
                }
            } else {
                $results[] = ['serial' => $serial, 'status' => 'error', 'message' => 'Serial không tồn tại'];
            }
        }

        return response()->json($results);
    }

    public function checkbrands(Request $request)
    {
        $serialData = $request->input('serials');
        $product_id = $request->input('product_id');

        if (is_array($serialData)) {
            $result = [];
            foreach ($serialData as $serial) {
                $sericheck = SerialNumber::where('serial_code', $serial)->first();

                if ($sericheck) {
                    if ($sericheck->status == 1) {
                        // Kiểm tra xem serial có trong product_import không
                        $existsInImport = ProductImport::where('sn_id', $sericheck->id)->exists();
                        
                        if (!$existsInImport) {
                            $result[$serial] = ['status' => 'external', 'message' => 'Số serial bên ngoài và hợp lệ.'];
                        } else {
                            $result[$serial] = ['status' => 'success', 'message' => 'Số serial nội bộ.'];
                        }
                    } else {
                        $result[$serial] = ['status' => 'success', 'message' => 'Số serial nội bộ.'];
                    }
                } else {
                    $result[$serial] = ['status' => 'external', 'message' => 'Số serial bên ngoài.'];
                }
            }
            return response()->json($result);
        }
        // Kiểm tra với một serial duy nhất
        $sericheck = SerialNumber::where('serial_code', $serialData)->first();
        if (!$sericheck) {
            return response()->json(['status' => 'external', 'message' => 'Số serial bên ngoài.']);
        }

        // Kiểm tra status = 1
        if ($sericheck->status == 1) {
            // Kiểm tra xem serial có trong product_import không
            $existsInImport = ProductImport::where('sn_id', $sericheck->id)->exists();
            
            if (!$existsInImport) {
                return response()->json(['status' => 'external', 'message' => 'Số serial bên ngoài và hợp lệ.']);
            }
            return response()->json(['status' => 'error', 'message' => 'Serial tồn tại trong kho.']);
        }
        
        // Kiểm tra status = 5
        if ($sericheck->status == 5) {
            return response()->json(['status' => 'error', 'message' => 'Serial tồn tại trong kho.']);
        }
        
        // Kiểm tra product_id
        if ($sericheck->product_id == $product_id) {
            return response()->json(['status' => 'success', 'message' => 'Số serial nội bộ.']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Serial không thuộc sản phẩm này.']);
    }

    /**
     * Kiểm tra batch serial numbers thay vì từng cái một
     */
    public function checkSNBatch(Request $request)
    {
        try {
            $nameModal = $request->input('nameModal');
            $warehouse_id = $request->input('warehouse_id');
            $import_id = $request->input('import_id');
            $serials = $request->input('serials', []);
            $products = $request->input('products', []);
            
            $errors = [];
            
            // Gom nhóm theo nameModal để xử lý hiệu quả
            if ($nameModal === "CXH" || $nameModal === "XH") {
                // Kiểm tra phiếu xuất hàng
                $errors = $this->checkExportBatch($serials, $products, $warehouse_id, $import_id, $nameModal);
            } elseif ($nameModal === "PCK") {
                // Kiểm tra phiếu chuyển kho
                $errors = $this->checkTransferBatch($serials, $warehouse_id);
            }
            
            return response()->json([
                'success' => empty($errors),
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi khi kiểm tra batch serial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['Có lỗi xảy ra khi kiểm tra dữ liệu']
            ], 500);
        }
    }
    
    /**
     * Kiểm tra batch cho phiếu xuất hàng (XH, CXH) giống logic checkSN
     */
    private function checkExportBatch($serials, $products, $warehouse_id, $import_id, $nameModal)
    {
        $errors = [];
        foreach ($products as $productData) {
            $productId = $productData['product_id'];
            $qty = intval($productData['qty']);
            $serial = $productData['serial'];

            if ($serial) {
                // Có serial
                $sn = SerialNumber::where('serial_code', $serial)
                    ->where('product_id', $productId)
                    ->first();
                if (!$sn) {
                    $errors[] = "Serial {$serial} không tồn tại";
                    continue;
                }
                $inventoryQty = InventoryLookup::where('product_id', $productId)
                    ->where('sn_id', $sn->id)
                    ->sum('remaining_quantity');
                if ($nameModal == "CXH") {
                    $exportedQty = ProductExport::where('product_id', $productId)
                        ->where('sn_id', $sn->id)
                        ->where('export_id', $import_id)
                        ->sum('quantity');
                    $availableQty = $inventoryQty + $exportedQty;
                    $requiredQty = max(1, $qty);
                    $enough = $availableQty >= $requiredQty;
                    if (!$enough) $errors[] = "S/N {$serial} không có sẵn để xuất (còn: {$availableQty}, cần: {$requiredQty})";
                } else { // XH
                    $availableQty = $inventoryQty;
                    $enough = ($sn->status == 1 && $availableQty >= $qty);
                    if (!$enough) $errors[] = "S/N {$serial} vượt quá số lượng tồn kho (còn: {$availableQty}, cần: {$qty})";
                }
            } else {
                // Không serial
                $inventoryQty = InventoryLookup::where('product_id', $productId)
                    ->where('sn_id', 0)
                    ->sum('remaining_quantity');
                $availableQty = $inventoryQty;
                if ($nameModal == "CXH") {
                    $exportedQty = ProductExport::where('export_id', $import_id)
                        ->where('product_id', $productId)
                        ->where('sn_id', 0)
                        ->sum('quantity');
                    $availableQty += $exportedQty;
                }
                if ($availableQty < $qty) {
                    $errors[] = "Mã hàng {$productData['product_code']} vượt quá số lượng tồn kho (hiện còn: {$availableQty}, cần: {$qty})";
                }
            }
        }
        return $errors;
    }
    
    /**
     * Kiểm tra batch cho phiếu chuyển kho
     */
    private function checkTransferBatch($serials, $warehouse_id)
    {
        $errors = [];
        
        if (empty($serials)) {
            return $errors;
        }
        
        $serialCodes = collect($serials)->pluck('serial')->filter()->map(function ($code) {
            return strtoupper(trim($code));
        })->toArray();
        // Hỗ trợ cả serial_borrow và serialBorrow từ frontend
        $serialBorrowCodes = collect($serials)->map(function ($item) {
            $code = $item['serial_borrow'] ?? ($item['serialBorrow'] ?? null);
            return $code ? strtoupper(trim($code)) : null;
        })->filter()->toArray();
        
        if (!empty($serialCodes)) {
            // Lấy thông tin product_id từ serials data
            $serialProductMap = [];
            foreach ($serials as $serialData) {
                if (isset($serialData['serial']) && isset($serialData['product_id'])) {
                    $serialProductMap[trim($serialData['serial'])] = $serialData['product_id'];
                }
            }

            if ($warehouse_id == 1) {
                // Kho hàng mới - kiểm tra serial có tồn tại, status = 1 và đúng product_id
                $existingSerials = SerialNumber::whereIn('serial_code', $serialCodes)
                    ->where('status', 1)
                    ->select('serial_code', 'product_id')
                    ->get()
                    ->keyBy('serial_code');
                
                foreach ($serialCodes as $serialCode) {
                    $record = $existingSerials->get($serialCode);
                    $expectedProductId = $serialProductMap[$serialCode] ?? null;
                    
                    if (!$record) {
                        $errors[] = "S/N {$serialCode} không tồn tại trong kho hàng mới hoặc không có sẵn";
                    } elseif ($expectedProductId && (int)$record->product_id !== (int)$expectedProductId) {
                        $errors[] = "S/N {$serialCode} không thuộc sản phẩm này";
                    }
                }
            } else {
                // Kho bảo hành: Serial hợp lệ nếu không tồn tại hoặc tồn tại ở kho bảo hành (2) và đúng product_id
                $serialRecords = SerialNumber::whereIn('serial_code', $serialCodes)
                    ->select('serial_code', 'warehouse_id', 'product_id')
                    ->get()
                    ->keyBy('serial_code');

                // Tập serial mượn hợp lệ: status = 5
                // hoặc status = 2 nếu đã dùng làm serial thay thế trong product_returns
                $borrowSerials = SerialNumber::whereIn('serial_code', $serialBorrowCodes)
                    ->select('id', 'serial_code', 'status', 'warehouse_id')
                    ->get();
                $borrowSerialRecords = [];
                foreach ($borrowSerials as $sn) {
                    if ((int)$sn->status === 5) {
                        $borrowSerialRecords[] = $sn->serial_code;
                        continue;
                    }
                    if ((int)$sn->status === 2) {
                        $usedAsReplacement = ProductReturn::where('replacement_serial_number_id', $sn->id)->exists();
                        if ($usedAsReplacement || (int)$sn->warehouse_id === 2) {
                            $borrowSerialRecords[] = $sn->serial_code;
                        }
                    }
                }

                foreach ($serialCodes as $serialCode) {
                    $record = $serialRecords->get($serialCode);
                    $expectedProductId = $serialProductMap[$serialCode] ?? null;
                    
                    if ($record) {
                        if ((int)$record->warehouse_id !== 2) {
                            $errors[] = "S/N {$serialCode} tồn tại nhưng không nằm trong kho Bảo hành";
                        } elseif ($expectedProductId && (int)$record->product_id !== (int)$expectedProductId) {
                            $errors[] = "S/N {$serialCode} không thuộc sản phẩm này";
                        }
                    }
                }

                foreach ($serialBorrowCodes as $borrowCode) {
                    if (!in_array($borrowCode, $borrowSerialRecords)) {
                        $errors[] = "S/N {$borrowCode} mượn không hợp lệ hoặc không có sẵn";
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     * Test method để kiểm tra logic tính toán tồn kho
     */
    public function testInventoryCalculation(Request $request)
    {
        $productId = $request->input('product_id');
        $serial = $request->input('serial');
        $warehouseId = $request->input('warehouse_id', 1);
        
        // Lấy dữ liệu tồn kho
        $inventoryData = InventoryLookup::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->select('product_id', 'sn_id', 'remaining_quantity')
            ->get();
        
        $result = [
            'product_id' => $productId,
            'serial' => $serial,
            'warehouse_id' => $warehouseId,
            'total_inventory' => $inventoryData->sum('remaining_quantity'),
            'inventory_details' => $inventoryData->toArray()
        ];
        
        if ($serial) {
            $sn = SerialNumber::where('serial_code', $serial)->first();
            if ($sn) {
                $serialInventory = $inventoryData->where('sn_id', $sn->id)->sum('remaining_quantity');
                $result['serial_inventory'] = $serialInventory;
                $result['sn_id'] = $sn->id;
            }
        } else {
            $nonSerialInventory = $inventoryData->where('sn_id', 0)->sum('remaining_quantity');
            $result['non_serial_inventory'] = $nonSerialInventory;
        }
        
        return response()->json($result);
    }
}
