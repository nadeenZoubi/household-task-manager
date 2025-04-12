$(document).ready(function () {
    $("#addMissionForm").submit(function (e) {
        e.preventDefault();

        // Serialize form data
        let formData = $(this).serialize();

        // AJAX request
        $.ajax({
            url: "add_mission_to_task.php",
            data: formData,
            type: "POST",
            success: function (response) {
                response = JSON.parse(response);
                if (response.id) {
                    let missionId = response.id;
                    let missionTitle = $("#missionTitle").val();
                    let addedDate = new Date().toLocaleDateString();
                    let assignedUser = $("#assignTo").val();
                    let tr = `
                    <tr>
                        <td>${missionTitle}</td>
                        <td>${addedDate}</td>
                        <td>${assignedUser}</td>
                        <td class=""><input type="checkbox" class="form-check-input status" data-id="${missionId}"></td>
                    </tr>`;
                    $("#missions_table tbody").append(tr);

                    // Re-enable the submit button
                    $("#addMissionForm button[type='submit']").attr("disabled", false);

                    // Optionally, clear the form
                    $("#addMissionForm")[0].reset();
                } else {
                    alert("Error: " + response.error);
                }
            }
        });
    });

    $(document).on('change', '.status', function () {
        let missionId = $(this).data('id');
        let finishedStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "finished.php",
            type: "POST",
            data: { missionId: missionId, missionStatus: finishedStatus },
            success: function (response) {
                console.log(response);
            },
        });
    });
});
