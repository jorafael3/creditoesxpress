<?php

$url_Guardar_datos = constant('URL') . 'principal/Guardar_datos/';
$url_validar_cedula = constant('URL') . 'principal/Validar_Cedula/';
$url_actualizado = constant('URL') . 'actualizado';

?>

<script>
    var url_Guardar_datos = '<?php echo $url_Guardar_datos ?>';
    var url_validar_cedula = '<?php echo $url_validar_cedula ?>';

    function Mensaje(t1, t2, ic) {
        Swal.fire(
            t1,
            t2,
            ic
        );
    }



    function Validar_Cedula() {
        let ced = $("#cedula").val();
        let param = {
            cedula: ced
        }
        console.log('param: ', param);
        AjaxSendReceiveData(url_validar_cedula, param, function(x) {
            console.log('x: ', x);
            if (x[1] == "error") {
                Mensaje(x[0], "", x[1]);
            } else {
                $("#INJ").empty();
                $("#INJ").append(x[1]);
                $("#kt_modal_new_target_submit").hide();

                setTimeout(() => {
                    $("#nombres").val(x[0][0]["Nombre"]);
                    $("#email").attr("placeholder", x[0][0]["Email"]);
                    $("#telefono").attr("placeholder", x[0][0]["Celular"]);
                }, 100);
                // $("#").val();

            }
        });
    }

    function Guardar_datos() {

        let cedula = $("#cedula").val();
        let telefono = $("#telefono").val();
        let email = $("#email").val();
        let check_g = $("#check_g").is(":checked");
        let check_pd = $("#check_pd").is(":checked");

        let param = {
            cedula: cedula,
            telefono: telefono,
            email: email,
            check_g: check_g,
            check_pd: check_pd,
        }
        console.log('param: ', param);


        AjaxSendReceiveData(url_Guardar_datos, param, function(x) {
            console.log('x: ', x);
            Mensaje(x[0], "", x[1]);
            if (x[1] == "success") {
                window.location.replace('<?php echo $url_actualizado ?>');
            }
        })
    }

    function AjaxSendReceiveData(url, data, callback) {
        var xmlhttp = new XMLHttpRequest();
        $.blockUI({
            message: '<div class="d-flex justify-content-center align-items-center"><p class="mr-50 mb-0">Cargando ...</p> <div class="spinner-grow spinner-grow-sm text-white" role="status"></div> </div>',
            css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
            },
            overlayCSS: {
                opacity: 0.5
            }
        });

        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = this.responseText;
                data = JSON.parse(data);
                callback(data);
            }
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = this.responseText;
                data = JSON.parse(data);
                callback(data);
            }
        }
        xmlhttp.onload = () => {
            $.unblockUI();
            // 
        };
        xmlhttp.onerror = function() {
            $.unblockUI();
        };
        data = JSON.stringify(data);
        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(data);

    }
</script>