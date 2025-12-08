$(document).ready(function () {
    // Hàm cập nhật số thứ tự
    function updateRowNumbers() {
        $("#tbody-data tr").each(function (index) {
            // Đặt số thứ tự bắt đầu từ 1 trong cột đầu tiên
            $(this)
                .find("td:first")
                .text(index + 1);
        });
    }

    // Khi nút btn-add-row được nhấn
    $("#btn-add-row").on("click", function () {
        // Tạo nội dung của hàng mới
        let rowIndex = $("#tbody-data tr").length; // Lấy chỉ số dòng hiện tại (được gán là index trong bảng)
        let newRow = `
        <tr class="row-product bg-white">
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4"></td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 service_name height-32 bg-input-guest-blue"
                    name="services[${rowIndex}][service_name]" value="" required>
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 unit height-32 bg-input-guest-blue"
                    name="services[${rowIndex}][unit]" value="">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 brand height-32 bg-input-guest-blue"
                    name="services[${rowIndex}][brand]" value="">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="number" min="1" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 quantity height-32 bg-input-guest-blue"
                    name="services[${rowIndex}][quantity]">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="number" step="0.01" min="0" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 unit_price height-32 bg-input-guest-blue"
                    name="services[${rowIndex}][unit_price]">
            </td>
           <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <select class="border-0 pl-1 pr-2 py-1 w-100 height-32 bg-input-guest-blue tax_rate"
                    name="services[${rowIndex}][tax_rate]">
                    <option value="10">10%</option>
                    <option value="8">8%</option>
                    <option value="0">KCT</option>
                </select>
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" step="0.01" min="0" readonly
                    class="border-0 pl-1 pr-2 py-1 w-100 total height-32"
                    name="services[${rowIndex}][total]" value="0">
            </td>
            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                <input type="text" autocomplete="off"
                    class="border-0 pl-1 pr-2 py-1 w-100 note height-32 bg-input-guest-blue"
                    name="services[${rowIndex}][note]" value="">
            </td>
            <td class="p-2 align-top activity border-bottom border-top-0 border-right">
               <button type="button" class="delete-row btn btn-sm"> <svg class="delete-row"
                    width="17" height="17" viewBox="0 0 17 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                        fill="#6B6F76"></path>
                </svg></button>
            </td>
        </tr>`;

        // Thêm hàng mới vào cuối bảng có id là #tbody-data
        $("#tbody-data").append(newRow);
        // Cập nhật lại số thứ tự
        updateRowNumbers();
    });

    // Xóa hàng khi nút delete-row được nhấn
    $("#tbody-data").on("click", ".delete-row", function () {
        $(this).closest("tr").remove();
        // Cập nhật lại số thứ tự sau khi xóa
        updateRowNumbers();
        calculateTotals();
    });

    // Cập nhật số thứ tự ban đầu (nếu cần)
    updateRowNumbers();
});
$(document).on("input", ".quantity, .unit_price", function () {
    var $row = $(this).closest(".row-product");
    var quantity = parseFloat($row.find(".quantity").val()) || 0;
    var unitPrice = parseFloat($row.find(".unit_price").val())|| 0;
    var total = quantity * unitPrice;
    $row.find(".total").val(formatCurrencyVND(total));
});

function formatCurrencyVND(value) {
    return value.toLocaleString("de-DE") + " đ";
}

function calculateTotals() {
    let totalBeforeTax = 0;
    let tax8Amount = 0;
    let tax10Amount = 0;
    $("#tbody-data .row-product").each(function () {
        const $row = $(this);
        const quantity = parseFloat($row.find(".quantity").val()) || 0;
        const unitPrice = parseFloat($row.find(".unit_price").val().replace(/[,.]/g, '')) || 0;
        const taxRate = parseInt($row.find(".tax_rate").val()) || 0;
        const rowTotal = quantity * unitPrice;
        totalBeforeTax += rowTotal;
        if (taxRate === 8) {
            tax8Amount += (rowTotal * 8) / 100;
        } else if (taxRate === 10) {
            tax10Amount += (rowTotal * 10) / 100;
        }
        $row.find(".total").text(rowTotal.toLocaleString("de-DE"));
    });
    const grandTotal = totalBeforeTax + tax8Amount + tax10Amount;
    $("#total-amount-sum").text(totalBeforeTax.toLocaleString("de-DE"));
    $("#product-tax8").text(formatCurrencyVND(tax8Amount));
    $("#product-tax10").text(formatCurrencyVND(tax10Amount));
    $("#grand-total").text(formatCurrencyVND(grandTotal));
    $("#total").val(grandTotal.toFixed(2));

    const printSumElement = document.getElementById("print-sum");
    if (printSumElement) {
        printSumElement.textContent = totalBeforeTax.toLocaleString("de-DE");
    }

    const printVat8Element = document.getElementById("print-vat-8");
    if (printVat8Element) {
        printVat8Element.textContent = tax8Amount.toLocaleString("de-DE");
    }

    const printVat10Element = document.getElementById("print-vat-10");
    if (printVat10Element) {
        printVat10Element.textContent = tax10Amount.toLocaleString("de-DE");
    }

    const sumVatElement = document.getElementById("sum-vat");
    if (sumVatElement) {
        sumVatElement.textContent = grandTotal.toLocaleString("de-DE");
    }
}
$(document).on(
    "input change",
    ".quantity, .unit_price, .tax_rate",
    function () {
        calculateTotals();
    }
);
