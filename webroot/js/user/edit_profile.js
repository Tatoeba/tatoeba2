document.addEventListener("DOMContentLoaded", () => {
    /*
    Note: The default nginx request body size (client_max_body_size)
    is only 1MB. Form submissions for avatar images often exceed this
    limit and must be checked to prevent HTTP 413 responses.
    */
    const MAX_IMAGE_BYTES = 1024 * 1024; // 1MB
    const MAX_FORM_BYTES = MAX_IMAGE_BYTES + 4096; // 1MB + 4KB
    let avatar_upload_form = document.getElementById("upload-avatar-form");
    let form_too_large = false;
    avatar_upload_form.addEventListener("change", async (event) => {
        let files = document.getElementById("upload-avatar-files").files;
        if (files && files.length >= 1) {
            // Reject too large images
            if (files[0].size > MAX_IMAGE_BYTES) {
                event.preventDefault();
                alert(avatar_upload_form.getAttribute("msg-too-large-image"));
                avatar_upload_form.reset();
            }

            // Update form size
            let form_data = new FormData(avatar_upload_form);
            const response = new Response(form_data);
            await response.blob()
            .then(blob => {
                console.log(blob.size)
                if (blob.size > MAX_FORM_BYTES) {
                    form_too_large = true;
                }
            })
            .catch(() => {
                alert(avatar_upload_form.getAttribute("msg-too-large-image"));
                avatar_upload_form.reset();
            });
        }
    })

    avatar_upload_form.addEventListener("submit", async (event) => {
        // Reject too large form submissions
        if (form_too_large) {
            alert(avatar_upload_form.getAttribute("msg-too-large-image"));
            event.preventDefault();
        }
    });
})
