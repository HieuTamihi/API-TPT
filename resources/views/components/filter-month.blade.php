<div class="block-options border" id="{{ $name }}-options" style="display: none">
    <div class="wrap w-100">
        <div class="heading-title title-wrap">
            <h5>{{ $title }}</h5>
        </div>
        <div class="input-groups p-2">
            <div class="filter-container">

                <!-- Bộ lọc Tháng, chỉ hiển thị nếu name là 'date' -->
                @if ($name == 'thang')
                    <label for="month-filter">Chọn Tháng:</label>
                    <select id="month-filter" class="month-filter form-control">
                        <option value="">Chọn tháng</option>
                        <option value="1">Tháng 01</option>
                        <option value="2">Tháng 02</option>
                        <option value="3">Tháng 03</option>
                        <option value="4">Tháng 04</option>
                        <option value="5">Tháng 05</option>
                        <option value="6">Tháng 06</option>
                        <option value="7">Tháng 07</option>
                        <option value="8">Tháng 08</option>
                        <option value="9">Tháng 09</option>
                        <option value="10">Tháng 10</option>
                        <option value="11">Tháng 11</option>
                        <option value="12">Tháng 12</option>
                    </select>
                @endif
                @if ($name == 'quy')
                    <!-- Bộ lọc Quý, chỉ hiển thị nếu name là 'quarter' -->
                    <!-- Bộ lọc Quý -->
                    <label for="quarter-filter">Chọn Quý:</label>
                    <select id="quarter-filter" class="quarter-filter form-control">
                        <option value="">Chọn quý</option>
                        <option value="1">Quý 1</option>
                        <option value="2">Quý 2</option>
                        <option value="3">Quý 3</option>
                        <option value="4">Quý 4</option>
                    </select>
                @endif
                <!-- Bộ lọc Năm, luôn hiển thị -->
                <label for="year-filter">Chọn Năm:</label>
                <select id="year-filter" class="year-filter-{{ $name }} year-filter form-control">
                    <option value="">Chọn năm</option>
                </select>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-baseline p-2">
        <button type="button" id="cancel-{{ $name }}"
            class="btn mx-1 btn-block text-13-black btn-cancel-filter" data-button="{{ $name }}">Hủy</button>
        <button type="submit" class="btn mx-1 btn-block btn-submit text-btnIner-filter"
            data-title="{{ $title }}" data-button-name="{{ $name }}">Xác Nhận</button>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Populate year dropdown with the last 10 years
        var currentYear = new Date().getFullYear();
        var yearSelect = $('.year-filter');
        for (var i = currentYear; i >= currentYear - 10; i--) {
            var option = $('<option></option>').val(i).text(i);
            yearSelect.append(option);
        }
        // Set current month as selected
        $('#month-filter').val(new Date().getMonth() + 1);
    });
</script>
