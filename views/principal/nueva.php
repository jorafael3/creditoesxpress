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
</style>

<div class="row justify-content-center">
    <div class="col-8 mt-20">
        <div class="card ">
            <div class="card-body">
                <div class="stepper stepper-pills" id="kt_stepper_example_basic">
                    <div class="stepper-nav flex-center flex-wrap mb-10">
                        <div class="stepper-item mx-8 my-4 current" data-kt-stepper-element="nav">
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
                            <div class="stepper-line h-40px"></div>
                        </div>
                        <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
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
                            <div class="stepper-line h-40px"></div>
                        </div>
                        <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
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
                            <div class="stepper-line h-40px"></div>
                        </div>

                    </div>
                    <form class="form w-lg-500px mx-auto" novalidate="novalidate" id="kt_stepper_example_basic_form">
                        <div class="mb-5">
                            <div class="flex-column current" data-kt-stepper-element="content">
                                <div class="fv-row mb-10">
                                    <label class="form-label fw-bold fs-2">Ingresa tu número celular</label>
                                    <h6 class="text-muted">Se enviara un código de verificación para validar en número</h6>
                                    <input placeholder="xxxxxxxxxx" id="CELULAR" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                                </div>
                                <div class="fv-row mb-10">
                                    <label class="form-label">Terminos y condiciones</label>
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input id="TERMINOS" class="form-check-input" checked="checked" type="checkbox" value="1" />
                                        <span class="form-check-label">
                                            Acepto los terminos y condiciones
                                        </span>
                                    </label>
                                </div>

                            </div>
                            <div class="flex-column" data-kt-stepper-element="content">
                                <div id="SECC_COD">

                                </div>
                            </div>
                            <div class="flex-column" data-kt-stepper-element="content">
                                <div id="SECC_CRE">
                                    <div class="fv-row mb-10">
                                        <label class="form-label d-flex align-items-center">
                                            <span class="required fw-bold fs-2">Cédula</span>
                                        </label>
                                        <input type="hidden" id="CEL" value="' . $cel . '">
                                        <input placeholder="xxxxxxxxxx" id="CEDULA" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                                    </div>
                                    <div class="fv-row mb-10">
                                        <label class="form-label d-flex align-items-center">
                                            <span class="fw-bold fs-2">Correo (opcional)</span>
                                        </label>
                                        <input placeholder="xxxxxxx@mail.com" id="CORREO" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="d-flex flex-stack justify-content-center">
                            <div class="me-2">
                                <!-- <button type="button" class="btn btn-light btn-active-light-success fs-3 fw-bold" data-kt-stepper-action="previous">
                                    Regresar
                                </button> -->
                            </div>
                            <div>
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






<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<script src="assets/js/scripts.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<?php require 'views/footer.php'; ?>
<?php require 'funciones/guardar_js.php'; ?>
<script>
    var codeInputs = $('.code-input');

    // Añadir evento de entrada para cada campo
    codeInputs.on('input', function() {
        // Obtener el índice del campo actual
        var currentIndex = codeInputs.index(this);

        // Mover al siguiente campo si se ha ingresado un dígito
        if ($(this).val().length === 1 && currentIndex < codeInputs.length - 1) {
            codeInputs.eq(currentIndex + 1).focus();
        }
    });
    codeInputs.first().focus();

    // document.addEventListener('DOMContentLoaded', function() {
    var codeInputs = document.querySelectorAll('.code-input');
    codeInputs.forEach(function(input, index) {
        input.addEventListener('input', function(event) {
            if (event.inputType === 'deleteContentBackward' && index > 0) {
                if (this.value === '') {
                    if (index == 1) {
                        codeInputs[0].focus();
                    } else {
                        codeInputs[index - 1].focus();
                    }
                }
            } else if (index < codeInputs.length - 1) {
                // codeInputs[index + 1].focus();
            }
        });
    });
    // codeInputs[0].focus();
    // });
</script>