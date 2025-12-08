let productArr = [];
$("#tbody-product-data tr.row-product").each(function () {
    const productId = parseInt($(this).data("product-id"), 10); // Lấy giá trị data-product-id và chuyển thành số
    if (!productArr.includes(productId)) {
        productArr.push(productId);
    }
});
$("#listProducts ul li").each(function () {
    const itemId = parseInt($(this).data("id"), 10); // Lấy giá trị data-id và chuyển thành số
    if (productArr.includes(itemId)) {
        $(this).hide(); // Ẩn sản phẩm nếu ID đã tồn tại trong productArr
    } else {
        $(this).show(); // Hiện sản phẩm nếu ID không có trong productArr
    }
});
function showAutoToast(type, message) {
    let color;
    switch (type) {
        case "success":
            color = "#09BD3C";
            break;
        case "warning":
            color = "#FF9500";
            break;
        default:
            color = "#343a40";
    }

    // Tạo một div để chứa nội dung HTML
    let messageDiv = document.createElement("div");
    messageDiv.innerHTML = message;
    messageDiv.style.color = "#fff";
    messageDiv.style.padding = "8px";
    messageDiv.style.lineHeight = "1.5";
    messageDiv.style.maxWidth = "700px";
    messageDiv.style.wordBreak = "break-word";

    Toastify({
        node: messageDiv,
        duration: 5000,
        close: true,
        gravity: "top",
        position: "center",
        backgroundColor: color, // màu nền
    }).showToast();
}
$(document).ready(function () {
    // Hàm thêm dòng
    $("#add-rows").click(function (event) {
        let rowCount = $("#table-body tr").length;
        event.preventDefault(); // Ngăn nút submit form
        const numRows = parseInt($("#row-count").val(), 10); // Lấy số lượng dòng cần thêm từ input
        const warehouse_id = $("#warehouse_id").val();
        if (warehouse_id == 2) {
            $("#warehouse_receive").show();
        } else {
            $("#warehouse_receive").hide();
        }
        const hideColumnReceive = warehouse_id == 2 ? "" : "d-none";

        if (isNaN(numRows) || numRows <= 0) {
            showAutoToast(
                "warning",
                "Vui lòng nhập số dòng hợp lệ (lớn hơn 0)"
            );
            return;
        }
        for (let i = 0; i < numRows; i++) {
            rowCount++; // Tăng số thứ tự
            const newRow = `
                <tr class="height-40">
                    <td class="text-13-black border py-0 text-center">${rowCount
                        .toString()
                        .padStart(2, "0")}</td>
                    <td class="text-13-black border py-0 pl-3 position-relative">
                        <input type="text" id="form_code" name="form_code" style="flex:2;"
                            autocomplete="off" placeholder="Nhập thông tin"
                            class="text-13-black w-100 border-0 seri-input-check">
                        <span class="check-icon"></span>
                    </td>
                    <td class="text-13-black border py-0 pl-3 position-relative ${hideColumnReceive}">
                        <input type="text" id="serial_borrow" name="serial_borrow" style="flex:2;"
                            autocomplete="off" placeholder="Nhập thông tin"
                            class="text-13-black w-100 border-0">
                        <span class="check-icon-borrow"></span>
                    </td>
                    <td class="text-13-black border py-0 text-center">
                        <button class="btn btn-sm delete-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.8226 5.6875C11.0642 5.6875 11.2601 5.88338 11.2601 6.125C11.2601 6.15874 11.2562 6.19237 11.2485 6.2252L9.94245 11.7758C9.75642 12.5663 9.05109 13.125 8.23897 13.125H5.76103C4.94894 13.125 4.24355 12.5663 4.05755 11.7758L2.75152 6.2252C2.69618 5.99001 2.84198 5.75447 3.07718 5.69913C3.11002 5.6914 3.14365 5.6875 3.17739 5.6875H10.8226ZM7.4375 0.875C8.64562 0.875 9.625 1.85438 9.625 3.0625H11.375C11.8583 3.0625 12.25 3.45426 12.25 3.9375V4.375C12.25 4.61662 12.0541 4.8125 11.8125 4.8125H2.1875C1.94588 4.8125 1.75 4.61662 1.75 4.375V3.9375C1.75 3.45426 2.14175 3.0625 2.625 3.0625H4.375C4.375 1.85438 5.35438 0.875 6.5625 0.875H7.4375ZM7.4375 2.1875H6.5625C6.07926 2.1875 5.6875 2.57925 5.6875 3.0625H8.3125C8.3125 2.57925 7.92074 2.1875 7.4375 2.1875Z"
                                fill="#151516" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
            $("#table-body").append(newRow); // Thêm dòng mới vào bảng
        }
    });

    // Hàm xóa dòng
    $(document).on("click", ".delete-row", function (event) {
        event.preventDefault();
        const currentRow = $(this).closest("tr");

        if (currentRow.hasClass("row-product")) {
            const productId = currentRow.data("product-id");
            const serial = currentRow.data("serial");

            // Lưu danh sách các hàng cần xóa
            const rowsToDelete = [currentRow];

            // Duyệt qua các hàng tiếp theo, xóa hàng bảo hành cùng serial
            currentRow.nextAll("tr").each(function () {
                const $nextRow = $(this);

                if ($nextRow.hasClass("row-product")) {
                    return false; // Dừng nếu gặp hàng sản phẩm khác
                }

                if (
                    $nextRow.hasClass("row-warranty") &&
                    $nextRow.data("serial") === serial
                ) {
                    rowsToDelete.push($nextRow);
                }
            });

            // Xóa tất cả hàng liên quan đến serial này
            rowsToDelete.forEach((row) => row.remove());

            // Kiểm tra xem có còn hàng bảo hành cho serial này hay không
            const hasWarranties =
                $(
                    `#tbody-product-data tr.row-warranty[data-product-id="${productId}"][data-serial="${serial}"]`
                ).length > 0;

            // Kiểm tra xem có còn hàng sản phẩm cho serial này hay không
            const hasProduct =
                $(
                    `#tbody-product-data tr.row-product[data-product-id="${productId}"][data-serial="${serial}"]`
                ).length > 0;

            // Nếu không còn sản phẩm và bảo hành cho serial này, xóa hàng "+"
            if (!hasWarranties && !hasProduct) {
                $(
                    `#tbody-product-data tr.add-warranty-row[data-product-id="${productId}"][data-serial="${serial}"]`
                ).remove();
            }

            // Xóa khỏi mảng productArr nếu không còn hàng nào cho productId
            const remainingRows = $(
                `#tbody-product-data tr[data-product-id="${productId}"]`
            ).not("#serials-count, #add-row-product");
            if (remainingRows.length === 0) {
                // Xóa hàng serials-count và add-row-product
                $(
                    "#tbody-product-data tr#serials-count[data-product-id='" +
                        productId +
                        "']"
                ).remove();
                $(
                    "#tbody-product-data tr#add-row-product[data-product-id='" +
                        productId +
                        "']"
                ).remove();

                // Xóa khỏi mảng
                const index = productArr.indexOf(productId);
                if (index > -1) {
                    productArr.splice(index, 1);
                }

                // Hiển thị lại mục trong danh sách mã sản phẩm
                $("#listProducts ul li").each(function () {
                    const item_id = parseInt($(this).data("id"), 10); // Chuyển thành số nguyên
                    if (!productArr.includes(item_id)) {
                        $(this).show(); // Hiển thị lại nếu không có trong mảng
                    }
                });
            } else {
                // Tính số lượng serial
                const serialCount = remainingRows.filter(function () {
                    // Lọc các hàng có productId và serial hợp lệ
                    const $row = $(this);
                    return (
                        $row.hasClass("row-product") ||
                        ($row.hasClass("row-warranty") &&
                            $row.data("serial") === serial)
                    );
                }).length;

                // Cập nhật lại số lượng serial
                const serialCountRow = $(
                    `#tbody-product-data tr#serials-count[data-product-id="${productId}"]`
                );
                if (serialCountRow.length > 0) {
                    serialCountRow
                        .find('input[name="serial_count"]')
                        .val(serialCount);
                }
            }
        } else if (currentRow.hasClass("row-warranty")) {
            const productId = currentRow.data("product-id");
            const serial = currentRow.data("serial");
            currentRow.remove();

            // Kiểm tra nếu không còn bảo hành cho serial này thì xóa hàng "+"
            setTimeout(() => {
                const hasWarranties =
                    $(
                        `#tbody-product-data tr.row-warranty[data-product-id="${productId}"][data-serial="${serial}"]`
                    ).length > 0;
                const hasProduct =
                    $(
                        `#tbody-product-data tr.row-product[data-product-id="${productId}"][data-serial="${serial}"]`
                    ).length > 0;

                if (!hasWarranties && !hasProduct) {
                    $(
                        `#tbody-product-data tr.add-warranty-row[data-product-id="${productId}"][data-serial="${serial}"]`
                    ).remove();
                }
            }, 0);
        } else {
            currentRow.remove();
        }

        updateRowNumbers();
        updateSerialCount();
        calculateTotalSerialCount();
    });

    //Xóa hàng bảo hành
    $(document).on("click", ".delete-warranty", function (event) {
        event.preventDefault();
        const currentRow = $(this).closest("tr");
        currentRow.remove();
    });

    // Hàm cập nhật số thứ tự
    function updateRowNumbers() {
        rowCount = 0; // Đặt lại số thứ tự
        $("#table-body tr").each(function (index) {
            rowCount = index + 1; // Cập nhật lại số thứ tự
            $(this).find("td:first").text(rowCount.toString().padStart(2, "0"));
        });
    }
});

