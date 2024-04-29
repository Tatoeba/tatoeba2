document.addEventListener("DOMContentLoaded", () => {
    /*
    Note: The default nginx request body size (client_max_body_size)
    is only 1MB. Form submissions for avatar images often exceed this
    limit and must be checked to prevent HTTP 413 responses.
    */
    const MAX_FORM_BYTES = 1024 * 1024; // 1MB
    let avatar_upload_form = document.getElementById("upload-avatar-form");
    avatar_upload_form.addEventListener("change", async (event) => {
        let files = document.getElementById("upload-avatar-files").files;
        if (files && files.length >= 1) {
            // Reject too large images based on total form size
            let form_data = new FormData(avatar_upload_form);
            const response = new Response(form_data);
            await response.blob()
            .then(blob => {
                if (blob.size >= MAX_FORM_BYTES) {
                    alert(avatar_upload_form.getAttribute("msg-too-large-image"));
                    avatar_upload_form.reset();
                }
            })
            .catch(() => {
                alert(avatar_upload_form.getAttribute("msg-too-large-image"));
                avatar_upload_form.reset();
            });
        }
    })
})
