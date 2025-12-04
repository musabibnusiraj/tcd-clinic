$(document).ready(function () {
    $(".bookNowBtn").on("click", function () {
        var doctorId = $(this).data('doctor-id');
        var selectedWeek = $("#week_date_" + doctorId).val();

        // Check if the selected week is not null
        if (selectedWeek !== null && selectedWeek !== "") {
            // Validate if the selected week is not in the past
            if (validateSelectedWeek(selectedWeek)) {
                // Redirect with the selected week as a parameter
                window.location.href = "available_channelings_by_doctor.php?week=" + selectedWeek + "&doctor_id=" + doctorId;
            } else {
                // Show an error message or take appropriate action for invalid selection
                alert("Invalid selection. Please choose a future week.");
            }
        } else {
            // Show an error message for null or empty selection
            alert("Please select a week before booking.");
        }
    });

    function validateSelectedWeek(selectedWeek) {
        // Extract the year and week number
        var match = selectedWeek.match(/^(\d{4})-W(\d{2})$/);
        if (!match) {
            return false; // Invalid format
        }

        var year = parseInt(match[1]);
        var week = parseInt(match[2]);

        // Get the first day (Monday) of the selected week
        var selectedDate = new Date(year, 0, (week) * 7 + 1);

        // Check if the selected week is in the future
        return selectedDate >= new Date();
    }
});