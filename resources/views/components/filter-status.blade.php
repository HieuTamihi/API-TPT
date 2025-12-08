<div class="block-options checkbox-options" id="{{ $name }}-options" style="display: none">
    <div class="wrap w-100">
        <div class="heading-title text-center title-wrap">
            <h5>{{ $title }}</h5>
        </div>
        <div class="outer3-srcoll">
            <ul class="ks-cboxtags-{{ $name }} p-0 mb-1 px-2">
                @foreach ($filters as $filter)
                    <li class="btn-submit" data-button-name="{{ $name }}" data-button="{{ $button ?? '' }}">
                        <input type="checkbox" id="{{ $name }}_{{ $filter['key'] }}"
                            name="{{ $name }}[]" value="{{ $filter['key'] }}">
                        <label style="color: {{ $filter['color'] }}"
                            for="{{ $name }}_{{ $filter['key'] }}">{{ $filter['value'] }}</label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Chọn tất cả
        $('.select-all-{{ $name }}').click(function() {
            $('.ks-cboxtags-{{ $name }} input[type="checkbox"]:visible').prop('checked', true);
        });

        // Hủy chọn tất cả
        $('.deselect-all-{{ $name }}').click(function() {
            $('.ks-cboxtags-{{ $name }} input[type="checkbox"]').prop('checked', false);
        });


        $('.ks-cboxtags-{{ $name }} li, .ks-cboxtags-{{ $name }} label').on('click', function(
            event) {
            if (event.target.tagName !== 'INPUT') {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked')); // Đảo ngược trạng thái checked
            }
        });
    });
</script>
