@include('partials.header', ['activeGroup' => 'systemFirst', 'activeName' => 'products'])
@section('title', $title)
<div class="content-header-fixed px-1">
    <div class="content__header--inner">
    </div>
</div>
</div>
<div class="content margin-top-127">
    <section class="content">
        <div class="container-fluided">
            <div class="row result-filter-guest margin-left20 my-1 mr-0">
            </div>
            <div class="col-12 p-0 m-0">
                <div class="card">
                    <h3 class="text-center">Danh sách trùng lặp</h3>
                    <div class="outer2 table-responsive text-nowrap">
                        @if (!empty($duplicates))
                            <form action="{{ route('products.bulkConfirm') }}" method="POST">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select_all"></th>
                                            <!-- Checkbox chọn/deselect tất cả -->
                                            <th>Mã sản phẩm</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Hãng</th>
                                            <th>Bảo hành</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($duplicates as $duplicate)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="products[]"
                                                        value="{{ json_encode($duplicate) }}">
                                                </td> <!-- Gửi dữ liệu của từng row -->
                                                <td>{{ $duplicate['row_data']->get(0) ?? 'N/A' }}</td>
                                                <!-- Mã sản phẩm -->
                                                <td>{{ $duplicate['row_data']->get(1) ?? 'N/A' }}</td>
                                                <!-- Tên sản phẩm -->
                                                <td>{{ $duplicate['row_data']->get(2) ?? 'N/A' }}</td>
                                                <!-- Thương hiệu -->
                                                <td>{{ $duplicate['row_data']->get(3) ?? 'N/A' }}</td> <!-- Bảo hành -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary custom-btn mx-1 d-flex align-items-center h-100">Xác nhận hàng loạt</button>
                                    <a href="{{ route('products.index') }}">
                                        <button type="button"
                                            class="btn-save-print rounded mx-1 py-1 px-2 d-flex align-items-center h-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <path
                                                    d="M5.6738 11.4801C5.939 11.7983 6.41191 11.8413 6.73012 11.5761C7.04833 11.311 7.09132 10.838 6.82615 10.5198L5.3513 8.75H12.25C12.6642 8.75 13 8.41421 13 8C13 7.58579 12.6642 7.25 12.25 7.25L5.3512 7.25L6.82615 5.4801C7.09132 5.1619 7.04833 4.689 6.73012 4.4238C6.41191 4.1586 5.939 4.2016 5.6738 4.5198L3.1738 7.51984C2.942 7.79798 2.942 8.20198 3.1738 8.48012L5.6738 11.4801Z"
                                                    fill="black"></path>
                                            </svg>
                                            <span class="text-button text-dark ml-2 font-weight-bold">Trở về</span>
                                        </button>
                                    </a>
                                </div>
                            </form>
                        @else
                            <p>Không có sản phẩm trùng lặp nào.</p>
                        @endif
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $('#select_all').on('change', function() {
                            $('input[name="products[]"]').prop('checked', this.checked);
                        });
                    });
                </script>

            </div>
        </div>
    </section>
</div>