function getSerialNumbers() {
    let serialNumbers = [];
    // Lặp qua tất cả các dòng trong bảng
    $("#table-body tr").each(function () {
        let serialInput = $(this).find('input[name="form_code"]').val(); // Lấy giá trị input
        if (serialInput.trim() !== "") {
            // Nếu ô input không trống
            serialNumbers.push(serialInput); // Thêm vào mảng
        }
    });

    return serialNumbers; // Trả về mảng serial numbers
}

function getSerialNumbersBorrow() {
    let serialNumbersBorrow = [];
    $("#table-body tr").each(function () {
        let serialInput = $(this).find('input[name="serial_borrow"]').val();
        if (serialInput && serialInput.trim() !== "") {
            serialNumbersBorrow.push(serialInput.trim());
        }
    });
    return serialNumbersBorrow;
}

function getWarranty() {
    let warrantyArr = [];

    // Lặp qua tất cả các hàng trong bảng
    $("#body-warranty tr").each(function () {
        // Tìm input name="name_warranty" và đảm bảo nó tồn tại
        let nameWarrantyInput = $(this).find('input[name="name_warranty"]');
        let nameWarranty =
            nameWarrantyInput.length > 0 ? nameWarrantyInput.val() : "";

        // Tìm input name="product_warranty_input" và đảm bảo nó tồn tại
        let productWarrantyInput = $(this).find(
            'input[name="product_warranty_input"]'
        );
        let productWarranty =
            productWarrantyInput.length > 0 ? productWarrantyInput.val() : "";

        // Kiểm tra nếu ít nhất một trong hai trường có giá trị
        if (nameWarranty.trim() !== "" || productWarranty.trim() !== "") {
            warrantyArr.push({
                name_warranty: nameWarranty.trim(),
                product_warranty_input: productWarranty.trim(),
            });
        }
    });

    return warrantyArr;
}

