<?php

$url_Validar_Celular = constant('URL') . 'principal/Validar_Celular/';
$url_Validar_Codigo = constant('URL') . 'principal/Validar_Codigo/';

?>

<script>
    var url_Validar_Celular = '<?php echo $url_Validar_Celular ?>';
    var url_Validar_Codigo = '<?php echo $url_Validar_Codigo ?>';

    var TELEFONO;

    function Mensaje(t1, t2, ic) {
        Swal.fire(
            t1,
            t2,
            ic
        );
    }

    $("#CELULAR").focus();


    var element = document.querySelector("#kt_stepper_example_basic");

    // Initialize Stepper
    var stepper = new KTStepper(element);

    // Handle next step
    stepper.on("kt.stepper.next", function(stepper) {

        if (stepper.getCurrentStepIndex() === 1) {
            // var celularInput = document.querySelector("#CELULAR");
            // celularInput = celularInput.value.trim();
            // if (celularInput == "") {
            //     Mensaje("Debe ingresar un numero celular", "", "error");
            //     $("#CELULAR").focus();
            //     return false;
            // } else if (celularInput.length != 10) {
            //     Mensaje("Debe ingresar un numero celular valido", "", "error");
            //     $("#CELULAR").focus();
            //     return false;
            // } else {
            //     let terminos = $("#TERMINOS").is(":checked");
            //     if (terminos == false) {
            //         Mensaje("Debe aceptar los terminos y condiciones para continuar", "", "error");
            //         return false;
            //     } else {
            //         Guardar_Celular();
            //     }
            // }
            // var codeInputs = $('.code-input');
            // codeInputs.first().focus();
            stepper.goNext();

        }
        if (stepper.getCurrentStepIndex() === 2) {
            // var codeInputs = $('.code-input');
            // codeInputs.first().focus();
            // Validar_Codigo();
            stepper.goNext();
        }

        stepper.goNext();
    });
    stepper.on("kt.stepper.previous", function(stepper) {
        // stepper.goPrevious();
    });


    function Guardar_Celular(callback) {
        let cel = $("#CELULAR").val();
        let terminos = $("#TERMINOS").is(":checked");
        let param = {
            celular: cel,
            terminos: terminos
        }
        console.log('param: ', param);

        AjaxSendReceiveData(url_Validar_Celular, param, function(x) {
            console.log('x: ', x);
            if (x[0] == 1) {
                TELEFONO = x[1];
                $("#SECC_COD").append(x[2]);
                stepper.goNext();
            } else {
                Mensaje(x[1], "", x[2]);
            }
        });
    }

    function Validar_Codigo() {
        var codeInputs = document.querySelectorAll('.code-input');
        var valores = Array.from(codeInputs).map(function(input) {
            return input.value;
        });
        let CON = 0;
        valores.map(function(x) {
            if (x.trim() == "") {
                Mensaje("Ingrese el codigo de 4 digitos", "", "error")
                return;
            } else {
                CON++;
            }
        });
        if (CON == 4) {
            let param = {
                TELEFONO: $("#CEL_1").val(),
                CODIGO: valores
            }
            console.log('param: ', param);
            AjaxSendReceiveData(url_Validar_Codigo, param, function(x) {
                console.log('x: ', x);
                if (x[0] == 1) {
                    $("#SECC_CRE").append(x[2]);
                    stepper.goNext();
                } else {
                    Mensaje(x[1], "", x[2]);
                }
            });
        }
    }

    function Verificar() {
        let Cedula = $("#CEDULA").val();
        let cel = $("#CEL").val();
        let email = $("#CORREO").val();

        if (Cedula == "") {
            Mensaje("Debe ingresar un número de cédula valido", "", "error")
        } else {
            let param = {
                cedula: cedula,
                celular: cel,
                email: email
            }
        }
    }

    $("#CELULAR").on("input", function() {
        var cleanedValue = $(this).val().replace(/\D/g, '');
        cleanedValue = cleanedValue.slice(0, 10);
        $(this).val(cleanedValue);
    });

    $("#CEDULA").on("input", function() {
        var cleanedValue = $(this).val().replace(/\D/g, '');
        cleanedValue = cleanedValue.slice(0, 10);
        $(this).val(cleanedValue);
    });


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