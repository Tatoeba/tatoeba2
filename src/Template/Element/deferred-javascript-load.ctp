<script type="text/javascript">
    function downloadJSAtOnload() {
        var srcs = <?= json_encode($srcs); ?>;
        for (i = 0; i < srcs.length; i++) {
            var element = document.createElement("script");
            element.src = srcs[i];
            document.body.appendChild(element);
        }
    }
    if (window.addEventListener)
        window.addEventListener("load", downloadJSAtOnload, false);
    else if (window.attachEvent)
        window.attachEvent("onload", downloadJSAtOnload);
    else
        window.onload = downloadJSAtOnload;
</script>