function getProduct() {
    //kiểm tra trang hiện tại
    let name_modal = $("#name_modal").val();
    let product = {};
    let productCode = $("#product_code_input").val().trim();
    let productName = $("#product_name_input").val().trim();
    let productBrand = $("#product_brand_input").val().trim();
    let productId = $("#product_id_input").val().trim();
    if (name_modal == "XH" || name_modal == "CXH") {
        let productWarranty = $("#product_warranty_input").val().trim();
        let nameWarranty = $("#name_warranty").val().trim();
        if (productWarranty !== "" || nameWarranty !== "") {
            product.warranty = productWarranty;
            product.name_warranty = nameWarranty;
        }
    }
    if (productCode !== "") {
        product.product_code = productCode;
    }
    if (productName !== "") {
        product.product_name = productName;
    }
    if (productBrand !== "") {
        product.product_brand = productBrand;
    }
    if (productId !== "") {
        product.id = productId;
    }

    return product;
}

function clearModalInputs(modalId) {
    $("#" + modalId)
        .find("input")
        .not("#name_modal,#row-count") // Loại trừ phần tử có ID là name_modal
        .val("");
    $(".check-icon").text("");
    $(".count-seri").text("0");
}

// Xử lý sự kiện cho nút .btn-destroy-modal
$(document).on("click", ".btn-destroy-modal", function () {
    let modalId = $(this).data("modal-id");
    clearModalInputs(modalId);
    console.log("Đã bấm nút btn-destroy-modal");
});

// Xử lý sự kiện cho nút .btn-save-print
$(document).on("click", ".btn-save-print", function () {
    let modalId = $(this).data("modal-id");
    clearModalInputs(modalId);

    console.log("Đã bấm nút btn-save-print");
    $("#body-warranty .option-warranty-product").remove();
    $("#table-body").empty();
    $("#row-count").val(5);
    $("#add-rows").click();
});

