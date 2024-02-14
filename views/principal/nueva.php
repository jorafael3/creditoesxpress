<?php

require 'views/header.php';

// var_dump($this->proveedores);
?><style>
    .verification-code {
        display: flex;
        justify-content: space-between;
        width: 200px;
        /* Ajusta el ancho según sea necesario */
        margin: auto;
    }

    .verification-code input {
        text-align: center;
        width: 60px;
        height: 60px;
        font-size: 24px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        margin: 5px;
        font-weight: bold;
    }

    .code-input {
        text-align: center;
        width: 60px;
        height: 60px;
        font-size: 24px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        margin: 0px;
        font-weight: bold;
    }

    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
    }

    #form-container {
        background-image: url('https://lh3.googleusercontent.com/qYJtNus3vCThuUrqVZwq7K4Ckq9Dwxo6Ea58hFHQUwX90VwezB_-I4etbFHAVBL77wXkDZf0ByUuHijqFb_KelTprIMPnvMUvG5rvJAcoq_zD5nGJzFK_vCDKlOy5Ix2rA=w1280');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        /* padding: 10px; */
        /* Añade relleno para evitar que el contenido se superponga con la imagen */
    }
</style>

<div class="row justify-content-center" id="form-container">
    <div class="col-xl-4 col-md-6" style="margin-top: 80px;">
        <div class="card ">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-5">
                        <img style="width: 100%;" src="https://lh5.googleusercontent.com/_QUNz9ix-1QXYTt_EYIKXhGCYR1k9Alfjlqg2-_9PRO32DrOlG0vD3kM5iao9kuPl_IwM3Mr89IsYj4VwOmhUn4=w1280" alt="">
                    </div>
                    <div class="col-8">
                        <img style="width: 100%;" src="https://lh5.googleusercontent.com/ZbZpdd6vzkMna7UHbhJeFJ-zwmhDFYUrLwTqDYlvLdj-xMUpabGdRENrfcW-pp8q2K1PTouq5U8xRUWVpIICsLy9q7k568mjWz7LKyXzvsQOIyMvNwISetVSIeiKCQs8vw=w1280" alt="">
                    </div>
                </div>


                <div class="stepper stepper-pills" id="kt_stepper_example_basic">
                    <div class="stepper-nav flex-center flex-wrap mb-5">

                        <div class="stepper-item current" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <!-- <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number"></span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">
                                    </h3>

                                    <div class="stepper-desc">
                                    </div>
                                </div> -->
                            </div>
                            <!-- <div class="stepper-line h-40px"></div> -->
                        </div>
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <!-- <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">2</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">
                                        Step 2
                                    </h3>

                                    <div class="stepper-desc">
                                        Description
                                    </div>
                                </div> -->
                            </div>
                            <!-- <div class="stepper-line h-40px"></div> -->
                        </div>
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <!-- <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">3</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">
                                        Step 3
                                    </h3>

                                    <div class="stepper-desc">
                                        Description
                                    </div>
                                </div> -->
                            </div>
                            <!-- <div class="stepper-line h-40px"></div> -->
                        </div>

                    </div>
                    <form class="form mx-auto" novalidate="novalidate" id="kt_stepper_example_basic_form">
                        <div class="mb-5">
                            <div class="flex-column current" data-kt-stepper-element="content">
                                <div id="SECC_CEL">
                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bold fs-1">Ingresa tu número celular</label>
                                        <h6 class="text-muted">Se enviará un código de verificación para validar el número</h6>
                                        <input placeholder="xxxxxxxxxx" id="CELULAR" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                                    </div>
                                    <div class="fv-row mb-10">
                                        <label class="form-label">Terminos y condiciones</label>
                                        <label class="form-check form-check-custom form-check-solid">
                                            <input id="TERMINOS" class="form-check-input" checked="checked" type="checkbox" value="1" />
                                            <span class="form-check-label fw-bold">
                                                He leído y acepto los
                                                <a class="fw-bold" href="#!" onclick="$('#exampleModalLong').modal('show')">
                                                    Términos y Condiciones
                                                </a>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="flex-column" data-kt-stepper-element="content">
                                <div id="SECC_COD">

                                </div>
                            </div>
                            <div class="flex-column" data-kt-stepper-element="content">
                                <div id="SECC_CRE">


                                </div>
                                <div id="SECC_APR">

                                </div>
                            </div>

                        </div>
                        <div class="d-flex flex-stack justify-content-center">
                            <div class="me-2">
                                <!-- <button type="button" class="btn btn-light btn-active-light-success fs-3 fw-bold" data-kt-stepper-action="previous">
                                    Regresar
                                </button> -->
                            </div>
                            <div id="SECC_B">
                                <button onclick="Verificar()" type="button" class="btn btn-success fs-3 fw-bold" data-kt-stepper-action="submit">
                                    <span class="indicator-label">
                                        Verificar
                                    </span>
                                    <span class="indicator-progress">
                                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>

                                <button type="button" class="btn btn-success fs-3 fw-bold" data-kt-stepper-action="next">
                                    Continuar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Términos y condiciones</h5>
                <button class="btn" type="button" onclick="$('#exampleModalLong').modal('hide')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#exampleModalLong').modal('hide')" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>




<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<script src="assets/js/scripts.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<?php require 'views/footer.php'; ?>
<?php require 'funciones/guardar_js.php'; ?>
<script>
    // var codeInputs = $('.code-input');

    // // Añadir evento de entrada para cada campo
    // codeInputs.on('input', function() {
    //     // Obtener el índice del campo actual
    //     var currentIndex = codeInputs.index(this);

    //     // Mover al siguiente campo si se ha ingresado un dígito
    //     if ($(this).val().length === 1 && currentIndex < codeInputs.length - 1) {
    //         codeInputs.eq(currentIndex + 1).focus();
    //     }
    // });
    // codeInputs.first().focus();

    $(document).on('input', '.code-input', function(event) {
        var index = $('.code-input').index(this);
        if (event.originalEvent.inputType === 'deleteContentBackward' && index > 0) {
            if ($(this).val() === '') {
                index == 1 ? $('.code-input').eq(0).focus() : $('.code-input').eq(index - 1).focus();
            }
        } else if (index < $('.code-input').length - 1) {
            $('.code-input').eq(index + 1).focus();
        }
    });

    $(document).on('keydown', '.code-input', function(event) {
        if (event.which === 13) { // 13 is the keycode for Enter
            event.preventDefault();
            Validar_Codigo();
        }
    });

    $("#CELULAR").on('keydown', function(event) {
        if (event.which === 13) { // 13 is the keycode for Enter
            event.preventDefault();
            Guardar_Celular();
        }
    });
</script>