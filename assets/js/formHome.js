document.addEventListener("DOMContentLoaded", function () {
    const messageContainer = document.getElementById("message-container");
    const appointmentForm = document.getElementById("appointment-form");

    // Function to display message
    function showMessage(message, isSuccess = true) {
        messageContainer.textContent = message;
        messageContainer.classList.add(isSuccess ? "success" : "error");
        messageContainer.classList.remove("fade-out");
        messageContainer.style.display = "block";

        // Automatically hide the message after 3 seconds
        setTimeout(() => {
            messageContainer.classList.add("fade-out");
            setTimeout(() => {
                messageContainer.style.display = "none";
                messageContainer.classList.remove("success", "error");
            }, 2000);
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
                console.log(data); // Log the response for debugging)
            })
            .catch((error) => {
                // Display error message
                showMessage("Đã có lỗi xảy ra. Vui lòng thử lại sau.", false);
                console.error("Error:", error);
            });
    });
});