// Hàm xử lý khi bấm nút Xác nhận
$(document).on("click", ".submit-button", function (event) {
    event.preventDefault(); // Ngăn chặn hành vi mặc định của nút

    let hasError = false;
    $(".seri-input-check").each(function () {
        const $checkIcon = $(this).siblings(".check-icon");
        if ($checkIcon.text() === "✖") {
            hasError = true;
            return false;
        }
    });
    if (hasError) {
        showAutoToast(
            "warning",
            "Có số serial không hợp lệ, vui lòng kiểm tra lại."
        );
        return;
    } else {
        console.log("Tất cả serial đều hợp lệ.");
    }

    $("[name='serial_borrow']").each(function () {
        const $checkIcon = $(this).siblings(".check-icon-borrow");
        if ($checkIcon.text() === "✖") {
            hasError = true;
            return false;
        }
    });
    if (hasError) {
        showAutoToast(
            "warning",
            "Có số serial không hợp lệ, vui lòng kiểm tra lại."
        );
        return;
    } else {
        console.log("Tất cả serial đều hợp lệ.");
    }

    let name_modal = $("#name_modal").val();
    let serialNumbers = getSerialNumbers(); // Lấy mảng serial numbers
    const warehouse_id = $("#warehouse_id").val();
    let serialNumbersBorrow = [];
    if (warehouse_id == 2) {
        $("#title-borrow").show();
        serialNumbersBorrow = getSerialNumbersBorrow();
        if (serialNumbersBorrow.length === 0) {
            showAutoToast("warning", "Vui lòng nhập serial number mượn");
            return;
        }
        let hasError = false;

        $("#table-body tr").each(function () {
            let formCode =
                $(this).find("[name='form_code']").val()?.trim() || "";
            let serialBorrow =
                $(this).find("[name='serial_borrow']").val()?.trim() || "";

            if (formCode && serialBorrow && formCode === serialBorrow) {
                showAutoToast(
                    "warning",
                    "Serial number mượn không được trùng với serial number"
                );
                hasError = true;
                return false; // Dừng vòng lặp
            }
        });

        if (hasError) return;
    } else {
        $("#title-borrow").hide();
    }
    let product = getProduct(); // Lấy thông tin sản phẩm
    let warranty = getWarranty();

    if (!product || Object.keys(product).length === 0) {
        showAutoToast("warning", "Vui lòng nhập thông tin sản phẩm.");
        return;
    }
    if (name_modal == "PCK" || name_modal == "CPCK") {
        if (serialNumbers.length === 0) {
            showAutoToast("warning", "Vui lòng nhập ít nhất một serial number");
            return;
        }
    }

    // Kiểm tra nhập S/N trùng
    let duplicates = [];
    let seen = new Set();

    // Duyệt qua từng input để lấy giá trị
    $('input[name="form_code"]').each(function () {
        let value = $(this).val().trim().toLowerCase(); // Chuẩn hóa về chữ thường
        if (seen.has(value) && value !== "") {
            duplicates.push(value); // Thêm giá trị trùng vào mảng
        } else {
            seen.add(value); // Thêm giá trị vào tập hợp
        }
    });

    // Nếu có giá trị trùng, thông báo
    if (duplicates.length > 0) {
        showAutoToast("warning", "Các S/N bị trùng: " + duplicates.join(", "));
        return;
    }

    if (!product || Object.keys(product).length === 0) {
        showAutoToast("warning", "Vui lòng nhập thông tin sản phẩm.");
        return;
    }

    let $tbody = $("#tbody-product-data");

    let dataType = $("#modal-id").attr("data-type");
    if (dataType === "update") {
        $tbody.find("tr[data-product-id='" + product.id + "']").remove();
        $(
            `#tbody-product-data tr.row-warranty[id^="serials-data-${product.id}"]`
        ).remove();
        $("#modal-id").attr("data-type", "create");
    }
    // Thêm các hàng mới từ serialNumbers
    if (serialNumbers.length === 0) {
        // Trường hợp không có serialNumber → vẫn thêm 1 dòng
        let serialBorrow =
            warehouse_id == 2 ? serialNumbersBorrow[0] || "" : "";
        let newRow = createSerialRow(
            0,
            product,
            "",
            serialBorrow,
            name_modal,
            warranty
        );
        $tbody.append(newRow);
    } else {
        serialNumbers.forEach(function (serial, index) {
            let serialBorrow =
                warehouse_id == 2 ? serialNumbersBorrow[index] || "" : "";

            let newRow = createSerialRow(
                index,
                product,
                serial,
                serialBorrow,
                name_modal,
                warranty
            ); // Hàm tạo dòng mới
            $tbody.append(newRow); // Thêm dòng mới vào tbody
        });
    }

    // Thêm hàng cuối cùng để đếm số lượng serial
    let countRow = createCountRow(
        serialNumbers.length,
        product,
        name_modal,
        warranty
    );
    $tbody.append(countRow); // Thêm dòng đếm số lượng vào cuối bảng

    $(".btn-destroy-modal").click(); // Đóng modal
    calculateTotalSerialCount();

    const productId = parseInt(product.id, 10);
    if (!productArr.includes(productId)) {
        productArr.push(productId);
    }
    $("#listProducts ul li").each(function () {
        const item_id = $(this).data("id");
        if (productArr.includes(item_id)) {
            $(this).hide();
        }
    });
});

