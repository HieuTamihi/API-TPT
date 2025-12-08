@foreach ($inventory as $item)
    <tr class="position-relative inven-lookup-info height-40">
        <input type="hidden" name="id-inven-lookup" class="id-inven-lookup" id="id-inven-lookup"
            value="{{ $item->id }}">
        <td class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
            {{ $item->product->product_code }}
        </td>
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
            {{ $item->product->product_name }}
        </td>
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
            {{ $item->product->brand }}
        </td>
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
            @if ($item->serialNumber)
                <a href="{{ route('inventoryLookup.edit', $item->id) }}">
                    {{ $item->serialNumber->serial_code ?? '' }}
                    @if ($item->serialNumber->status == 5)
                        <span class="text-13-black">(Hàng mượn)</span>
                    @endif
                </a>
            @endif
        </td>
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
            {{ $item->provider->provider_name ?? '' }}
        </td>
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
            {{ date_format(new DateTime($item->import_date), 'd/m/Y') }}
        </td>
        @if (!auth()->user()->hasAnyRole(['Quản lý kho', 'Bảo hành']))
            <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                {{ $item->warehouse->warehouse_name ?? '' }}
            </td>
        @endif
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
            {{ $item->storage_duration }} ngày
        </td>
        <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
            @if ($item->status == '1')
                <span class="text-danger">Tới hạn bảo trì</span>
            @endif
        </td>
    </tr>
@endforeach
