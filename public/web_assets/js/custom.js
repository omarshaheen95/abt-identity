   /*---------------------------------
        Form validation
    ---------------------------------*/

    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })


    /*---------------------------------
        runToastify
    ---------------------------------  */

    function showToastify(res, status){
        let backgroundColor,
            position = "right";
        if($("html").attr("dir") === "rtl"){
            position = "left";
        }
        if(status === "success"){
            backgroundColor= "#01C2A0";
        }
        if(status === "error"){
            backgroundColor= "#F3385D";
        }
        Toastify({
            text: res,
            duration: 3000,
            close:true,
            gravity:"bottom",
            position: position,
            backgroundColor: backgroundColor,
        }).showToast();
    }