// Hàm tạo hàng dữ liệu với serial
function createSerialRow(
    index,
    product,
    serial,
    serialBorrow,
    name,
    warranties
) {
    const hideLastColumn = name === "TN" ? "d-block" : "d-none";
    const hideLastWarranty = name === "XH" || name === "CXH" ? "" : "d-none";
    const hideSerialBorrow = $("#warehouse_id").val() == 2 ? "" : "d-none";
    const readonly = name === "PCK" || name === "CPCK" ? "readonly" : "";
    const bgBlue = name === "PCK" || name === "CPCK" ? "" : "bg-input-guest-blue";
    let name_modal = $("#name_modal").val();
    let rows = [];
    console.log(name);
    
    rows.push(`
        <tr id="serials-data" class="row-product bg-white" data-index="${
            index + 1
        }" data-serial="${serial}" data-product-code="${
        product.product_code
    }"  data-product-id="${product.id}">
         <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4 d-none">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 product_id height-32" readonly
                    name="product_id[]" value="${product.id || ""}">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32" readonly
                    name="product_code" value="${product.product_code || ""}">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32" readonly
                    name="product_name" value="${product.product_name || ""}">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 brand height-32" readonly
                    name="brand" value="${product.product_brand || ""}">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="number" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 height-32 ${bgBlue} qty"
                    name="qty[]" value="1" ${readonly}>
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 position-relative">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 serial height-32" readonly
                    name="serial[]" value="${serial}">
                    <span class="list-recei"></span>
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 position-relative ${hideSerialBorrow}">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 serial_borrow height-32" readonly
                    name="serial_borrow_input[]" value="${serialBorrow}">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideLastColumn}">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 status_recept height-32 bg-input-guest-blue"
                    name="status_recept[]" value="">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideLastWarranty}">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 name_warranty height-32 bg-input-guest-blue"
                    name="name_warranty_product[]" placeholder="Thông tin" value="${
                        product.name_warranty || ""
                    }">
            </td>
           <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideLastWarranty}">
    <input type="number" autocomplete="off"
        class="border-0 pl-1 pr-2 py-1 w-100 warranty height-32 bg-input-guest-blue"
        name="warranty[]" placeholder="Tháng" value="${product.warranty || ""}"
        step="1"
       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
       onkeydown="if(event.key === '.' || event.key === ',') event.preventDefault();" >
</td>

            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 note_seri height-32 bg-input-guest-blue"
                    name="note_seri[]" value="">
            </td>
            <td class="p-2 align-top activity border-bottom border-top-0 border-right text-center">
                <svg class="delete-row" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                        fill="#6B6F76"></path>
                </svg>
            </td>
        </tr>
    `);
    // Chỉ thêm thông tin bảo hành nếu có từ hàng thứ 2 trở đi
    warranties.slice(1).forEach((warrantyItem) => {
        rows.push(`
            <tr id="serials-data-${
                product.id
            }" data-serial="${serial}" class="row-warranty bg-white">
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                    <input type="text" autocomplete="off"
                        class="border-0 pl-1 pr-2 py-1 w-100 name_warranty height-32 bg-input-guest-blue"
                        name="name_warranty_product[]" placeholder="Thông tin" value="${
                            warrantyItem.name_warranty || ""
                        }">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                    <input type="number" autocomplete="off"
                        class="border-0 pl-1 pr-2 py-1 w-100 warranty height-32 bg-input-guest-blue"
                        name="warranty[]" placeholder="Tháng" value="${
                            warrantyItem.product_warranty_input || ""
                        }" step="1"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       onkeydown="if(event.key === '.' || event.key === ',') event.preventDefault();">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                <td class="p-2 align-top activity border-bottom border-top-0 border-right text-center">
                    <svg class="delete-row" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z" fill="#6B6F76"></path>
                    </svg>
                </td>
            </tr>
        `);
    });

    if (name_modal == "XH" || name_modal == "CXH") {
        rows.push(`
            <tr class="add-warranty-row" data-product-id="${product.id}" data-serial="${serial}">
                <td colspan="5" class="text-right border-right p-2 text-13 align-top border-bottom border-top-0">
                    <svg class="add-warranty" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.65625 2.625C7.65625 2.26257 7.36243 1.96875 7 1.96875C6.63757 1.96875 6.34375 2.26257 6.34375 2.625V6.34375H2.625C2.26257 6.34375
                        1.96875 6.63757 1.96875 7C1.96875 7.36243 2.26257 7.65625 2.625 7.65625H6.34375V11.375C6.34375 11.7374 6.63757 12.0312 7 12.0312C7.36243
                        12.0312 7.65625 11.7374 7.65625 11.375V7.65625H11.375C11.7374 7.65625 12.0312 7.36243 12.0312 7C12.0312 6.63757 11.7374 6.34375 11.375
                        6.34375H7.65625V2.625Z" fill="#151516"></path>
                    </svg>
                </td>
                <td colspan="4" class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            </tr>
        `);
    }

    return rows.join("");
}

// Hàm tạo hàng cuối cùng để đếm số lượng serial
function createCountRow(count, product, name, warranty) {
    let dnone = name === "XH" || name === "CXH" ? "d-none" : "";
    let warehouse_id = $("#warehouse_id").val();
    let colspanValue1, colspanValue2;
    if (name === "TN") {
        colspanValue1 = 4;
        colspanValue2 = 8;
    } else if (name === "NH" || name === "CNH") {
        colspanValue1 = 3;
        colspanValue2 = 7;
    } else if (name === "XH" || name === "CXH") {
        colspanValue1 = 5;
        colspanValue2 = 9;
    } else if (name === "PCK" || name === "CPCK") {
        if (warehouse_id == 1) {
            colspanValue1 = 3;
            colspanValue2 = 7;
        } else {
            colspanValue1 = 4;
            colspanValue2 = 8;
        }
    }
    // Convert warranty thành chuỗi JSON nếu có
    const warrantyData = warranty
        ? JSON.stringify(warranty)
        : JSON.stringify([]);
    return `
        <tr id="serials-count" class="bg-white" data-product-code="${product.product_code}" data-product-id="${product.id}">
            <td colspan="2" class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4"></td>
            <td class="border-right p-2 text-13 align-center border-bottom border-top-0 text-right text-purble">Tổng số lượng:</td>
            <td class="border-right p-2 text-13 align-center border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 height-32" readonly
                    name="serial_count" value="${count}">
            </td>
            <td colspan="${colspanValue1}" class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
        </tr>
        <tr id="add-row-product" class="bg-white" data-product-code="${product.product_code}" data-product-id="${product.id}">
            <td colspan="${colspanValue2}" class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                <button type="button" class="save-info-product btn" data-product-id="${product.id}" data-product-code="${product.product_code}"
                 data-product-name="${product.product_name}" data-product-brand="${product.product_brand}" data-product-warranty='${warrantyData}'>
                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                    height="16" viewBox="0 0 16 16" fill="none">
                    <path
                        d="M4.75 2.00007C2.67893 2.00007 1 3.679 1 5.75007V11.25C1 13.3211 2.67893 15 4.75 15H10.2501C12.3212 15 14.0001 13.3211 14.0001 11.25V8.00007C14.0001 7.58586 13.6643 7.25007 13.2501 7.25007C12.8359 7.25007 12.5001 7.58586 12.5001 8.00007V11.25C12.5001 12.4927 11.4927 13.5 10.2501 13.5H4.75C3.50736 13.5 2.5 12.4927 2.5 11.25V5.75007C2.5 4.50743 3.50736 3.50007 4.75 3.50007H7C7.41421 3.50007 7.75 3.16428 7.75 2.75007C7.75 2.33586 7.41421 2.00007 7 2.00007H4.75Z"
                        fill="#6D7075" />
                    <path
                    d="M12.1339 5.19461L10.7197 3.7804L6.52812 7.97196C5.77185 8.72823 5.25635 9.69144 5.0466 10.7402C5.03144 10.816 5.09828 10.8828 5.17409 10.8677C6.22285 10.6579 7.18606 10.1424 7.94233 9.38618L12.1339 5.19461Z"
                    fill="#6D7075" />
                    <path
                        d="M13.4559 1.45679C13.2663 1.39356 13.0571 1.44293 12.9158 1.58431L11.7803 2.71974L13.1945 4.13395L14.33 2.99852C14.4714 2.85714 14.5207 2.64802 14.4575 2.45834C14.2999 1.98547 13.9288 1.61441 13.4559 1.45679Z"
                    fill="#6D7075" />
                </svg>
                </button>
            </td>
        </tr>
    `;
}

