<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Quotation;
use App\Models\Receiving;
use App\Models\ReturnForm;
use App\Models\SerialNumber;
use App\Models\User;
use App\Models\warrantyHistory;
use App\Models\warrantyLookup;
use App\Models\WarrantyReceived;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Serializer;

class ReturnFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $returnforms;

    public function __construct(ReturnForm $returnforms)
    {
        $this->returnforms = $returnforms;
    }
    public function index()
    {
        $returnforms = ReturnForm::with(['reception', 'customer', 'productReturns'])->orderBy('id', 'desc')->get();
        $title = 'Phiếu trả hàng';
        $customers = Customers::all();
        return view('expertise.returnforms.index', compact('returnforms', 'title', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $quoteNumber = (new Receiving)->getQuoteCount('PTH', ReturnForm::class, 'return_code');
        $title = 'Tạo phiếu trả hàng';
        $existingReceptionIds = ReturnForm::pluck('reception_id')->toArray();
        $receivings = Receiving::whereNotIn('id', $existingReceptionIds)->where('status', 2)->get();
        return view('expertise.returnforms.create', compact('quoteNumber', 'title', 'receivings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (ReturnForm::where('return_code', $request->form_code_receiving)->exists()) {
            return back()->with('warning', 'Mã phiếu trả hàng đã tồn tại.');
        }
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'reception_id' => 'required|unique:return_form,reception_id',
            'return_code' => 'required|unique:return_form,return_code',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string',
            'date_created' => 'required|date',
            'contact_person' => 'nullable|string',
            'return_method' => 'required|in:1,2,3',
            'user_id' => 'required|string',
            'phone_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:1,2',
            'return' => 'required|array',
            'return.*.product_id' => 'required|exists:products,id',
            'return.*.quantity' => 'required|integer|min:1',
            'return.*.serial_code' => 'nullable|string',
            'return.*.serial_id' => 'required',
            'return.*.replacement_code' => 'nullable|integer',
            'return.*.replacement_serial_number_id' => 'array',
            'return.*.warranty' => ['nullable', 'array'],
            'return.*.warranty.*.name_warranty' => ['required', 'string'],
            'return.*.warranty.*.extra_warranty' => ['nullable', 'integer'],
            'return.*.warranty.*.note' => ['nullable', 'string'],
            'return.*.note' => 'nullable|string',
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $returnForm = ReturnForm::create($validated);

            foreach ($validated['return'] as $returnItem) {
                $return_form_id = $returnForm->id;
                $product_id = $returnItem['product_id'];
                $serial_number_id = $returnItem['serial_id'];
                $note = $returnItem['note'] ?? null;

                $replacement_codes = $returnItem['replacement_code'] ?? null;
                $replacement_serial_codes = $returnItem['replacement_serial_number_id'] ?? [];

                // Cập nhật trạng thái phiếu tiếp nhận
                $stateRecei = $validated['status'] == 1 ? 3 : 4;
                Receiving::find($validated['reception_id'])->update([
                    'status' => $stateRecei,
                    'state' => 0,
                    'closed_at' => $validated['status'] == 1 ? $validated['date_created'] : null,
                ]);

                if ($returnForm->reception->form_type != 2) {
                    // Trường hợp không có serial thay thế (ví dụ chỉ sửa chữa)
                    if (empty(array_filter($replacement_serial_codes))) {
                        $productReturn = ProductReturn::createProductReturn([
                            'return_form_id' => $return_form_id,
                            'product_id' => $product_id,
                            'quantity' => 1,
                            'serial_number_id' => $serial_number_id,
                            'replacement_code' => null,
                            'replacement_serial_number_id' => null,
                            'extra_warranty' => $returnItem['extra_warranty'] ?? null,
                            'notes' => $note,
                        ]);

                        // Clone hoặc tạo mới warrantyLookup cũ
                        $oldWarrantyLookup = warrantyLookup::firstOrCreate(
                            ['sn_id' => $serial_number_id],
                            [
                                'product_id' => $product_id,
                                'customer_id' => $validated['customer_id'],
                                'name_warranty' => 'Hàng bên ngoài',
                                'export_return_date' => $validated['date_created'],
                                'warranty' => 0,
                                'warranty_expire_date' => $validated['date_created'],
                                'status' => 0,
                            ]
                        );

                        // Ghi lịch sử bảo hành
                        warrantyHistory::create([
                            'warranty_lookup_id' => $oldWarrantyLookup->id,
                            'receiving_id' => $validated['reception_id'],
                            'return_id' => $return_form_id,
                            'product_return_id' => $productReturn->id,
                            'note' => $note,
                        ]);

                        // Cập nhật trạng thái serial gốc nếu cần
                        SerialNumber::find($serial_number_id)
                            ->update(['status' => 1, 'warehouse_id' => 2]);
                    } else {
                        foreach ($replacement_serial_codes as $replacement_serial_code) {
                            $replacementSerial = SerialNumber::where('serial_code', $replacement_serial_code)
                                ->where('product_id', $replacement_codes)
                                ->whereIn('status', [1, 5])
                                ->first();

                            if (!$replacementSerial) continue;

                            // Tạo product_return
                            $productReturn = ProductReturn::createProductReturn([
                                'return_form_id' => $return_form_id,
                                'product_id' => $product_id,
                                'quantity' => 1,
                                'serial_number_id' => $serial_number_id,
                                'replacement_code' => $replacement_codes,
                                'replacement_serial_number_id' => $replacementSerial->id,
                                'extra_warranty' => $returnItem['extra_warranty'] ?? null,
                                'notes' => $note,
                            ]);

                            // Sao chép bảo hành
                            $warranty_lookups = warrantyLookup::where('sn_id', $serial_number_id)->get();
                            foreach ($warranty_lookups as $warranty) {
                                warrantyLookup::create([
                                    'product_id' => $product_id,
                                    'sn_id' => $replacementSerial->id,
                                    'customer_id' => $validated['customer_id'],
                                    'name_warranty' => $warranty->name_warranty,
                                    'name_status' => $warranty->name_status,
                                    'export_return_date' => $warranty->export_return_date,
                                    'warranty' => $warranty->warranty,
                                    'warranty_expire_date' => $warranty->warranty_expire_date,
                                    'status' => 0,
                                ]);
                            }

                            // Cập nhật trạng thái Serial
                            SerialNumber::find($serial_number_id)->update(['status' => 1, 'warehouse_id' => 2]);
                            $replacementSerial->update(['status' => 2]);

                            // Ghi lịch sử bảo hành
                            $oldWarrantyLookup = warrantyLookup::where('sn_id', $serial_number_id)->first();
                            if (!$oldWarrantyLookup) {
                                $oldWarrantyLookup = warrantyLookup::create([
                                    'product_id' => $product_id,
                                    'sn_id' => $serial_number_id,
                                    'customer_id' => $validated['customer_id'],
                                    'name_warranty' => 'Hàng bên ngoài',
                                    'name_status' => null,
                                    'export_return_date' => $validated['date_created'],
                                    'warranty' => 0,
                                    'warranty_expire_date' => $validated['date_created'],
                                    'status' => 0,
                                ]);
                            }

                            warrantyHistory::create([
                                'warranty_lookup_id' => $oldWarrantyLookup->id,
                                'receiving_id' => $validated['reception_id'],
                                'return_id' => $return_form_id,
                                'product_return_id' => $productReturn->id,
                                'note' => $note,
                            ]);
                        }
                    }
                }

                // Xử lý nếu form_type == 2 (bảo hành có thêm thời gian)
                if ($returnForm->reception->form_type == 2 && !empty($returnItem['warranty'])) {
                    foreach ($returnItem['warranty'] as $warrantyItem) {
                        $extra_warranty = (int) ($warrantyItem['extra_warranty'] ?? 0);

                        $productReturn = ProductReturn::createProductReturn([
                            'return_form_id' => $return_form_id,
                            'product_id' => $product_id,
                            'quantity' => 1,
                            'serial_number_id' => $serial_number_id,
                            'replacement_code' => $replacement_codes,
                            'replacement_serial_number_id' => null,
                            'extra_warranty' => $extra_warranty,
                            'notes' => $note,
                        ]);

                        $wa = WarrantyReceived::with('productReceived')
                            ->whereHas('productReceived', function ($q) use ($validated) {
                                $q->where('reception_id', $validated['reception_id']);
                            })
                            ->where('name_warranty', $warrantyItem['name_warranty'])
                            ->first();

                        if ($wa) {
                            $wa->update(['product_return_id' => $productReturn->id]);
                        }

                        $warrantyRecord = warrantyLookup::where('sn_id', $serial_number_id)
                            ->where('name_warranty', $warrantyItem['name_warranty'])
                            ->first();

                        if ($warrantyRecord) {
                            $updateData = [
                                'return_date' => $validated['date_created'],
                                'status' => 0,
                            ];
                            if ($extra_warranty > 0) {
                                $updateData['name_expire_date'] = $warrantyItem['name_warranty'];
                                $updateData['warranty_extra'] = $extra_warranty;
                                $updateData['service_warranty_expired'] = Carbon::parse($validated['date_created'])->addMonths($extra_warranty);
                            }
                            $warrantyRecord->update($updateData);
                        } else {
                            $createData = [
                                'sn_id' => $serial_number_id,
                                'product_id' => $product_id,
                                'customer_id' => $validated['customer_id'],
                                'return_date' => $validated['date_created'],
                                'name_status' => 'Còn bảo hành',
                                'status' => 0,
                            ];
                            if ($extra_warranty > 0) {
                                $createData['name_expire_date'] = $warrantyItem['name_warranty'];
                                $createData['warranty_extra'] = $extra_warranty;
                                $createData['service_warranty_expired'] = Carbon::parse($validated['date_created'])->addMonths($extra_warranty);
                            }
                            $warrantyRecord = warrantyLookup::create($createData);
                        }

                        // Cập nhật trạng thái theo ngày hết bảo hành
                        $today = Carbon::now();
                        $expired = $warrantyRecord->service_warranty_expired ? Carbon::parse($warrantyRecord->service_warranty_expired) : null;

                        $warrantyRecord->update([
                            'status' => $expired === null || $today->gte($expired) || $warrantyRecord->warranty_extra == 0 ? 1 : 2
                        ]);

                        warrantyHistory::create([
                            'warranty_lookup_id' => $warrantyRecord->id,
                            'receiving_id' => $validated['reception_id'],
                            'return_id' => $return_form_id,
                            'product_return_id' => $productReturn->id,
                            'note' => $note,
                        ]);

                        SerialNumber::find($serial_number_id)->update(['status' => 6]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('returnforms.index')->with('msg', 'Tạo phiếu trả hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating return form: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReturnForm $returnForm)
    {
        return view('expertise.returnforms.show', compact('returnForm'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Lấy thông tin phiếu trả hàng và quan hệ với productReturns và reception
        $returnForm = ReturnForm::with('productReturns', 'reception')->findOrFail($id);
        $title = 'Chi tiết phiếu trả hàng';
        // Lấy tất cả các reception_id đã có trong bảng return_form
        $existingReceptionIds = ReturnForm::pluck('reception_id')->toArray();
        // Lọc ra tất cả các receiving, nhưng giữ lại reception_id của returnForm hiện tại
        $receivings = Receiving::whereNotIn('id', $existingReceptionIds)
            ->orWhere('id', $returnForm->reception_id)
            ->get();
        // Các thông tin khác liên quan đến phiếu trả hàng
        $returnProducts = $returnForm->productReturns->keyBy('id');
        $dataProduct = Product::all();
        $data = ReturnForm::with('productReturns', 'reception')->get();
        $customers = Customers::all();
        $users = User::all();
        // Trả về view với các dữ liệu đã chuẩn bị
        return view('expertise.returnforms.edit', compact('returnForm', 'title', 'receivings', 'returnProducts', 'dataProduct', 'data', 'customers', 'users'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate input data with unique rule exceptions for the current record
        $validated = $request->validate([
            'reception_id' => 'required|unique:return_form,reception_id,' . $id,
            'return_code' => 'required|unique:return_form,return_code,' . $id,
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string',
            'date_created' => 'required|date',
            'contact_person' => 'nullable|string',
            'return_method' => 'required|in:1,2,3',
            'user_id' => 'required|string',
            'phone_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:1,2',
            'return' => 'required|array',
            'return.*.product_id' => 'required|exists:products,id',
            'return.*.quantity' => 'required|integer|min:1',
            'return.*.serial_code' => 'nullable|string',
            'return.*.serial_id' => 'required',
            'return.*.replacement_code' => 'nullable|integer',
            'return.*.replacement_serial_number_id' => 'nullable|string',
            'return.*.name_warranty' => 'nullable|string',
            'return.*.extra_warranty' => 'nullable|integer',
            'return.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Find the return form
            $returnForm = ReturnForm::findOrFail($id);

            // Update the return form
            $returnForm->update($validated);
            // dd($request->all());
            // Delete existing product returns to avoid duplicates
            ProductReturn::where('return_form_id', $id)->delete();
            foreach ($validated['return'] as $returnItem) {
                $product_id = $returnItem['product_id'];
                $quantity = $returnItem['quantity'];
                $serial_number_id = $returnItem['serial_id'];
                $name_warranty = $returnItem['name_warranty'];
                $extra_warranty = (int) $returnItem['extra_warranty'] ?? null;
                $note = $returnItem['note'] ?? null;
                $replacement_serial_number_id = $returnItem['replacement_serial_number_id'] ?? null;
                $replacement_code = $replacement_serial_number_id ? ($returnItem['replacement_code'] ?? null) : null;

                $replacementSerialId = null;
                $oldReplacementSerialId = ProductReturn::where('return_form_id', $id)->value('replacement_serial_number_id') ?? 0;
                if ($replacement_serial_number_id) {
                    $replacementSerial = SerialNumber::where('serial_code', $replacement_serial_number_id)
                        ->where('product_id', $replacement_code)
                        ->where('warehouse_id', 2)
                        ->first();
                    if ($replacementSerial && $replacementSerial->id !== $oldReplacementSerialId) {
                        $replacementSerialId = $replacementSerial;
                    }
                }

                // Update receiving status
                $stateRecei = $validated['status'] == 1 ? 3 : 4;
                Receiving::find($validated['reception_id'])->update([
                    'status' => $stateRecei,
                    'state' => 0,
                    'closed_at' => $validated['status'] == 1 ? $validated['date_created'] : null,
                ]);

                $data = [
                    'return_form_id' => $id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'serial_number_id' => (int)$serial_number_id,
                    'replacement_code' => $replacement_code,
                    'replacement_serial_number_id' => $replacementSerialId->id ?? 0,
                    'extra_warranty' => $extra_warranty,
                    'notes' => $note,
                ];



                // Handle replacement serial number updates
                if ($replacementSerialId) {
                    // Delete existing warranty lookups for the replacement
                    warrantyLookup::where('sn_id', $replacementSerialId->id)->delete();
                    $warranty_lookup = warrantyLookup::where('sn_id', $serial_number_id)->get();
                    foreach ($warranty_lookup as $warranty) {
                        warrantyLookup::create([
                            'product_id' => $product_id,
                            'sn_id' => $replacementSerialId->id,
                            'customer_id' => $validated['customer_id'],
                            'name_warranty' => $warranty->name_warranty,
                            'name_status' => $warranty->name_status,
                            'return_date' => $warranty->return_date,
                            'warranty' => $warranty->warranty,
                            'warranty_expire_date' => $warranty->warranty_expire_date,
                            'status' => 0,
                        ]);
                    }

                    if ($oldReplacementSerialId) {
                        SerialNumber::find($oldReplacementSerialId)->update(['status' => 1, 'warehouse_id' => 2]);
                    }
                    SerialNumber::find($serial_number_id)->update(['status' => 1, 'warehouse_id' => 2]);
                    SerialNumber::find($replacementSerialId->id)->update(['status' => 2]);
                }

                if ($returnForm->reception->form_type != 2) {
                    $productReturn = ProductReturn::createProductReturn($data);
                    $oldWarrantyLookup = warrantyLookup::where('sn_id', $serial_number_id)->first();

                    // Update or create warranty history
                    warrantyHistory::updateOrCreate(
                        [
                            'warranty_lookup_id' => $oldWarrantyLookup->id,
                            'return_id' => $id,
                        ],
                        [
                            'receiving_id' => $validated['reception_id'],
                            'product_return_id' => $productReturn->id,
                            'note' => $note,
                        ]
                    );
                }

                if ($returnForm->reception->form_type == 2) {
                    $productReturn = ProductReturn::createProductReturn($data);

                    // Update warranty lookup using name_warranty from input
                    $warrantyRecordOld = warrantyLookup::updateOrCreate(
                        [
                            'sn_id' => $serial_number_id
                        ],
                        [
                            'product_id' => $product_id,
                            'customer_id' => $validated['customer_id'],
                            'warranty' => $extra_warranty,
                            'return_date' => $validated['date_created'],
                            'name_status' => 'Còn bảo hành',
                            'service_warranty_expired' => Carbon::parse($validated['date_created'])->addMonths($extra_warranty),
                            'status' => 0,
                        ]
                    );

                    // Update warranty status
                    $today = Carbon::now();
                    if ($warrantyRecordOld->warranty == 0 || ($warrantyRecordOld->service_warranty_expired && $today->greaterThanOrEqualTo($warrantyRecordOld->service_warranty_expired))) {
                        $warrantyRecordOld->update(['status' => 1]);
                    } else {
                        $warrantyRecordOld->update(['status' => 2]);
                    }

                    // Update warranty received
                    $wa = WarrantyReceived::with('productReceived')
                        ->whereHas('productReceived', function ($query) use ($validated) {
                            $query->where('reception_id', $validated['reception_id']);
                        })
                        ->where('name_warranty', $name_warranty) // Added name_warranty condition
                        ->first();

                    if ($wa) {
                        $wa->update(['product_return_id' => $productReturn->id]);
                    }

                    // Update or create warranty history
                    warrantyHistory::updateOrCreate(
                        [
                            'warranty_lookup_id' => $warrantyRecordOld->id,
                            'return_id' => $id,
                        ],
                        [
                            'receiving_id' => $validated['reception_id'],
                            'product_return_id' => $productReturn->id,
                            'note' => $note,
                        ]
                    );

                    // Update serial number status
                    SerialNumber::find($serial_number_id)->update(['status' => 5]);
                }
            }

            DB::commit();
            return redirect()->route('returnforms.index')->with('msg', 'Cập nhật phiếu trả hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating return form: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Load return form with correct relations
            $returnForm = ReturnForm::with([
                'reception',
                'productReturns',
                'productReturns.serialNumber',
                'productReturns.replacementSerialNumber'
            ])->findOrFail($id);

            // Log data for debugging
            Log::info('Deleting return form with ID: ' . $id, [
                'return_form' => $returnForm->toArray()
            ]);

            foreach ($returnForm->productReturns as $productReturn) {
                // Log each product return being processed
                Log::info('Processing product return', [
                    'product_return_id' => $productReturn->id,
                    'serial_number_id' => $productReturn->serial_number_id,
                    'replacement_serial_number_id' => $productReturn->replacement_serial_number_id
                ]);

                // Handle warranty histories separately since it's not a direct relation
                $warrantyHistories = warrantyHistory::where('product_return_id', $productReturn->id)->get();
                foreach ($warrantyHistories as $warrantyHistory) {
                    $warrantyLookup = warrantyLookup::find($warrantyHistory->warranty_lookup_id);
                    if ($warrantyLookup) {
                        Log::info('Resetting warranty lookup', [
                            'warranty_lookup_id' => $warrantyLookup->id
                        ]);

                        // Nếu warranty_extra > 0, đặt các trường về null
                        $updateData = ['status' => 0];
                        if ($warrantyLookup->warranty_extra > 0) {
                            $updateData['name_expire_date'] = null;
                            $updateData['warranty_extra'] = null;
                            $updateData['service_warranty_expired'] = null;
                        }

                        // Cập nhật warrantyLookup
                        $warrantyLookup->update($updateData);
                        // Nếu name_warranty là null sau khi cập nhật, thì xóa luôn warranty_lookup
                        if (is_null($warrantyLookup->name_warranty)) {
                            Log::info('Deleting warranty lookup', [
                                'warranty_lookup_id' => $warrantyLookup->id
                            ]);
                            $warrantyLookup->delete();
                        }
                    }
                    $warrantyHistory->delete();
                }


                // Handle replacement serial number
                if ($productReturn->replacementSerialNumber) {
                    Log::info('Handling replacement serial', [
                        'replacement_serial_id' => $productReturn->replacement_serial_number_id
                    ]);

                    // Delete warranty lookups for replacement serial
                    warrantyLookup::where('sn_id', $productReturn->replacement_serial_number_id)
                        ->delete();

                    // Reset replacement serial status
                    $productReturn->replacementSerialNumber->update([
                        'status' => 1
                    ]);
                }

                // Reset original serial number
                if ($productReturn->serialNumber) {
                    Log::info('Resetting original serial', [
                        'serial_id' => $productReturn->serial_number_id
                    ]);

                    $productReturn->serialNumber->update([
                        'status' => 2,
                    ]);
                }

                // Handle warranty received
                WarrantyReceived::where('product_return_id', $productReturn->id)
                    ->update(['product_return_id' => null]);

                // Delete the product return
                $productReturn->delete();
            }

            // Reset receiving status
            if ($returnForm->reception) {
                Log::info('Resetting reception', [
                    'reception_id' => $returnForm->reception_id
                ]);

                $returnForm->reception->update([
                    'status' => 2,
                    'state' => 0,
                    'closed_at' => null
                ]);
            }

            // Delete return form
            $returnForm->delete();

            DB::commit();
            return redirect()->route('returnforms.index')
                ->with('msg', 'Xóa phiếu trả hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log detailed error information
            Log::error('Error deleting return form: ' . $e->getMessage(), [
                'return_form_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('msg', 'Xóa phiếu trả hàng thành công' . $e->getMessage());
        }
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
        if (isset($data['form_type']) && $data['form_type'] !== null) {
            $statusValues = [];
            if (in_array(1, $data['form_type'])) {
                $statusValues[] = '<span style="color: #858585;">Bảo hành</span>';
            }
            if (in_array(2, $data['form_type'])) {
                $statusValues[] = '<span style="color: #08AA36BF;">Dịch vụ</span>';
            }
            if (in_array(3, $data['form_type'])) {
                $statusValues[] = '<span style="color:rgba(67, 54, 154, 0.75);">Bảo hành dịch vụ</span>';
            }
            $filters[] = ['value' => 'Loại phiếu: ' . implode(', ', $statusValues), 'name' => 'loai-phieu', 'icon' => 'status'];
        }
        if (isset($data['status']) && $data['status'] !== null) {
            $statusValues = [];
            if (in_array(1, $data['status'])) {
                $statusValues[] = '<span style="color: #858585;">Hoàn thành</span>';
            }
            if (in_array(2, $data['status'])) {
                $statusValues[] = '<span style="color: #08AA36BF;">Khách không đồng ý</span>';
            }
            $filters[] = ['value' => 'Tình-trạng: ' . implode(', ', $statusValues), 'name' => 'tinh-trang', 'icon' => 'status'];
        }

        if ($request->ajax()) {
            $returnforms = $this->returnforms->getReturnFormAjax($data);
            return response()->json([
                'data' => $returnforms,
                'filters' => $filters,
            ]);
        }
        return false;
    }
}
