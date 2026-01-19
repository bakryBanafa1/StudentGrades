/**
 * Validation Script
 */

function validateLogin() {
    var studentId = document.getElementById("student_id").value;
    var password = document.getElementById("password").value;

    if (studentId.trim() === "" || password.trim() === "") {
        alert("الرجاء إدخال رقم القيد وكلمة المرور");
        return false;
    }
    return true;
}

// Optional: Add more interactivity here if needed
document.addEventListener("DOMContentLoaded", function () {
    // Console log for debug
    console.log("System Ready");
});