//lấy thông tin hàng
$(document).ready(function () {
    function toggleListGuest(input, list, filterInput) {
        input.on("click", function () {
            list.show();
        });

        $(document).click(function (event) {
            if (
                !$(event.target).closest(input).length &&
                !$(event.target).closest(filterInput).length
            ) {
                list.hide();
            }
        });

        var applyFilter = function () {
            var value = filterInput.val().toUpperCase();
            list.find("li").each(function () {
                var item_id = $(this).data("id");
                var text = $(this).find("a").text().toUpperCase();

                // Kiểm tra nếu item_id không có trong productArr và khớp với bộ lọc tìm kiếm
                if (!productArr.includes(item_id) && text.indexOf(value) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        };

        input.on("keyup", applyFilter);
        filterInput.on("keyup", applyFilter);
    }

    //nhà cung cấp
    toggleListGuest(
        $("#product_code_input"),
        $("#listProducts"),
        $("#searchProduct")
    );
    //lấy tên nhà cung cấp và id
    $('a[name="info-product"]').on("click", function () {
        const dataName = $(this).data("name");
        const dataCode = $(this).data("code");
        const dataBrand = $(this).data("brand");
        const dataWarranty = $(this).data("warranty");
        const data_id = $(this).data("id");
        $("#product_code_input").val(dataCode);
        $("#product_name_input").val(dataName);
        $("#product_brand_input").val(dataBrand);
        $("#product_id_input").val(data_id);

        // Xóa các dòng bảo hành cũ trước khi thêm mới
        $(".option-warranty-product").remove();

        if (Array.isArray(dataWarranty) && dataWarranty.length > 0) {
            console.log("Danh sách bảo hành:", dataWarranty);

            // Đổ dữ liệu bảo hành đầu tiên vào ô input chính
            $("#name_warranty").val(dataWarranty[0].info);
            $("#product_warranty_input").val(dataWarranty[0].warranty);

            // Nếu có nhiều hơn 1 bảo hành, thêm dòng mới
            let warrantyHtml = "";
            for (let i = 1; i < dataWarranty.length; i++) {
                warrantyHtml += `
                    <tr class="option-warranty-product"> 
                        <td class="text-13-black border border-bottom-0 border-top-0 py-0"></td>
                        <td class="text-13-black border border-bottom-0 border-top-0 py-0"></td>
                        <td class="text-13-black border border-bottom-0 border-top-0 py-0"></td>
                        <td class="text-13-black border border-bottom-0 py-0 border-white">
                            <input type="text" name="name_warranty" placeholder="Thông tin" value="${dataWarranty[i].info}" class="text-13-black w-100 border-0 bg-input-guest-blue p-2">
                        </td>
                        <td class="text-13-black border border-bottom-0 py-0 border-white">
                            <input type="number" name="product_warranty_input" placeholder="Tháng" value="${dataWarranty[i].warranty}" class="text-13-black w-100 border-0 bg-input-guest-blue p-2">
                        </td>
                        <td class="text-13-black border border-bottom-0 py-0 border-white delete-warranty text-center">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.936" fill-rule="evenodd" clip-rule="evenodd" d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z" fill="#6C6F74"></path>
                                <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z" fill="#6C6F74"></path>
                                <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z" fill="#6C6F74"></path>
                                <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z" fill="#6C6F74"></path>
                            </svg>
                        </td>
                    </tr>
                `;
            }

            // Thêm dòng bảo hành sau hàng đầu tiên
            $("#body-warranty tr:first").after(warrantyHtml);
        } else {
            console.log(
                "Không có bảo hành hoặc danh sách rỗng, giữ nguyên bảng."
            );

            // Nếu không có bảo hành, giữ nguyên hoặc có thể xóa nội dung cũ
            $("#name_warranty").val("");
            $("#product_warranty_input").val("");
        }
    });
});
$(document).on("click", ".save-info-product", function (e) {
    e.preventDefault();
    let name_modal = $("#name_modal").val();
    // Lấy giá trị của data-product-id từ phần tử được click
    const productId = $(this).data("product-id");
    const productName = $(this).data("product-name");
    const productCode = $(this).data("product-code");
    const productBrand = $(this).data("product-brand");
    const productWarranty = $(this).data("product-warranty");
    const warehouse_id = $("#warehouse_id").val();
    let dnone = "d-none";
    if ((name_modal == "PCK" || name_modal == "CPCK") && warehouse_id == 2) {
        dnone = "";
    }

    if (warehouse_id == 2) {
        $("#warehouse_receive").show();
    } else {
        $("#warehouse_receive").hide();
    }

    // Khai báo mảng để lưu thông tin sản phẩm
    const products = [];
    // Duyệt qua từng dòng có data-product-id trùng với productId
    $(
        "#tbody-product-data .row-product[data-product-id='" + productId + "']"
    ).each(function () {
        const $row = $(this); // Dòng hiện tại
        // Lấy tất cả thông tin sản phẩm trong dòng này
        const productInfo = {
            serial: $row.find(".serial").val(), // Chỉ lấy thông tin serial
            serial_borrow: $row.find(".serial_borrow").val(),
        };
        // Thêm thông tin sản phẩm vào mảng
        products.push(productInfo);
    });

    // Xóa nội dung cũ trong tbody trước khi đổ dữ liệu mới
    $("#table-body").empty();
    $("#modal-id").attr("data-type", "update");
    $("#modal-id").modal("show");
    $("#product_code_input").val(productCode);
    $("#product_name_input").val(productName || "");
    $("#product_brand_input").val(productBrand || "");
    $("#product_warranty_input").val(productWarranty || "");
    $("#product_id_input").val(productId);
    // Đổ dữ liệu chỉ với serial vào tbody
    products.forEach((product, index) => {
        const row = `
            <tr class="height-40">
                <td class="text-13-black border py-0 text-center">0${
                    index + 1
                }</td>
                <td class="text-13-black border py-0 pl-3 position-relative">
                    <input type="text" name="form_code" value="${
                        product.serial
                    }" class="text-13-black w-100 border-0 serial-input seri-input-check" placeholder="Nhập thông tin">
                     <span class="check-icon"></span>
                </td>
                <td class="text-13-black border py-0 pl-3 position-relative ${dnone}">
                    <input type="text" name="serial_borrow" id="serial_borrow" value="${
                        product.serial_borrow
                    }" class="border-0 pl-1 pr-2 py-1 w-100 serial_borrow height-32" placeholder="Nhập thông tin">
                     <span class="check-icon-borrow"></span>
                </td>
                <td class="text-13-black border py-0 text-center">
                    <button class="btn btn-sm delete-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8226 5.6875C11.0642 5.6875 11.2601 5.88338 11.2601 6.125C11.2601 6.15874 11.2562 6.19237 11.2485 6.2252L9.94245 11.7758C9.75642 12.5663 9.05109 13.125 8.23897 13.125H5.76103C4.94894 13.125 4.24355 12.5663 4.05755 11.7758L2.75152 6.2252C2.69618 5.99001 2.84198 5.75447 3.07718 5.69913C3.11002 5.6914 3.14365 5.6875 3.17739 5.6875H10.8226ZM7.4375 0.875C8.64562 0.875 9.625 1.85438 9.625 3.0625H11.375C11.8583 3.0625 12.25 3.45426 12.25 3.9375V4.375C12.25 4.61662 12.0541 4.8125 11.8125 4.8125H2.1875C1.94588 4.8125 1.75 4.61662 1.75 4.375V3.9375C1.75 3.45426 2.14175 3.0625 2.625 3.0625H4.375C4.375 1.85438 5.35438 0.875 6.5625 0.875H7.4375ZM7.4375 2.1875H6.5625C6.07926 2.1875 5.6875 2.57925 5.6875 3.0625H8.3125C8.3125 2.57925 7.92074 2.1875 7.4375 2.1875Z" fill="#151516"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
        $("#table-body").append(row);
    });
    // Xóa tất cả các dòng có class "option-warranty-product"
    $("#body-warranty .option-warranty-product").remove();
    // Kiểm tra và đổ dữ liệu vào bảng
    productWarranty.slice(1).forEach((warranty) => {
        // Tạo dòng mới với dữ liệu bảo hành từ mỗi phần tử bắt đầu từ chỉ mục 1 (phần tử thứ 2)
        const warrantyRow = `
            <tr class="option-warranty-product height-40">
                <td class="text-13-black border border-bottom-0 py-0 pl-3"></td>
                <td class="text-13-black border border-bottom-0 py-0 pl-3"></td>
                <td class="text-13-black border border-bottom-0 py-0 pl-3"></td>
                <td class="text-13-black border border-bottom-0 py-0 border-white">
                    <input type="text" name="name_warranty" placeholder="Thông tin" id="name_warranty" value="${warranty.name_warranty}" style="flex:2;" class="text-13-black w-100 border-0 bg-input-guest-blue p-2">
                </td>
                <td class="text-13-black border border-bottom-0 py-0 border-white">
                    <input type="number" id="product_warranty_input" name="product_warranty_input" placeholder="Tháng" value="${warranty.product_warranty_input}" style="flex:2;" class="text-13-black w-100 border-0 bg-input-guest-blue p-2">
                </td>
                <td class="text-13-black border border-bottom-0 py-0 border-white delete-warranty text-center">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.936" fill-rule="evenodd" clip-rule="evenodd" d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z" fill="#6C6F74"></path>
                    <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z" fill="#6C6F74"></path>
                    <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z" fill="#6C6F74"></path>
                    <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z" fill="#6C6F74"></path>
                </svg>
                </td>
            </tr>
        `;

        // Thêm dòng mới vào bảng
        $("#option-warranty").before(warrantyRow);
    });
    // Log thông tin sản phẩm tìm được
    // $(".btn-save-print").click();
    $("#product_code_input").val(productCode);
    $("#product_name_input").val(
        productName !== "undefined" ? productName : ""
    );
    $("#product_brand_input").val(
        productBrand !== "undefined" ? productBrand : ""
    );
    if (
        productWarranty.length > 0 &&
        productWarranty[0].product_warranty_input !== undefined
    ) {
        $("#product_warranty_input").val(
            productWarranty[0].product_warranty_input
        );
        $("#name_warranty").val(productWarranty[0].name_warranty);
    } else {
        $("#product_warranty_input").val("");
        $("#name_warranty").val("");
    }
    $("#product_id_input").val(productId);
    updateSerialCount();
});
function updateSerialCount() {
    let count = 0;
    $(".seri-input-check").each(function () {
        const $input = $(this);
        const $checkIcon = $input.siblings(".check-icon");
        const serialNumber = $input.val().trim();
        if (serialNumber === "") {
            $checkIcon.text("").css("color", "transparent");
        }
        if (serialNumber !== "") {
            count++;
        }
    });
    $(".count-seri").text(count);
}
//Hàm tổng số lượng s/n
function calculateTotalSerialCount() {
    let inputs = document.querySelectorAll('input[name="serial_count"]');
    let total = 0;

    inputs.forEach((input) => {
        let value = parseFloat(input.value);
        if (!isNaN(value)) {
            total += value;
        }
    });
    $("#sumSN").text(total);

    return total;
}
// Hàm chung để xử lý việc lấy dữ liệu serial
function collectSerialData() {
    let serialData = [];
    $("#tbody-product-data tr#serials-data").each(function () {
        let productId = $(this).data("product-id");
        let serialInput = $(this).find("input.serial");

        if (serialInput.length > 0) {
            serialInput.each(function () {
                serialData.push({
                    productId: productId,
                    serial: $(this).val(),
                    inputElement: $(this),
                    rowElement: $(this).closest("tr"),
                });
            });
        }
    });
    return serialData;
}

// Hàm chung để kiểm tra serial qua AJAX
function checkSerials(branchId, formType, serialData, callback) {
    if (branchId && formType && serialData.length > 0) {
        $.ajax({
            url: "/check-serial",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                branch_id: branchId,
                form_type: formType,
                serial_data: serialData.map((item) => ({
                    productId: item.productId,
                    serial: item.serial,
                })),
            },
            success: function (response) {
                callback(response);
            },
            error: function (xhr) {
                console.error("Lỗi khi gửi yêu cầu AJAX:", xhr.responseText);
            },
        });
    }
}

//Thêm bảo hành
$("#add-warranty").click(function (e) {
    e.preventDefault();
    $("#option-warranty").before(`
    <tr class="option-warranty-product"> 
        <td class="text-13-black border border-bottom-0 border-top-0 py-0"></td>
        <td class="text-13-black border border-bottom-0 border-top-0 py-0"></td>
        <td class="text-13-black border border-bottom-0 border-top-0 py-0"></td>
        <td class="text-13-black border border-bottom-0 py-0 border-white">
            <input type="text" name="name_warranty" placeholder="Thông tin" id="name_warranty" style="flex:2;" class="text-13-black w-100 border-0 bg-input-guest-blue p-2 ">
        </td>
        <td class="text-13-black border border-bottom-0 py-0 border-white">
            <input type="number" id="product_warranty_input" name="product_warranty_input" placeholder="Tháng" style="flex:2;" class="text-13-black w-100 border-0 bg-input-guest-blue p-2"  step="1"
       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
       onkeydown="if(event.key === '.' || event.key === ',') event.preventDefault();">
        </td>
        <td class="text-13-black border border-bottom-0 py-0 border-white delete-warranty text-center">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.936" fill-rule="evenodd" clip-rule="evenodd" d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z" fill="#6C6F74"></path>
                <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z" fill="#6C6F74"></path>
                <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z" fill="#6C6F74"></path>
                <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd" d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z" fill="#6C6F74"></path>
            </svg>
        </td>
    </tr>
`);
});

$(document).on("click", ".add-warranty-row", function () {
    const productId = $(this).data("product-id");
    const serial = $(this).data("serial");

    // Tạo hàng mới cho bảo hành
    const newRow = `
        <tr class="row-warranty" data-product-id="${productId}" data-serial="${serial}">
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"><input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 name_warranty height-32 bg-input-guest-blue" placeholder="Thông tin" name="name_warranty_product[]"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"><input type="number" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 warranty height-32 bg-input-guest-blue" placeholder="Tháng" name="warranty[]"  step="1"
       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
       onkeydown="if(event.key === '.' || event.key === ',') event.preventDefault();"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 text-center">
                <svg class="delete-row" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z" fill="#6B6F76"></path>
                </svg>
            </td>
        </tr>
    `;

    // Thêm hàng mới ngay trước hàng "+"
    $(this).before(newRow);

    // Cập nhật lại số serial
    updateSerialCount();
});

function validateInput(selector, message, focusSelector = null) {
    const value = $(selector).val();
    if (!value) {
        showAutoToast("warning", message);
        if (focusSelector) {
            $(focusSelector).click();
        }
        return false;
    }
    return true;
}
