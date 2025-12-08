$("body").on("input", "#amount,#qty_money,.number", function (event) {
    // Lấy giá trị đã nhập
    var value = event.target.value;

    // Xóa các ký tự không phải số và dấu phân thập phân từ giá trị
    var formattedValue = value.replace(/[^0-9.]/g, "");

    // Định dạng số với dấu phân cách hàng nghìn và giữ nguyên số thập phân
    var formattedNumber = numberWithCommas(formattedValue);

    event.target.value = formattedNumber;
});

function numberWithCommas(number) {
    // Chia số thành phần nguyên và phần thập phân
    var parts = number.split(".");
    var integerPart = parts[0];
    var decimalPart = parts[1];

    // Định dạng phần nguyên số với dấu phân cách hàng nghìn
    var formattedIntegerPart = integerPart
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    // Kết hợp phần nguyên và phần thập phân (nếu có)
    var formattedNumber =
        decimalPart !== undefined
            ? formattedIntegerPart + "." + decimalPart
            : formattedIntegerPart;

    return formattedNumber;
}
function formatCurrency(value) {
    value = Math.round(value * 100) / 100;

    var parts = value.toString().split(".");
    var integerPart = parts[0];
    var formattedValue = "";

    var count = 0;
    for (var i = integerPart.length - 1; i >= 0; i--) {
        formattedValue = integerPart.charAt(i) + formattedValue;
        count++;
        if (count % 3 === 0 && i !== 0) {
            formattedValue = "," + formattedValue;
        }
    }

    if (parts.length > 1) {
        formattedValue += "." + parts[1];
    }
    return formattedValue;
}
function sumElements(className = "", elementType = "td") {
    // Tạo một đối tượng để lưu tổng theo từng data-id
    let totalsById = {};

    // Biến để lưu tổng của tất cả các phần tử
    let grandTotal = 0;

    // Tạo selector cho các phần tử với class cụ thể
    let selector = `${elementType}.${className}`;

    // Duyệt qua tất cả các phần tử có selector
    $(selector).each(function () {
        // Kiểm tra xem phần tử có bị ẩn không
        if ($(this).css("display") == "none") {
            return; // Bỏ qua phần tử này nếu bị ẩn
        }

        // Lấy giá trị text từ phần tử, bỏ qua dấu phẩy và các khoảng trắng
        let value = $(this).text().trim().replace(/,/g, "");

        // Chuyển giá trị về số
        value = parseFloat(value);

        // Lấy data-id của phần tử
        let dataId = $(this).attr("data-id");

        // Log ra giá trị của phần tử và data-id để kiểm tra
        console.log(
            `Phần tử: ${$(
                this
            ).text()}, data-id: ${dataId}, Giá trị sau xử lý: ${value}`
        );

        // Kiểm tra nếu giá trị là số hợp lệ thì cộng vào tổng theo data-id và grand total
        if (!isNaN(value)) {
            // Cộng vào tổng cho từng data-id
            if (!totalsById[dataId]) {
                totalsById[dataId] = 0;
            }
            totalsById[dataId] += value;

            // Cộng vào tổng toàn bộ
            grandTotal += value;
        } else {
            console.log("Giá trị không hợp lệ:", $(this).text());
        }
    });

    // Trả về cả tổng cộng và tổng theo từng data-id
    return {
        grandTotal: grandTotal, // Tổng cộng của tất cả các phần tử
        totalsById: totalsById, // Tổng theo từng data-id
    };
}
function sumValuesByClassAndId(className, idAttribute, containerTag) {
    var valueSums = {};

    // Step 1: Collect all unique idAttribute values from the elements with the className
    $("." + className).each(function () {
        var id = $(this).data(idAttribute); // Get the id attribute
        if (valueSums[id] === undefined) {
            valueSums[id] = 0; // Initialize with 0
        }
    });

    // Step 2: Iterate over each element with the provided class name and sum the values
    $("." + className).each(function () {
        // Check if the closest parent container (e.g., 'tr') is not hidden
        if ($(this).closest(containerTag).css("display") !== "none") {
            var id = $(this).data(idAttribute); // Get the id attribute (dynamic)
            var value = parseFloat($(this).text().replace(/,/g, "")); // Get the text, remove commas, and convert to a number

            // Add the value to the sum for this id
            valueSums[id] += value;
        }
    });

    return valueSums;
}

function updateValuesByClass(result, className) {
    // Iterate over each element with the provided class name
    $("." + className).each(function () {
        var id = $(this).data("id"); // Get the data-id attribute

        // Check if the result object has a sum for this id
        if (result[id] !== undefined) {
            // Set the text of the current element to the corresponding sum
            $(this).text(result[id].toLocaleString()); // Convert number to a string with commas
        } else {
            // If no sum is found, set it to 0
            $(this).text("0");
        }
    });
}

function countClassOccurrencesById(className, idAttribute, containerTag) {
    var countOccurrences = {};
    $("." + className).each(function () {
        var id = $(this).data(idAttribute); // Get the id attribute
        if (countOccurrences[id] === undefined) {
            countOccurrences[id] = 0; // Initialize the count with 0
        }
    });

    // Step 2: Iterate over each element with the provided class name and count occurrences
    $("." + className).each(function () {
        // Check if the closest parent container (e.g., 'tr') is not hidden
        if ($(this).closest(containerTag).css("display") !== "none") {
            var id = $(this).data(idAttribute); // Get the id attribute (dynamic)

            // Increment the count for this id
            countOccurrences[id] += 1;
        }
    });

    return countOccurrences;
}
