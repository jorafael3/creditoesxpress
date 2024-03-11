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
        background-image: url('<?php echo constant("URL") ?>/public/img/SV24BackgroundLC.jpg');
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
                        <img style="width: 100%;" src="<?php echo constant("URL") ?>/public/img/SV24 - Logos LC_Salvacero.png" alt="">
                    </div>
                    <div class="col-8">
                        <img style="width: 100%;" src="<?php echo constant("URL") ?>/public/img/SV24 - Logos LC_Credito.png" alt="">
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
                                    <div class="fv-row mb-5">
                                        <label class="form-label fw-bold fs-1">Ingresa tu número celular</label>
                                        <h6 class="text-muted">Se enviará un código de verificación para validar el número</h6>
                                        <input placeholder="xxxxxxxxxx" id="CELULAR" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                                    </div>
                                    <div class="fv-row mb-5">
                                        <label class="form-check form-check-custom form-check-solid">
                                            <a class="fw-bold text-success" href="#!" onclick="$('#exampleModalreq').modal('show')">
                                                Ver requisitos
                                            </a>
                                        </label>
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

                <body>
                    <h1>Términos y Condiciones</h1>
                    <h2>Declaración de Capacidad legal y sobre la Aceptación</h2>
                    <p>Por este medio, por mis propios y personales derechos de manera libre y voluntaria, declaro que soy mayor de edad, ecuatoriano o extranjero en residencia legal en el Ecuador y me encuentro en capacidad y habilidad legal para obligarme y suscribir todo tipo de acto jurídico según las normas del Código Civil y del Ordenamiento Jurídico Ecuatoriano, por lo cual, SALVACERO CIA. LTDA no tiene responsabilidad alguna sobre la condición jurídica previa o posterior del declarante, siendo responsabilidad exclusiva del usuario que autoriza la correcta utilización de los medios electrónicos utilizados para acceder a este canal digital y la conciencia de su habilidad o capacidad civil. En tal sentido, SALVACERO CIA. LTDA, una vez expuestos estos términos y condiciones, asume la aceptación como legítima.</p>

                    <h2>Autorizaciones</h2>

                    <h3>1. Autorización de consulta y almacenamiento de información del Registro Civil y de datos biométricos:</h3>
                    <p>Autorizo de manera libre y voluntaria a SALVACERO CIA. LTDA a registrar y almacenar mis patrones biométricos para autenticación facial en este canal transaccional, para lo cual me comprometo a seguir los pasos para su registro previstos en el propio canal, así como, a cumplir las instrucciones que SALVACERO CIA. LTDA. determine, cuando necesite acceder y realizar transacciones en la plataforma. Además, declaro conocer con suficiencia en qué consiste esta modalidad de autenticación.</p>

                    <h3>2. Autorización de consulta de información de comportamiento crediticio:</h3>
                    <p>Autorizo de manera libre y voluntaria a SALVACERO CIA. LTDA y a quien sea el futuro cesionario, beneficiario o acreedor del crédito solicitado o del documento o título valor que lo respalde para que obtenga cuantas veces sean necesarias, de cualquier fuente de información, incluidos los burós de crédito, mi información de riesgos crediticios. De igual forma SALVACERO CIA. LTDA o quien sea el futuro cesionario, beneficiario o acreedor del crédito solicitado o del documento o título cambiario que lo respalde queda expresamente autorizado para que pueda transferir o entregar dicha información a los burós de crédito y/o a la Central de Riesgos si fuere pertinente. En este sentido declaro de manera libre y voluntaria que he sido informado previamente por SALVACERO CIA. LTDA. y/o sus comercios afiliados y que cuento con pleno conocimiento de:</p>
                    <ol>
                        <li>La existencia de las bases de datos de información necesaria únicamente para la prestación del servicio de referencias crediticias; su contenido; su finalidad; y, sus potenciales destinatarios;</li>
                        <li>Las posibles consecuencias del uso de la información; y,</li>
                        <li>Los derechos que me asisten y las garantías relacionadas con ellos.</li>
                    </ol>

                    <h3>3. Autorización para actividades de mercadeo:</h3>
                    <p>Autorizo de manera libre y voluntaria a SALVACERO CIA. LTDA a incorporar mis datos en bases de datos propias o de terceros para actividades de mercadeo directo. Conozco que he sido informado que, en caso de revocatoria de la presente autorización, deberé hacerlo de manera expresa a través de la página web de SALVACERO CIA. LTDA.</p>
                </body>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#exampleModalLong').modal('hide')" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModalreq" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Requisitos</h5>
                <button class="btn" type="button" onclick="$('#exampleModalreq').modal('hide')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body">

                <body>
                    <h1>Requisitos para solicitud de préstamo</h1>
                    <ul>
                        <li><strong>Edad:</strong> 21-63 años</li>
                        <li><strong>Documentos de identificación:</strong> Copia de cédula y certificado de votación.</li>
                        <li><strong>Comprobante de residencia:</strong> Planilla de servicios básicos (máximo un mes anterior al vigente).</li>
                        <li><strong>Referencias personales:</strong> 3 referencias (celular) personales y 1 laboral (jefe o compañero).</li>
                        <li><strong>Monto del préstamo:</strong> Mínimo $600 | Máximo $2.500</li>
                        <li><strong>Plazo del préstamo:</strong> Mínimo 6 meses | Máximo 36 meses</li>
                        <li><strong>Tasa de interés:</strong> La tasa de interés de la financiera es del 16.06% anual.</li>
                    </ul>
                </body>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#exampleModalreq').modal('hide')" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Meta Pixel Code -->
<script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1534955887076711');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1534955887076711&ev=PageView&noscript=1" /></noscript>
<!-- End Meta Pixel Code -->


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