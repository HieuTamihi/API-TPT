@include('partials.header')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- Nội dung trang con sẽ được chèn vào đây -->
            @yield('content')
        </div>
    </div>
</div>

<!-- Bao gồm phần footer -->
@include('partials.footer')

<!-- Thêm JS chung cho toàn bộ trang -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.6/dist/cdn.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
