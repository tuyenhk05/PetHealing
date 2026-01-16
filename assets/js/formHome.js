document.addEventListener("DOMContentLoaded", function () {
    const messageContainer = document.getElementById("message-container");
    const appointmentForm = document.getElementById("appointment-form");

    // Function to display message
    function showMessage(message, isSuccess = true) {
        messageContainer.textContent = message;
        messageContainer.classList.add(isSuccess ? "success" : "error");
        messageContainer.style.display = "block";

        // Automatically hide the message after 3 seconds
        setTimeout(() => {
            messageContainer.classList.add("fade-out");
            setTimeout(() => {
                messageContainer.style.display = "none";
                messageContainer.classList.remove(
                    "success",
                    "error",
                    "fade-out"
                );
            }, 5000); // Delay 2 seconds to hide the message
        }, 2000);
    }

    // Handle form submission with AJAX
    appointmentForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(appointmentForm);

        // Send data via AJAX
        fetch(`/PetHealing/includes/handle_form_appointment.php`, {
            method: "POST",
            body: formData,
        })
            .then((response) => response.text())
            .then((data) => {
                // Display success message
                showMessage(
                    "Đặt lịch thành công! Chúng tôi sẽ liên hệ với bạn sớm.",
                    true
                );
                console.log(data); // Log the response for debugging
            })
            .catch((error) => {
                // Display error message
                showMessage("Đã có lỗi xảy ra. Vui lòng thử lại sau.", false);
                console.error("Error:", error);
            });
        setTimeout(() => {
            this.reset(); // Xoá toàn bộ dữ liệu đã nhập
        }, 300); // chờ xử lý xong mới reset
    });
});

const serviceName = localStorage.getItem("selectedService");
console.log(localStorage.getItem("selectedService")); // Kiểm tra xem có lấy được tên dịch vụ không
// Nếu có dịch vụ được chọn, điền vào ô input
if (serviceName) {
    document.getElementById("service").value = serviceName;
}

const dateInput = document.getElementById("appointment-date");
const timeInput = document.getElementById("appointment-time");

// Set min date = hôm nay
const today = new Date();
const yyyy = today.getFullYear();
const mm = String(today.getMonth() + 1).padStart(2, "0");
const dd = String(today.getDate()).padStart(2, "0");
const minDate = `${yyyy}-${mm}-${dd}`;
dateInput.min = minDate;

// Set khung giờ hẹn từ 08:00 đến 18:00
timeInput.min = "08:00";
timeInput.max = "18:00";
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.reveal-card');

    const observerOptions = {
        threshold: 0.2 // Kích hoạt khi thấy được 20% thẻ rứa
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;

                // Tuyệt kỹ: Staggered Delay (Trễ so le)
                if (card.classList.contains('center-card')) {
                    // Thẻ giữa hiện ra ngay lập tức rứa
                    setTimeout(() => card.classList.add('active'), 300);
                } else {
                    // Hai thẻ bên cạnh hiện ra trễ hơn 400ms rứa mô
                    setTimeout(() => card.classList.add('active'), 800);
                }
                observer.unobserve(card); // Chỉ chạy một lần cho uy nghiêm rứa
            }
        });
    }, observerOptions);

    cards.forEach(card => observer.observe(card));
});

document.addEventListener('DOMContentLoaded', function () {
    const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Tuyệt kỹ Staggered Delay (Trễ so le) rứa mô
                // Đệ sẽ cho cái Header hiện trước, rồi tới Form và Ảnh
                setTimeout(() => {
                    entry.target.classList.add('active');
                }, 400);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealElements.forEach(el => observer.observe(el));
});
document.addEventListener('DOMContentLoaded', function () {
    const revealElements = document.querySelectorAll('.reveal-up');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => observer.observe(el));
});
 document.addEventListener('DOMContentLoaded', function() {
        const revealElements = document.querySelectorAll('.reveal-up');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Khi phần tử lọt vào tầm mắt, ta ban cho nó class 'active' rứa mô
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target); // Xong rồi thì thôi, không soi nữa rứa
                }
            });
        }, { 
            threshold: 0.1, // Thấy 10% thẻ là kích hoạt "võ công" rứa mô
            rootMargin: '0px 0px -50px 0px' 
        });

        revealElements.forEach(el => observer.observe(el));
 });
document.addEventListener('DOMContentLoaded', function () {
    const revealElements = document.querySelectorAll('.reveal-up');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => observer.observe(el));
});