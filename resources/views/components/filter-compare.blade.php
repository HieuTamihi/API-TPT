    <div class="block-options" id="{{ $name }}-options" style="display:none">
        <div class="wrap w-100">
            <div class="heading-title title-wrap">
                <h5>{{ $title }}</h5>
            </div>
            <div class="input-group p-2 justify-content-around border-filter">
                <select class="{{ $name }}-operator operator" name="{{ $name }}operator"
                    style="width: 30%">
                    <option value=">=">>=</option>
                    <option value="<=">
                        <= </option>
                </select>
                <input class="w-50 {{ $name }}-quantity quantity {{ $name }}-input" type="text"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="{{ $name }}" value=""
                    placeholder="Nhập thông tin" autocomplete="off">
            </div>
        </div>
        <div class="d-flex justify-contents-center align-items-baseline p-2">
            <button type="button" id="cancel-{{ $name }}"
                class="btn mx-1 btn-block text-13-black btn-cancel-filter"
                data-button="{{ $name }}">Hủy</button>
            <button type="submit" class="btn mx-1 btn-block btn-submit text-btnIner-filter"
                data-title="{{ $title }}" data-button-name="{{ $name }}"
                data-button="{{ isset($button) ? $button : '' }}">Xác
                Nhận</button>
        </div>
    </div>
