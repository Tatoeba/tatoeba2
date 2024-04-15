document.addEventListener("DOMContentLoaded", () => {
    let avatar_upload_form = document.getElementById("upload-avatar-form");
    avatar_upload_form.addEventListener("submit", e => {
        let files = document.getElementById("upload-avatar-files").files;
        if (files && files.length == 1) {
            let f = files[0];
            if (f.size > 1024 * 1024) {
                alert(avatar_upload_form.getAttribute("msg-too-large-image"));

                // Stop form submission
                e.preventDefault();
            }
        }
    })
})
