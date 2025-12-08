function handleSmallScreen() {
    let countClick = 1;
    $("#sideGuest").on("click", function () {
        if (countClick === 1) {
            $("#mySidenav").css({
                width: "352px",
                cssText: "width: 352px !important",
            });
            $("#main").css({
                marginRight: "352px",
                cssText: "margin-right: 352px !important",
            });
            $("#show_info_Guest").css({
                opacity: "1",
                cssText: "opacity: 1 !important",
            });
            countClick += 1;
        } else if (countClick === 2) {
            $("#mySidenav").css({ width: "0", cssText: "width: 0 !important" });
            $("#main").css({
                marginRight: "0",
                cssText: "margin-right: 0 !important;",
            });
            $("#show_info_Guest").css({
                opacity: "0",
                cssText: "opacity: 0 !important",
            });
            countClick = 1;
        }
    });
}

function handleLargeScreen() {
    let countClick = 1;
    $("#sideGuest").on("click", function () {
        if (countClick === 1) {
            $("#mySidenav").css({ width: "0", cssText: "width: 0 !important" });
            $("#main").css({
                marginRight: "10px",
                cssText: "margin-right: 10px !important;",
            });
            $("#show_info_Guest").css({
                opacity: "0",
                cssText: "opacity: 0 !important",
            });
            $("#title--fixed").css({ cssText: "right: 0" });
            countClick += 1;
        } else if (countClick === 2) {
            $("#mySidenav").css({
                width: "352px",
                cssText: "width: 352px !important",
            });
            $("#main").css({
                marginRight: "352px",
                cssText: "margin-right: 352px !important;",
            });
            $("#show_info_Guest").css({
                opacity: "1",
                cssText: "opacity: 1 !important",
            });
            $("#title--fixed").css({ cssText: "right: 352px" });
            countClick = 1;
        }
    });
}

// Lấy kích thước màn hình khi trang web được tải
var windowWidth = window.innerWidth;

// Thêm một sự kiện lắng nghe để kiểm tra khi kích thước màn hình thay đổi
$(window).on("resize", function () {
    // Cập nhật kích thước màn hình sau mỗi lần thay đổi
    windowWidth = window.innerWidth;

    // Kiểm tra điều kiện tùy chọn (ví dụ: max-width là 991.98px)
    if (windowWidth <= 991.98) {
        // Xử lý khi màn hình nhỏ hơn hoặc bằng 991.98px
        handleSmallScreen();
    } else {
        // Xử lý khi màn hình lớn hơn 991.98px
        handleLargeScreen();
    }
});

// Gọi một lần khi trang web được tải để xác định trạng thái ban đầu của màn hình
$(document).ready(function () {
    if (windowWidth <= 991.98) {
        handleSmallScreen();
    } else {
        handleLargeScreen();
    }
});
//Thông báo
function showNotification(type, message) {
    // Create the notification element
    var notification = document.createElement("div");
    notification.className =
        "alert alert-" + type + " alert-dismissible fade show";
    notification.setAttribute("role", "alert");
    notification.style.position = "absolute";
    notification.style.top = "0";
    notification.style.left = "50%";
    notification.style.transform = "translate(-50%, 0%)";
    notification.style.zIndex = "999999";

    // Create the message content
    var messageDiv = document.createElement("div");
    messageDiv.className = "message pl-3";
    messageDiv.innerHTML = message;

    // Create the close button
    var closeButton = document.createElement("button");
    closeButton.type = "button";
    closeButton.className = "close";
    closeButton.setAttribute("data-dismiss", "alert");
    closeButton.setAttribute("aria-label", "Close");
    var closeSpan = document.createElement("span");
    closeSpan.className = "d-flex";
    closeSpan.innerHTML =
        '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M18 18L6 6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />' +
        '<path d="M18 6L6 18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />' +
        "</svg>";
    closeButton.appendChild(closeSpan);

    // Append the elements to the notification
    notification.appendChild(messageDiv);
    notification.appendChild(closeButton);

    // Append the notification to the document body
    document.body.appendChild(notification);

    // Show the notification
    notification.style.display = "block";

    // Hide the notification after a certain duration (e.g., 5 seconds)
    setTimeout(function () {
        // Check if the notification is a child of any parent node before attempting to remove it
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}
function formatNumber(number) {
    // Check if the input number is undefined, null, or not a number
    if (number === undefined || number === null || isNaN(number)) {
        return "";
    }

    // Convert the number to a string
    var numberString = number.toString();

    // If it's a decimal with .00, convert to integer
    if (numberString.indexOf(".") !== -1 && /\.\d*0$/.test(numberString)) {
        return numberString.replace(/\.?0+$/, "");
    }

    return numberString;
}
function allowNumericInput(input) {
    // Lọc giá trị để chỉ giữ lại số và một dấu chấm thập phân
    input.value = input.value.replace(/[^\d.]/g, "");

    // Kiểm tra xem có nhiều hơn một dấu chấm không
    var parts = input.value.split(".");
    if (parts.length > 2) {
        // Nếu có nhiều hơn một dấu chấm, giữ lại phần thập phân của phần cuối cùng
        input.value = parts.slice(0, -1).join("") + "." + parts.slice(-1);
    }

    // Nếu đang nhập dấu chấm thập phân và số 0 đầu tiên
    if (input.value.startsWith("0") && input.value !== "0.") {
        // Loại bỏ các số 0 ở đầu
        input.value = parseFloat(input.value);
    }
}

function validateDecimalInput(event, input) {
    // Kiểm tra nếu người dùng đang thêm dấu chấm thập phân và giá trị hiện tại đã chứa một dấu chấm
    if (
        (event.key === "." && input.value.includes(".")) ||
        (isNaN(event.key) && event.key !== ".")
    ) {
        event.preventDefault();
    }
}

function showAutoToast(type, message) {
    let color;
    switch (type) {
        case "success":
            color = "#09BD3C"; // Màu xanh lá cây cho thông báo thành công
            break;
        case "warning":
            color = "#FF9500"; // Màu cam cho thông báo cảnh báo
            break;
        default:
            color = "#343a40"; // Màu mặc định
    }

    Toastify({
        text: message, // Nội dung thông báo
        duration: 3000, // Thời gian hiển thị (ms)
        close: true, // Cho phép đóng thông báo
        gravity: "top", // Vị trí hiển thị (top, bottom, left, right)
        position: "center",
        style: {
            background: color // Màu nền
        }
    }).showToast(); // Hiển thị thông báo toast
}
