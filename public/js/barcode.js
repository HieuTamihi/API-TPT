let barcodeData = ""; 
let barcodeTimeout;
const barcodeDelay = 300;

$(document).on("keydown", function (e) {
    if (
        (e.ctrlKey &&
            (e.key === "i" ||
                e.key === "I" ||
                e.key === "j" ||
                e.key === "J" ||
                e.key === "u" ||
                e.key === "U")) ||
        e.key === "F12"
    ) {
        e.preventDefault();
        return;
    }

    if (barcodeTimeout) clearTimeout(barcodeTimeout);

    if (e.key.length === 1 || e.key === "Enter" || e.key === "Tab") {
        if (e.key !== "Enter" && e.key !== "Tab") {
            barcodeData += e.key;
        }

        if (e.key === "Enter" || e.key === "Tab") {
            e.preventDefault();
            
            if (barcodeData.trim() !== "") {
                console.log("Final Barcode:", barcodeData); // Debug
                fillBarcodeAndMoveNext(barcodeData.trim());
            } else {
                moveToNextInput();
            }

            barcodeData = "";
            return;
        }

        barcodeTimeout = setTimeout(() => {
            barcodeData = "";
        }, barcodeDelay);
    }
});

// Hàm điền barcode và focus vào input tiếp theo
function fillBarcodeAndMoveNext(barcode) {
    const inputs = $(".seri-input-check");
    const currentInput = $("input:focus");
    const currentIndex = inputs.index(currentInput);

    if (currentIndex !== -1) {
        $(currentInput).val(barcode);
        const nextInput = inputs[currentIndex + 1];

        if (nextInput) {
            $(nextInput).focus();
        } else {
            $("#add-rows").click();
            setTimeout(() => {
                const updatedInputs = $(".seri-input-check");
                const newIndex = currentIndex + 1;
                if (updatedInputs[newIndex]) {
                    $(updatedInputs[newIndex]).focus();
                }
            }, 200);
        }
    }
}

// Hàm di chuyển con trỏ đến ô tiếp theo nếu không có barcode
function moveToNextInput() {
    const inputs = $(".seri-input-check");
    const currentInput = $("input:focus");
    const currentIndex = inputs.index(currentInput);

    if (currentIndex !== -1) {
        const nextInput = inputs[currentIndex + 1];

        if (nextInput) {
            $(nextInput).focus();
        } else {
            $("#add-rows").click();
            setTimeout(() => {
                const updatedInputs = $(".seri-input-check");
                const newIndex = currentIndex + 1;
                if (updatedInputs[newIndex]) {
                    $(updatedInputs[newIndex]).focus();
                }
            }, 200);
        }
    }
}
