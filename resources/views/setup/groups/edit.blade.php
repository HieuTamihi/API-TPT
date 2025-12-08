@include('partials.header', ['activeGroup' => 'systemFirst', 'activeName' => 'groups'])
@section('title', $title)
<form action="{{ route('groups.update', ['group' => $group->id]) }}" method="POST"
    onsubmit="return checkDuplicateRepresentatives()">
    @csrf
    @method('PUT')
    <input type="hidden" id="idGroup" value="{{ $group->id }}">
    <div class="content-wrapper m-0 wrapper-height">
        <div class="content-header-fixed p-0">
            <div class="content__header--inner">
                <div class="content__heading--left text-long-special opacity-0">
                    <span class="ml-4">Thiết lập ban đầu</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69269 13.9741C7.43577 13.7171 7.43577 13.3006 7.69269 13.0437L10.7363 10.0001L7.69269 6.95651C7.43577 6.69959 7.43577 6.28303 7.69269 6.02611C7.94962 5.76918 8.36617 5.76918 8.6231 6.02611L12.1319 9.53488C12.3888 9.7918 12.3888 10.2084 12.1319 10.4653L8.6231 13.9741C8.36617 14.231 7.94962 14.231 7.69269 13.9741Z"
                                fill="#26273B" fill-opacity="0.8" />
                        </svg>
                    </span>
                    <span>
                        <a class="text-dark" href="{{ route('groups.index') }}">Nhóm
                            đối
                            tượng</a>
                    </span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69269 13.9741C7.43577 13.7171 7.43577 13.3006 7.69269 13.0437L10.7363 10.0001L7.69269 6.95651C7.43577 6.69959 7.43577 6.28303 7.69269 6.02611C7.94962 5.76918 8.36617 5.76918 8.6231 6.02611L12.1319 9.53488C12.3888 9.7918 12.3888 10.2084 12.1319 10.4653L8.6231 13.9741C8.36617 14.231 7.94962 14.231 7.69269 13.9741Z"
                                fill="#26273B" fill-opacity="0.8" />
                        </svg>
                    </span>
                    <span class="font-weight-bold">Sửa nhóm đối tượng</span>
                </div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('groups.index') }}" class="activity">
                            <button class="btn-destroy btn-light mx-1 d-flex align-items-center h-100" type="button">
                                <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 14 14" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14ZM5.03033 3.96967C4.73744 3.67678 4.26256 3.67678 3.96967 3.96967C3.67678 4.26256 3.67678 4.73744 3.96967 5.03033L5.93934 7L3.96967 8.96967C3.67678 9.26256 3.67678 9.73744 3.96967 10.0303C4.26256 10.3232 4.73744 10.3232 5.03033 10.0303L7 8.06066L8.96967 10.0303C9.26256 10.3232 9.73744 10.3232 10.0303 10.0303C10.3232 9.73744 10.3232 9.26256 10.0303 8.96967L8.06066 7L10.0303 5.03033C10.3232 4.73744 10.3232 4.26256 10.0303 3.96967C9.73744 3.67678 9.26256 3.67678 8.96967 3.96967L7 5.93934L5.03033 3.96967Z"
                                        fill="black" />
                                </svg>
                                <p class="p-0 m-0 text-dark">Hủy</p>
                            </button>
                        </a>
                        <button type="submit" class="custom-btn d-flex align-items-center h-100"
                            style="margin-right:10px">
                            <svg class="mx-1" width="18" height="18" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.75 1V6.75C6.75 7.5297 7.34489 8.17045 8.10554 8.24313L8.25 8.25H14V13C14 14.1046 13.1046 15 12 15H4C2.89543 15 2 14.1046 2 13V3C2 1.89543 2.89543 1 4 1H6.75ZM8 1L14 7.03022H9C8.44772 7.03022 8 6.5825 8 6.03022V1Z"
                                    fill="white" />
                            </svg>
                            <p class="p-0 m-0">Cập nhật nhóm đối tượng</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="content editgroup" style="margin-top:8.7rem">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <section class="">
                <div id="info" class="content tab-pane in active">
                    <section class="content">
                        <div class="container-fluided">
                            <div class="info-chung">
                                <div class="content-info">
                                    <div class="d-flex align-items-center height-60-mobile">
                                        <div class="title-info py-2 border border-top-0 border-left-0 height-100">
                                            <p class="p-0 m-0 margin-left32 text-14 required-label">Loại đối
                                                tượng
                                            </p>
                                        </div>
                                        <div
                                            class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                                            <select name="group_type_id" id="grouptypeSelect" disabled required
                                                class="border-0 w-100 text-13-black height-100">
                                                <option value="">Chọn loại nhóm</option>
                                                @foreach ($grouptypes as $grouptype)
                                                    <option
                                                        {{ isset($group) && $group->group_type_id == $grouptype->id ? 'selected' : '' }}
                                                        value="{{ $grouptype->id }}">{{ $grouptype->group_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center height-60-mobile">
                                        <div class="title-info py-2 border border-left-0 height-100 border-top-0">
                                            <p class="p-0 m-0 required-label margin-left32 text-14">
                                                Mã đối tượng
                                            </p>
                                        </div>
                                        <input type="text" required placeholder="Nhập thông tin" name="group_code"
                                            value="{{ $group->group_code }}" required autocomplete="off"
                                            class="border border-white w-100 py-2 border-left-0 height-100 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                                    </div>
                                    <div class="d-flex align-items-center height-60-mobile">
                                        <div class="title-info py-2 border border-left-0 height-100 border-top-0">
                                            <p class="p-0 m-0 required-label margin-left32 text-14">
                                                Tên nhóm đối tượng
                                            </p>
                                        </div>
                                        <input type="text" required placeholder="Nhập thông tin" autocomplete="off"
                                            name="group_name_display" value="{{ $group->group_name }}" required
                                            class="border border-white w-100 py-2 border-left-0 height-100 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                                    </div>
                                    <div class="d-flex  align-items-center height-60-mobile ">
                                        <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                                            <p class="p-0 m-0 margin-left32 text-14">Mô tả</p>
                                        </div>
                                        <input type="text" placeholder="Nhập thông tin" name="group_desc"
                                            autocomplete="off"
                                            class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue"
                                            value="{{ $group->description }}">
                                    </div>
                                    <p
                                        class="font-weight-bold text-uppercase info-chung--heading bg-white text-purble">
                                        <span class="ml-4 pl-2">Danh sách trong nhóm</span>
                                    </p>
                                    <div class="">
                                        <table id="example2" class="table table-hover bg-white rounded">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="height-52">
                                                        <span class="d-flex justify-content-start ml-4">
                                                            <a href="#" class="sort-link btn-submit"
                                                                data-sort-by="quotation_number" data-sort-type="DESC">
                                                                <button class="btn-sort text-13" type="submit">
                                                                    Mã
                                                                </button>
                                                            </a>
                                                            <div class="icon" id="icon-quotation_number"></div>
                                                        </span>
                                                    </th>
                                                    <th scope="col" class="height-52">
                                                        <span class="d-flex justify-content-start">
                                                            <a href="#" class="sort-link btn-submit"
                                                                data-sort-by="quotation_number" data-sort-type="DESC">
                                                                <button class="btn-sort text-13" type="submit">
                                                                    Tên
                                                                </button>
                                                            </a>
                                                            <div class="icon" id="icon-quotation_number"></div>
                                                        </span>
                                                    </th>
                                                    <th scope="col" class="height-52"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-data-group">
                                                @foreach ($dataGroup['results'] as $item)
                                                    <tr class="position-relative height-52">
                                                        <td
                                                            class="text-13-black max-width180 text-left border-top-0 border-bottom pl-4">
                                                            {{ $item->code }}
                                                        </td>
                                                        <td
                                                            class="text-13-black max-width180 text-left border-top-0 border-bottom">
                                                            {{ $item->name }}
                                                        </td>
                                                        <td class="text-13-black text-left border-top-0 border-bottom">
                                                            <button type="button" class="btn btn-sm deleteOJ"
                                                                data-id="{{ $item->id }}"
                                                                data-id-group="{{ $group->id }}">
                                                                <svg width="16" height="16"
                                                                    viewBox="0 0 16 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.936" fill-rule="evenodd"
                                                                        clip-rule="evenodd"
                                                                        d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z"
                                                                        fill="#6C6F74" />
                                                                    <path opacity="0.891" fill-rule="evenodd"
                                                                        clip-rule="evenodd"
                                                                        d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z"
                                                                        fill="#6C6F74" />
                                                                    <path opacity="0.891" fill-rule="evenodd"
                                                                        clip-rule="evenodd"
                                                                        d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z"
                                                                        fill="#6C6F74" />
                                                                    <path opacity="0.891" fill-rule="evenodd"
                                                                        clip-rule="evenodd"
                                                                        d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z"
                                                                        fill="#6C6F74" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" data-toggle="modal" data-target="#listModal"
                                        class="btn-save-print d-flex align-items-center h-100 py-1 px-2 ml-4 rounded addGuestNew"
                                        style="margin-right:10px">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            class="mr-2" viewBox="0 0 16 16" fill="none">
                                            <path
                                                d="M8.75 3C8.75 2.58579 8.41421 2.25 8 2.25C7.58579 2.25 7.25 2.58579 7.25 3V7.25H3C2.58579 7.25 2.25 7.58579 2.25 8C2.25 8.41421 2.58579 8.75 3 8.75H7.25V13C7.25 13.4142 7.58579 13.75 8 13.75C8.41421 13.75 8.75 13.4142 8.75 13V8.75H13C13.4142 8.75 13.75 8.41421 13.75 8C13.75 7.58579 13.4142 7.25 13 7.25H8.75V3Z"
                                                fill="#282A30" />
                                        </svg>
                                        <span class="text-table">Thêm</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
            {{-- Modal add --}}
            <div class="modal fade" id="listModal" tabindex="-1" role="dialog"
                aria-labelledby="productModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="margin-top: 5%;">
                    <div class="modal-content">
                        <h5 class="modal-title p-2 border-bottom">Danh sách</h5>
                        <div class="modal-body pb-0 px-2 pt-0">
                            <div class="content-info p-2 border-bottom">
                                <div class="outer3-srcoll">
                                    <ul class="ks-cboxtags listData p-0 mb-1 px-2"
                                        data-group="{{ $group->group_type_id }}">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 py-1 px-1">
                            <button type="button" class="btn-save-print rounded h-100 text-table py-1"
                                data-dismiss="modal">Trở về</button>
                            <button type="button" class="custom-btn align-items-center h-100 py-1 px-2 text-table"
                                id="addGuest">Thêm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('dist/js/scripts.js') }}"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        var selectedValue = $('.listData').attr('data-group');
        var data_obj;
        $('#grouptypeSelect').change(function() {
            selectedValue = $(this).val();
            $('.listData').attr('data-group', selectedValue);
        });
        var dataupdate = [];
        $(document).on('click', '.addGuestNew', function(e) {
            e.preventDefault();
            if (selectedValue) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('dataObj') }}",
                    data: {
                        group_id: selectedValue
                    },
                    success: function(data) {
                        $('.listData').empty();
                        data_obj = data.obj;
                        data.results.forEach(function(item) {
                            var listItem =
                                '<li class="d-flex align-items-center border-bottom">';
                            listItem += '<input type="checkbox" id="' + data.obj +
                                '_' + item.id + '" name="' + data.obj +
                                '[]" value="' + item.id + '">';
                            listItem += '<span class="text-13 px-4 py-2" for="' +
                                item.name + '_' + item.id +
                                '">' + item.name + '</span>';
                            listItem += '</li>';
                            $('.listData').append(listItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            } else {
                console.log('Group ID is not set.');
            }
        });

        function appendRowToTable(id, name) {
            var idGroup = $('#idGroup').val();
            var newRow = `
            <tr class="position-relative height-52">
                <td class="text-13-black max-width180 text-left border-top-0 border-bottom pl-4">
                    ${id}
                </td>
                <td class="text-13-black max-width180 text-left border-top-0 border-bottom">
                    ${name}
                </td>
                <td class="text-13-black text-left border-top-0 border-bottom">
                    <button type="button" class="btn btn-sm deleteOJ" data-id="${id}" data-id-group="${idGroup}">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.936" fill-rule="evenodd" clip-rule="evenodd" d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z" fill="#6C6F74"></path>
                            <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z" fill="#6C6F74"></path>
                            <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z" fill="#6C6F74"></path>
                            <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z" fill="#6C6F74"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
            $('#tbody-data-group').append(newRow);
        }

        $(document).on('click', '#addGuest', function(e) {
            e.preventDefault();
            $('.ks-cboxtags input[type="checkbox"]').each(function() {
                const value = $(this).val();
                if ($(this).is(':checked') && dataupdate.indexOf(value) === -1) {
                    dataupdate.push(value);
                } else if (!$(this).is(':checked')) {
                    const index = dataupdate.indexOf(value);
                    if (index !== -1) {
                        dataupdate.splice(index, 1);
                    }
                }
            });
            if (dataupdate.length !== 0) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('updateDataGroup') }}",
                    data: {
                        group_type_id: selectedValue,
                        data_obj: data_obj,
                        group_id: {{ $group->id }},
                        dataupdate: dataupdate,
                    },
                    success: function(data) {
                        if (data && Array.isArray(data.results)) {
                            data.results.forEach(function(item) {
                                appendRowToTable(item.id, item.name);
                            });
                            dataupdate = [];
                        } else {
                            console.error('data.results không phải là mảng hoặc undefined');
                        }
                    }
                });
            } else {
                console.log('duck');
            }
            $('#listModal').modal('hide');
        });
        //xóa đối tượng trong nhóm
        $('#tbody-data-group').on('click', '.deleteOJ', function() {
            const id = $(this).data('id');
            const idGroup = $(this).data('id-group');
            const button = $(this);

            $.ajax({
                type: 'get',
                url: "{{ route('deleteOJ') }}",
                data: {
                    id: id,
                    idGroup: idGroup,
                },
                success: function(data) {
                    if (data.success) {
                        button.closest('tr').remove();
                        showAutoToast('success', data.msg);
                    } else {
                        showAutoToast('warning', data.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });
</script>
