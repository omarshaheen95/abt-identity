


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

    function runToastify(res, status){
        let backgroundColor,
            position = "right";
        if($("html").attr("dir") == "rtl"){
            position = "left";
        }
        if(status == "success"){
            backgroundColor= "#01C2A0";
        }
        if(status == "error"){
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



    /*---------------------------------
        form submit
    ---------------------------------*/

    // $("#login-form").on("submit", function(e){
    //     e.preventDefault();
    //     var _form = $(this);
    //     if (_form[0].checkValidity() === false) {
    //         e.stopPropagation();
    //     } else {
    //         _form.find(".btn-submit .spinner-border").toggleClass("d-none");
    //         _form.addClass("disabled");
    //
    //         setTimeout( function(){
    //             runToastify("تم حفظ البيانات بنجاح", "success")
    //             _form.find(".btn-submit .spinner-border").toggleClass("d-none");
    //             _form.removeClass("disabled was-validated");
    //             _form[0].reset();
    //         }, 2000);
    //     }
    // });
