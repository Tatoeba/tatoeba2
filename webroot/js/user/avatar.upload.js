document.addEventListener("DOMContentLoaded", () => {
    let avatar_upload_form = document.querySelector(".newPicture form");
    avatar_upload_form.addEventListener("submit", e => {
        let files = document.querySelector(".newPicture input[type=file]").files;
        for (const f of files) {
            if (f.size > 1024 * 1024) {
                alert("File is too large. Confirm the file you are uploading is less than 1MB in size.");

                // Stop form submission
                e.preventDefault();
            }
        }
    })
})
