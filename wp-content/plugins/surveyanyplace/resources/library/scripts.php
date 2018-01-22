<script>
    /**
     *Opens and closes the popup
     */
function surveyanyplace_openPopup() {
    document.getElementById("popup_overlay").style.display = "";
}

function surveyanyplace_closePopup() {
    document.getElementById("popup_overlay").style.display = "none";
}
</script>
<script>
    /**
     *Opens and closes the sametab
     */
    function surveyanyplace_openSame() {
        document.getElementById("sametab_overlay").style.display = "";
    }

    function surveyanyplace_closeSame() {
        document.getElementById("sametab_overlay").style.display = "none";
    }
</script>
<script>
    /**
     *Opens and closes the drawer
     */
    function surveyanyplace_openNav() {
        document.getElementById("main").style.marginLeft = "25%";
        document.getElementById("drawer_overlay").style.display = "";
    }

    function surveyanyplace_closeNav() {
        document.getElementById("main").style.marginLeft= "0";
        document.getElementById("drawer_overlay").style.display = "none";
    }
</script>


