<div class="block-options border" id="{{ $name }}-options" style="display: none">
    <div class="wrap w-100">
        <div class="heading-title title-wrap">
            <h5>{{ $title }}</h5>
        </div>
        <div class="input-groups p-2">
            <div class="start w-100 d-flex justify-content-between">
                <label for="start">Từ ngày</label>
                <input type="date" name="date_start" id="date_start_{{ $name }}" class="date_start rounded">
            </div>
            <div class="end w-100 d-flex justify-content-between pt-2">
                <label for="start">Đến ngày</label>
                <input type="date" name="date_end" id="date_end_{{ $name }}" class="date_end rounded">
            </div>
        </div>
    </div>
    <div></div>
    <div class="d-flex align-items-baseline p-2">
        <button type="button" id="cancel-{{ $name }}"
            class="btn mx-1 btn-block text-13-black btn-cancel-filter" data-button="{{ $name }}">
            Hủy
        </button>
        <button type="submit" class="btn mx-1 btn-block btn-submit text-btnIner-filter"
            data-title="{{ $title }}" data-button-name="{{ $name }}"
            data-button="{{ $button }}">Xác Nhận</button>
    </div>
</div>
