<?php

require 'views/header.php';

// var_dump($this->proveedores);
?>

<div class="d-flex flex-column flex-root" id="kt_app_root">
    <!--begin::Page bg image-->
    <style>
        /* body {
            background-image: url('<?php echo constant('URL') ?>public/assets/media/auth/bg7.jpg');
        }

        [data-theme="dark"] body {
            background-image: url('<?php echo constant('URL') ?>public/assets/media/auth/bg7-dark.jpg');
        } */
    </style>
    <!--end::Page bg image-->
    <!--begin::Authentication - Signup Welcome Message -->
    <div class="d-flex flex-column flex-center flex-column-fluid">
        <!--begin::Content-->
        <div class="d-flex flex-column flex-center p-10">
            <!--begin::Wrapper-->
            <div class="card card-flush w-lg-650px py-5">
                <div class="card-body py-15 py-lg-20">

                    <div class="text-center">
                        <img src="https://www.cartimex.com/assets/img/logo200.png" alt="logo">
                    </div>
                    <div class="text-center mb-3">
                        <span class="text-gray-700"></span>
                    </div>
                    <div class="text-center mb-3">
                        <h1 class="fw-bolder fs-2qx text-gray-900 mb-4">Actualiza tus datos</h1>

                    </div>
                    <!--begin::Title-->
                    <div id="kt_modal_new_target_form" action="#">
                        <!--begin::Heading-->
                        <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">Ruc / Cédula</span>
                            </label>
                            <!--end::Label-->
                            <input autocomplete="off" onkeypress="return valideKey(event);" maxlength="13" minlength="10" id="cedula" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                        </div>
                        <div class="text-left">
                            <button onclick="Validar_Cedula()" id="kt_modal_new_target_submit" class="btn btn-success" name="n">
                                <span class="indicator-label">Continuar <i class="bi bi-arrow-right fs-1"></i></span>
                            </button>
                        </div>
                        <div id="INJ">

                        </div>
                        <!-- <div class="d-none">
                            <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Nombres</span>
                                </label>
                                <input autocomplete="off" onkeypress="return soloLetras(event)" onblur="limpia()" id="nombres" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Apellidos</span>
                                </label>
                                <input autocomplete="off" onkeypress="return soloLetras(event)" onblur="limpia()" id="apellidos" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                            </div>
                            <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Email</span>
                                </label>
                                <input autocomplete="off" id="email" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Dirección</span>
                                </label>
                                <input id="direccion" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Teléfono</span>
                                </label>
                                <input onkeypress="return valideKey(event);" id="telefono" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                            </div>
                            <div class="fv-row mb-8 fv-plugins-icon-container">
                                <label class="form-check form-check-inline">
                                    <input id="check_g" class="form-check-input" type="checkbox" name="toc" value="1">
                                    <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">He leído y acepto
                                        <a href="#" class="ms-1 link-primary">Términos y Condiciones</a></span>
                                </label>
                            </div>
                            <div class="fv-row mb-8 fv-plugins-icon-container">
                                <label class="form-check form-check-inline">
                                    <input id="check_pd" class="form-check-input" type="checkbox" name="toc" value="1">
                                    <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">He leído y acepto
                                        <a href="#" onclick="$('#Modal2').modal('show')" class="ms-1 link-primary">Términos y Condiciones</a> de proteccion de datos</span>
                                </label>
                            </div>

                            <div class="text-center">
                                <button onclick="Guardar_datos()" type="submit" id="kt_modal_new_target_submit" class="btn btn-primary" name="n">
                                    <span class="indicator-label">Actualizar</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div> -->
                        <div></div>
                    </div>


                </div>
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Authentication - Signup Welcome Message-->
</div>

<div class="modal fade" id="Modal1" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Términos y Condiciones</strong></h5>
                <button type="button" onclick="$('#Modal1').modal('hide')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body lg" align="justify">
                <p>
                    <!DOCTYPE html>
                    <html>

                    <head>
                        <meta charset="UTF-8">
                        <title>Aviso de Privacidad de Datos Personales</title>
                    </head>

                    <body>
                        <h3 class="fw-bold">Aviso de Privacidad de Datos Personales</h3>

                        <h5>UNO</h5>
                        <p>Según lo establecido en la Ley Orgánica de Protección de Datos Personales, CARTIMEX S.A le garantiza que tratamos y trataremos sus datos personales de una manera transparente y responsable desde que los recogemos, registramos, almacenamos, utilizamos y cedemos garantizando que sus datos serán utilizados de manera adecuada por el personal adecuado.</p>

                        <h5>DOS</h5>
                        <p>La privacidad de los datos personales cubre a los actuales y nuevos clientes (personas naturales y jurídicas), empleados, accionistas y proveedores de CARTIMEX S.A e incluye también a aquellas personas (naturales o jurídicas) que sin ser clientes, soliciten información sobre nuestros productos y/o servicios a través de nuestro sitio web, almacenes u oficina a nivel nacional, call center o cualquier involucrado que se requiera para la ejecución de transacciones necesarias para el producto o servicio prestado.</p>

                        <h5>TRES</h5>
                        <p>Los datos de carácter personal que sean requeridos mediante nuestro sitio web, almacenes u oficina a nivel nacional, call center, serán tratados con confidencialidad, integridad, respeto a los derechos del titular y en total apego a la ley de protección de datos personales por:</p>

                        <table>
                            <tr>
                                <td><strong>EMPRESA:</strong></td>
                                <td>CARTIMEX S. A</td>
                            </tr>
                            <tr>
                                <td><strong>REGISTRO UNICO DE CONTRIBUYENTES:</strong></td>
                                <td>0991400427001</td>
                            </tr>
                            <tr>
                                <td><strong>DIRECCION:</strong></td>
                                <td>Calle Blanca Munoz y Av. Elias Munoz</td>
                            </tr>
                            <tr>
                                <td><strong>TELEFONO:</strong></td>
                                <td>(04) 371-4240</td>
                            </tr>
                            <tr>
                                <td><strong>EMAIL DELEGADO PROTECCION DE DATOS:</strong></td>
                                <td>datospersonales@cartimex.net</td>
                            </tr>
                        </table>
                        <br>
                        <h5>CUATRO</h5>
                        <p>Los datos personales recabados son:</p>
                        <ol>
                            <li>
                                Datos de carácter identificativo:
                                <ol>
                                    <li>Nombres y apellidos</li>
                                    <li>Documento de identidad, número de pasaporte, carné de extranjería</li>
                                    <li>Dirección del domicilio</li>
                                    <li>Teléfono de domicilio</li>
                                    <li>Correo personal</li>
                                    <li>Celular</li>
                                    <li>Lugar de trabajo</li>
                                    <li>Dirección de lugar de trabajo</li>
                                    <li>Teléfono de trabajo</li>
                                </ol>
                            </li>
                            <li>
                                Datos de características personales:
                                <ol>
                                    <li>Fecha de nacimiento</li>
                                    <li>Nacionalidad</li>
                                    <li>Estado civil</li>
                                    <li>Datos personales del cónyuge (de ser aplicable)</li>
                                    <li>Sociedad conyugal (de ser aplicable)</li>
                                    <li>Nivel de educación</li>
                                    <li>Representante legal en el caso de ser menor de edad</li>
                                    <li>Datos de las personas de contacto (de ser aplicable)</li>
                                </ol>
                            </li>
                            <li>
                                Datos económicos-financieros y de seguros:
                                <ol>
                                    <li>Créditos, avales, datos bancarios, comportamiento de créditos del Buró</li>
                                    <li>Información tributaria</li>
                                    <li>Seguros, tarjetas de crédito</li>
                                    <li>Activos, pasivos, patrimonio</li>
                                    <li>Pensión/jubilación</li>
                                    <li>Fuente(s) de ingresos</li>
                                    <li>Ingresos y egresos mensuales (dependiente/independiente)</li>
                                    <li>Actividades económicas entre otras</li>
                                </ol>
                            </li>
                            <li>
                                Datos de carácter social:
                                <ol>
                                    <li>Persona Expuesta Políticamente</li>
                                </ol>
                            </li>
                        </ol>

                        <h5>CINCO</h5>
                        <p>Los datos personales recolectados a través de nuestro sitio web, almacenes u oficina a nivel nacional, Call center, son recabados de los clientes para:</p>
                        <ul style="list-style-type: none;">
                            <li> <strong> 5.1 </strong> El cumplimiento de obligaciones legales</li>
                            <li> <strong> 5.2 </strong> La ejecución de medidas y/o procesos precontractuales o contractuales</li>
                            <li> <strong> 5.3 </strong> Cumplir con la norma “Conozca a su Cliente” de la UAFE</li>
                            <li> <strong> 5.4 </strong> Concesión de créditos</li>
                            <li> <strong> 5.5 </strong> El cumplimiento de requerimientos de entes de control</li>
                            <li> <strong> 5.6 </strong> Verificación de la información proporcionada por el cliente para dar cumplimiento a normas de prevención de lavado de activos y financiamiento de terrorismo, previo al otorgamiento de productos y/o servicios</li>
                            <li> <strong> 5.7 </strong> Procesos de autenticación requeridos para una correcta contratación entre las partes</li>
                            <li> <strong> 5.8 </strong> Envío de mensajes de texto (por cualquier medio) o correspondencia escrita para la correcta comunicación en relación a los productos, servicios y obligaciones relativos a los productos y servicios contratados y a los nuevos productos o servicios que se ofrecerán</li>
                            <li> <strong> 5.9 </strong> Coordinación de entrega o envío de información o comunicaciones relacionadas a los productos o servicios contratados</li>
                            <li> <strong> 5.10 </strong> Brindar soporte a emergencias y facilitar procesos a los organismos de emergencia, seguridad y control (como por ejemplo cuerpo de bomberos, Policía Nacional) respecto a la información frente a un evento de riesgo</li>
                            <li> <strong> 5.11 </strong> Atención del servicio por Call center autorizado(s), mediante procesos de grabación de las llamadas debidamente autorizadas por el cliente, que ingresan a través del referido canal de atención</li>
                        </ul>

                        <h5>SEIS</h5>
                        <p>Los datos personales recolectados a través de nuestro sitio web, almacenes u oficina a nivel nacional, a los proveedores, para:</p>
                        <ul style="list-style-type: none;">
                            <li><strong> 6.1 </strong> Manejar y supervisar los procesos de compra de CARTIMEX S.A con base en la información y requerimientos de las áreas usuarias, respecto de proveedores (personas naturales o jurídicas).</li>
                            <li><strong> 6.2 </strong> Coordinar la emisión de las órdenes de compra utilizadas para proveedores (personas naturales o jurídicas).</li>
                            <li><strong> 6.3 </strong> Coordinar la firma de contratos de compra venta utilizados con proveedores.</li>
                            <li><strong> 6.4 </strong> Mitigar posibles riesgos futuros para CARTIMEX S.A mediante mecanismos de validación y contrastación de información jurídica, societaria (o personal) y de flujo de caja del proveedor.</li>
                            <li><strong> 6.5 </strong> Dar cumplimiento normativo interno y externo en la calificación de proveedores (conocimiento de proveedores).</li>
                            <li><strong> 6.6 </strong> Para el envío de invitaciones a proceso de licitación o adquisición.</li>
                            <li><strong> 6.7 </strong> Realizar gestiones relacionadas con pagos, cobros, facturación, garantías, medios de pago y en general con las labores de carácter administrativo que surjan o se relacionen con los servicios que presta el proveedor.</li>
                            <li><strong> 6.8 </strong> Evaluar la calidad de los servicios o productos del proveedor.</li>
                        </ul>

                        <h5>SIETE</h5>
                        <p>Los datos personales recolectados a través de los canales físicos o electrónicos son también recabados para ofrecer, a quién decida aceptarlos, nuevos productos o servicios, basados en el previo consentimiento del titular, y comprenderá la información que se obtiene de personas naturales y clientes que solicitan/adquieren un producto o un servicio, para efectuar acciones relacionadas con Marketing, desarrollo de negocios, prospección comercial, publicidad, perfilamiento, para:</p>
                        <ul style="list-style-type: none;">
                            <li> <strong> 7.1 </strong> Análisis de información de clientes para el desarrollo o mantenimiento de productos y/o servicios; y, envío de campañas para incentivar su aceptación a través de la página web, correos electrónicos y redes sociales</li>
                            <li> <strong> 7.2 </strong> Generar conocimiento a través de la información de los clientes capturada por la página web y redes sociales, para visualización de indicadores de campañas y el estado de los productos y servicios.</li>
                            <li> <strong> 7.2 </strong> Generar conocimiento a través de la información de los clientes capturada por la página web y redes sociales, para visualización de indicadores de campañas y el estado de los productos y servicios.</li>
                            <li></li>
                        </ul>
                        <h5>OCHO</h5>
                        <p>Para el cumplimiento de las finalidades previamente indicadas del presente documento, sus datos pueden ser tratados por terceros por encargo de CARTIMEX S. A terceros que en primera instancia se encuentra enlistados a continuación de este párrafo.</p>

                        <ul style="list-style-type: none;">
                            <li> <strong> 8.1 </strong> Entidades de Control</li>
                            <li> <strong> 8.2 </strong> Gestión con Call Center</li>
                            <li> <strong> 8.3 </strong> Gestión de canales electrónicos y ventas de Asistencias</li>
                            <li> <strong> 8.4 </strong> Empresa de verificación de datos</li>
                            <li> <strong> 8.5 </strong> Empresa de gestión documental</li>
                            <li> <strong> 8.6 </strong> Empresas emisoras/agentes de tarjetas de crédito</li>
                            <li> <strong> 8.7 </strong> Agencias de medios digitales y Agencia de medios tradicionales</li>
                            <li> <strong> 8.8 </strong> Administración Tributaria, proveedores</li>
                            <li> <strong> 8.9 </strong> Empresas especializadas en seguridad</li>
                            <li> <strong> 8.1 </strong>. Proveedor de herramientas colaborativas en red</li>
                            <li> <strong> 8.1 </strong>. Proveedor de hosting de página web</li>
                        </ul>

                        <p>Dichos tratamientos se llevaran a cabo bajo el estricto cumplimiento de confidencialidad, garantizando mecanismos de protección de información establecidos en la Ley Orgánica de Protección de Datos Personales.</p>

                        <h5>NUEVE</h5>
                        <p>CARTIMEX S. A conservará sus datos personales hasta que usted ejercite el derecho de revocatoria o cancelación conforme a la Ley Orgánica de Protección de Datos Personales (LOPDP), sin embargo, CARTIMEX S. A podrá retener los datos en caso que la cancelación no sea posible, por mandato legal o motivo debidamente fundamentado.</p>


                        <h5>DIEZ</h5>
                        <p>Para garantizar el acceso del titular a sus derechos de acceso a la información, actualización, rectificación, eliminación y oposición; entre otros, pone a disposición el correo electrónico del delegado@........................, a quien deberá de dirigir un correo electrónico, especificando en el “Asunto”, su nombre, número de cédula y que se hace referencia al(los) siguiente(s) derecho(s)</p>

                        <ul>
                            <li> <strong> 10.1 </strong> Acceso a la información: El titular tiene derecho a conocer los datos personales que estén en poder de CARTIMEX S. A</li>
                            <li> <strong> 10.2 </strong> Rectificación y Actualización: El titular tiene derecho a la rectificación y actualización de sus datos personales cuando se encuentren registrados de forma inexacta o incompleta, previa presentación de los justificativos del caso.</li>
                            <li> <strong> 10.3 </strong> Eliminación: En atención al artículo 15 de la LOPDP, se podrá solicitar la eliminación de sus datos personales cuando:</li>
                            <ul>
                                <li> <strong> 10.3.1 </strong> El tratamiento no cumpla con los principios establecidos en la presente ley;</li>
                                <li> <strong> 10.3.2 </strong> El tratamiento no sea necesario o pertinente para el cumplimiento de la finalidad;</li>
                                <li> <strong> 10.3.3 </strong> Los datos personales hayan cumplido con la finalidad para la cual fueron recogidos o tratados;</li>
                                <li> <strong> 10.3.4 </strong> Hubiere vencido el plazo de conservación de los datos personales;</li>
                                <li> <strong> 10.3.5 </strong> El tratamiento afecte derechos fundamentales o libertades individuales;</li>
                                <li> <strong> 10.3.6 </strong> Revoque el consentimiento prestado o señale no haberlo otorgado para uno o varios fines específicos;</li>
                                <li> <strong> 10.3.7 </strong> Exista obligación legal.</li>
                            </ul>
                            <li> <strong> 10.4 </strong> Oposición: En atención a los artículos 16 y 20 de la LOPDP, el titular podrá manifestar la oposición o negarse al tratamiento de sus datos personales.</li>
                            <li> <strong> 10.5 </strong> Portabilidad: El titular podrá recibir, en formato electrónico, los datos personales que haya facilitado.</li>
                            <li> <strong> 10.6 </strong> Suspensión del tratamiento: En atención al artículo 19 de la LOPDP, el titular podrá solicitar la suspensión del tratamiento de tus datos personales por ejemplo:</li>
                            <ul>
                                <li> <strong> 10.6.1 </strong> Mientras se comprueba la impugnación de la exactitud de sus datos.</li>
                                <li><strong>10.6.2</strong> Cuando el tratamiento es ilícito.</li>
                                <li><strong>10.6.3</strong>Cuando CARTIMEX S. A no necesite tratar los datos personales.</li>

                            </ul>
                        </ul>
                        <p>Para más información puede contactar con el Delegado de Protección
                            de Datos a la dirección electrónica
                            datospersonales@cartimex.net</p>

                        <h5> ONCE</h5>
                        <p>CARTIMEX S. A se reserva el derecho de modificar o actualizar este Aviso de Privacidad cuando lo considere necesario, por lo que, cualquier cambio será puesto en conocimiento por medio de nuestros canales de comunicación autorizados.</p>
                    </body>





                    </html>

                </p>
            </div>
            <button type="button" class="btn btn-secondary" onclick="$('#Modal2').modal('hide')" data-dismiss="modal">Aceptar</button>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal2" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Términos y Condiciones</strong></h5>
                <button type="button" onclick="$('#Modal2').modal('hide')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body lg" align="justify">
                <p>
                    <!DOCTYPE html>
                    <html>

                    <head>
                        <meta charset="UTF-8">
                        <title>Aviso de Privacidad de Datos Personales</title>
                    </head>

                    <body>
                        <h3 class="fw-bold">Aviso de Privacidad de Datos Personales</h3>

                        <h5>UNO</h5>
                        <p>Según lo establecido en la Ley Orgánica de Protección de Datos Personales, CARTIMEX S.A le garantiza que tratamos y trataremos sus datos personales de una manera transparente y responsable desde que los recogemos, registramos, almacenamos, utilizamos y cedemos garantizando que sus datos serán utilizados de manera adecuada por el personal adecuado.</p>

                        <h5>DOS</h5>
                        <p>La privacidad de los datos personales cubre a los actuales y nuevos clientes (personas naturales y jurídicas), empleados, accionistas y proveedores de CARTIMEX S.A e incluye también a aquellas personas (naturales o jurídicas) que sin ser clientes, soliciten información sobre nuestros productos y/o servicios a través de nuestro sitio web, almacenes u oficina a nivel nacional, call center o cualquier involucrado que se requiera para la ejecución de transacciones necesarias para el producto o servicio prestado.</p>

                        <h5>TRES</h5>
                        <p>Los datos de carácter personal que sean requeridos mediante nuestro sitio web, almacenes u oficina a nivel nacional, call center, serán tratados con confidencialidad, integridad, respeto a los derechos del titular y en total apego a la ley de protección de datos personales por:</p>

                        <table>
                            <tr>
                                <td><strong>EMPRESA:</strong></td>
                                <td>CARTIMEX S. A</td>
                            </tr>
                            <tr>
                                <td><strong>REGISTRO UNICO DE CONTRIBUYENTES:</strong></td>
                                <td>0991400427001</td>
                            </tr>
                            <tr>
                                <td><strong>DIRECCION:</strong></td>
                                <td>Calle Blanca Munoz y Av. Elias Munoz</td>
                            </tr>
                            <tr>
                                <td><strong>TELEFONO:</strong></td>
                                <td>(04) 371-4240</td>
                            </tr>
                            <tr>
                                <td><strong>EMAIL DELEGADO PROTECCION DE DATOS:</strong></td>
                                <td>datospersonales@cartimex.net</td>
                            </tr>
                        </table>
                        <br>
                        <h5>CUATRO</h5>
                        <p>Los datos personales recabados son:</p>
                        <ol>
                            <li>
                                Datos de carácter identificativo:
                                <ol>
                                    <li>Nombres y apellidos</li>
                                    <li>Documento de identidad, número de pasaporte, carné de extranjería</li>
                                    <li>Dirección del domicilio</li>
                                    <li>Teléfono de domicilio</li>
                                    <li>Correo personal</li>
                                    <li>Celular</li>
                                    <li>Lugar de trabajo</li>
                                    <li>Dirección de lugar de trabajo</li>
                                    <li>Teléfono de trabajo</li>
                                </ol>
                            </li>
                            <li>
                                Datos de características personales:
                                <ol>
                                    <li>Fecha de nacimiento</li>
                                    <li>Nacionalidad</li>
                                    <li>Estado civil</li>
                                    <li>Datos personales del cónyuge (de ser aplicable)</li>
                                    <li>Sociedad conyugal (de ser aplicable)</li>
                                    <li>Nivel de educación</li>
                                    <li>Representante legal en el caso de ser menor de edad</li>
                                    <li>Datos de las personas de contacto (de ser aplicable)</li>
                                </ol>
                            </li>
                            <li>
                                Datos económicos-financieros y de seguros:
                                <ol>
                                    <li>Créditos, avales, datos bancarios, comportamiento de créditos del Buró</li>
                                    <li>Información tributaria</li>
                                    <li>Seguros, tarjetas de crédito</li>
                                    <li>Activos, pasivos, patrimonio</li>
                                    <li>Pensión/jubilación</li>
                                    <li>Fuente(s) de ingresos</li>
                                    <li>Ingresos y egresos mensuales (dependiente/independiente)</li>
                                    <li>Actividades económicas entre otras</li>
                                </ol>
                            </li>
                            <li>
                                Datos de carácter social:
                                <ol>
                                    <li>Persona Expuesta Políticamente</li>
                                </ol>
                            </li>
                        </ol>

                        <h5>CINCO</h5>
                        <p>Los datos personales recolectados a través de nuestro sitio web, almacenes u oficina a nivel nacional, Call center, son recabados de los clientes para:</p>
                        <ul style="list-style-type: none;">
                            <li> <strong> 5.1 </strong> El cumplimiento de obligaciones legales</li>
                            <li> <strong> 5.2 </strong> La ejecución de medidas y/o procesos precontractuales o contractuales</li>
                            <li> <strong> 5.3 </strong> Cumplir con la norma “Conozca a su Cliente” de la UAFE</li>
                            <li> <strong> 5.4 </strong> Concesión de créditos</li>
                            <li> <strong> 5.5 </strong> El cumplimiento de requerimientos de entes de control</li>
                            <li> <strong> 5.6 </strong> Verificación de la información proporcionada por el cliente para dar cumplimiento a normas de prevención de lavado de activos y financiamiento de terrorismo, previo al otorgamiento de productos y/o servicios</li>
                            <li> <strong> 5.7 </strong> Procesos de autenticación requeridos para una correcta contratación entre las partes</li>
                            <li> <strong> 5.8 </strong> Envío de mensajes de texto (por cualquier medio) o correspondencia escrita para la correcta comunicación en relación a los productos, servicios y obligaciones relativos a los productos y servicios contratados y a los nuevos productos o servicios que se ofrecerán</li>
                            <li> <strong> 5.9 </strong> Coordinación de entrega o envío de información o comunicaciones relacionadas a los productos o servicios contratados</li>
                            <li> <strong> 5.10 </strong> Brindar soporte a emergencias y facilitar procesos a los organismos de emergencia, seguridad y control (como por ejemplo cuerpo de bomberos, Policía Nacional) respecto a la información frente a un evento de riesgo</li>
                            <li> <strong> 5.11 </strong> Atención del servicio por Call center autorizado(s), mediante procesos de grabación de las llamadas debidamente autorizadas por el cliente, que ingresan a través del referido canal de atención</li>
                        </ul>

                        <h5>SEIS</h5>
                        <p>Los datos personales recolectados a través de nuestro sitio web, almacenes u oficina a nivel nacional, a los proveedores, para:</p>
                        <ul style="list-style-type: none;">
                            <li><strong> 6.1 </strong> Manejar y supervisar los procesos de compra de CARTIMEX S.A con base en la información y requerimientos de las áreas usuarias, respecto de proveedores (personas naturales o jurídicas).</li>
                            <li><strong> 6.2 </strong> Coordinar la emisión de las órdenes de compra utilizadas para proveedores (personas naturales o jurídicas).</li>
                            <li><strong> 6.3 </strong> Coordinar la firma de contratos de compra venta utilizados con proveedores.</li>
                            <li><strong> 6.4 </strong> Mitigar posibles riesgos futuros para CARTIMEX S.A mediante mecanismos de validación y contrastación de información jurídica, societaria (o personal) y de flujo de caja del proveedor.</li>
                            <li><strong> 6.5 </strong> Dar cumplimiento normativo interno y externo en la calificación de proveedores (conocimiento de proveedores).</li>
                            <li><strong> 6.6 </strong> Para el envío de invitaciones a proceso de licitación o adquisición.</li>
                            <li><strong> 6.7 </strong> Realizar gestiones relacionadas con pagos, cobros, facturación, garantías, medios de pago y en general con las labores de carácter administrativo que surjan o se relacionen con los servicios que presta el proveedor.</li>
                            <li><strong> 6.8 </strong> Evaluar la calidad de los servicios o productos del proveedor.</li>
                        </ul>

                        <h5>SIETE</h5>
                        <p>Los datos personales recolectados a través de los canales físicos o electrónicos son también recabados para ofrecer, a quién decida aceptarlos, nuevos productos o servicios, basados en el previo consentimiento del titular, y comprenderá la información que se obtiene de personas naturales y clientes que solicitan/adquieren un producto o un servicio, para efectuar acciones relacionadas con Marketing, desarrollo de negocios, prospección comercial, publicidad, perfilamiento, para:</p>
                        <ul style="list-style-type: none;">
                            <li> <strong> 7.1 </strong> Análisis de información de clientes para el desarrollo o mantenimiento de productos y/o servicios; y, envío de campañas para incentivar su aceptación a través de la página web, correos electrónicos y redes sociales</li>
                            <li> <strong> 7.2 </strong> Generar conocimiento a través de la información de los clientes capturada por la página web y redes sociales, para visualización de indicadores de campañas y el estado de los productos y servicios.</li>
                            <li> <strong> 7.2 </strong> Generar conocimiento a través de la información de los clientes capturada por la página web y redes sociales, para visualización de indicadores de campañas y el estado de los productos y servicios.</li>
                            <li></li>
                        </ul>
                        <h5>OCHO</h5>
                        <p>Para el cumplimiento de las finalidades previamente indicadas del presente documento, sus datos pueden ser tratados por terceros por encargo de CARTIMEX S. A terceros que en primera instancia se encuentra enlistados a continuación de este párrafo.</p>

                        <ul style="list-style-type: none;">
                            <li> <strong> 8.1 </strong> Entidades de Control</li>
                            <li> <strong> 8.2 </strong> Gestión con Call Center</li>
                            <li> <strong> 8.3 </strong> Gestión de canales electrónicos y ventas de Asistencias</li>
                            <li> <strong> 8.4 </strong> Empresa de verificación de datos</li>
                            <li> <strong> 8.5 </strong> Empresa de gestión documental</li>
                            <li> <strong> 8.6 </strong> Empresas emisoras/agentes de tarjetas de crédito</li>
                            <li> <strong> 8.7 </strong> Agencias de medios digitales y Agencia de medios tradicionales</li>
                            <li> <strong> 8.8 </strong> Administración Tributaria, proveedores</li>
                            <li> <strong> 8.9 </strong> Empresas especializadas en seguridad</li>
                            <li> <strong> 8.1 </strong>. Proveedor de herramientas colaborativas en red</li>
                            <li> <strong> 8.1 </strong>. Proveedor de hosting de página web</li>
                        </ul>

                        <p>Dichos tratamientos se llevaran a cabo bajo el estricto cumplimiento de confidencialidad, garantizando mecanismos de protección de información establecidos en la Ley Orgánica de Protección de Datos Personales.</p>

                        <h5>NUEVE</h5>
                        <p>CARTIMEX S. A conservará sus datos personales hasta que usted ejercite el derecho de revocatoria o cancelación conforme a la Ley Orgánica de Protección de Datos Personales (LOPDP), sin embargo, CARTIMEX S. A podrá retener los datos en caso que la cancelación no sea posible, por mandato legal o motivo debidamente fundamentado.</p>


                        <h5>DIEZ</h5>
                        <p>Para garantizar el acceso del titular a sus derechos de acceso a la información, actualización, rectificación, eliminación y oposición; entre otros, pone a disposición el correo electrónico del delegado@........................, a quien deberá de dirigir un correo electrónico, especificando en el “Asunto”, su nombre, número de cédula y que se hace referencia al(los) siguiente(s) derecho(s)</p>

                        <ul>
                            <li> <strong> 10.1 </strong> Acceso a la información: El titular tiene derecho a conocer los datos personales que estén en poder de CARTIMEX S. A</li>
                            <li> <strong> 10.2 </strong> Rectificación y Actualización: El titular tiene derecho a la rectificación y actualización de sus datos personales cuando se encuentren registrados de forma inexacta o incompleta, previa presentación de los justificativos del caso.</li>
                            <li> <strong> 10.3 </strong> Eliminación: En atención al artículo 15 de la LOPDP, se podrá solicitar la eliminación de sus datos personales cuando:</li>
                            <ul>
                                <li> <strong> 10.3.1 </strong> El tratamiento no cumpla con los principios establecidos en la presente ley;</li>
                                <li> <strong> 10.3.2 </strong> El tratamiento no sea necesario o pertinente para el cumplimiento de la finalidad;</li>
                                <li> <strong> 10.3.3 </strong> Los datos personales hayan cumplido con la finalidad para la cual fueron recogidos o tratados;</li>
                                <li> <strong> 10.3.4 </strong> Hubiere vencido el plazo de conservación de los datos personales;</li>
                                <li> <strong> 10.3.5 </strong> El tratamiento afecte derechos fundamentales o libertades individuales;</li>
                                <li> <strong> 10.3.6 </strong> Revoque el consentimiento prestado o señale no haberlo otorgado para uno o varios fines específicos;</li>
                                <li> <strong> 10.3.7 </strong> Exista obligación legal.</li>
                            </ul>
                            <li> <strong> 10.4 </strong> Oposición: En atención a los artículos 16 y 20 de la LOPDP, el titular podrá manifestar la oposición o negarse al tratamiento de sus datos personales.</li>
                            <li> <strong> 10.5 </strong> Portabilidad: El titular podrá recibir, en formato electrónico, los datos personales que haya facilitado.</li>
                            <li> <strong> 10.6 </strong> Suspensión del tratamiento: En atención al artículo 19 de la LOPDP, el titular podrá solicitar la suspensión del tratamiento de tus datos personales por ejemplo:</li>
                            <ul>
                                <li> <strong> 10.6.1 </strong> Mientras se comprueba la impugnación de la exactitud de sus datos.</li>
                                <li><strong>10.6.2</strong> Cuando el tratamiento es ilícito.</li>
                                <li><strong>10.6.3</strong>Cuando CARTIMEX S. A no necesite tratar los datos personales.</li>

                            </ul>
                        </ul>
                        <p>Para más información puede contactar con el Delegado de Protección
                            de Datos a la dirección electrónica
                            datospersonales@cartimex.net</p>

                        <h5> ONCE</h5>
                        <p>CARTIMEX S. A se reserva el derecho de modificar o actualizar este Aviso de Privacidad cuando lo considere necesario, por lo que, cualquier cambio será puesto en conocimiento por medio de nuestros canales de comunicación autorizados.</p>
                    </body>





                    </html>

                </p>
            </div>
            <button type="button" class="btn btn-secondary" onclick="$('#Modal2').modal('hide')" data-dismiss="modal">Aceptar</button>
        </div>
    </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<?php require 'views/footer.php'; ?>
<?php require 'funciones/guardar_js.php'; ?>
<script>
    function valideKey(evt) {
        // code is the decimal ASCII representation of the pressed key.
        var code = (evt.which) ? evt.which : evt.keyCode;

        if (code == 8) { // backspace.
            return true;
        } else if (code >= 48 && code <= 57) { // is a number.
            return true;
        } else if (code == 45) { // is a number.
            return true;
        } else { // other keys.
            return false;
        }
    }

    function soloLetras(e) {
        key = e.keyCode || e.which;
        tecla = String.fromCharCode(key).toLowerCase();
        letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
        especiales = [8, 37, 39, 46];

        tecla_especial = false
        for (var i in especiales) {
            if (key == especiales[i]) {
                tecla_especial = true;
                break;
            }
        }

        if (letras.indexOf(tecla) == -1 && !tecla_especial)
            return false;
    }

    function limpia() {
        var val = document.getElementById("miInput").value;
        var tam = val.length;
        for (i = 0; i < tam; i++) {
            if (!isNaN(val[i]))
                document.getElementById("miInput").value = '';
        }
    }

    var inputElement = document.getElementById("cedula");
    inputElement.focus();

    function openmodal2() {
        $("#Modal2").modal("show");
    }

    function openmodal1() {
        $("#Modal1").modal("show");
    }

    function cancelar() {
        $("#INJ").empty();
        $("#kt_modal_new_target_submit").show();

    }
    // var inputElement = document.getElementById("cedula");

    // inputElement.addEventListener("keydown", function(event) {
    //     if (event.keyCode === 13) {
    //         event.preventDefault(); // Prevent form submission if inside a form element
    //         executeFunction();
    //     }
    // });

    // function executeFunction() {
    //     // Your function logic here
    //     Validar_Cedula();
    // }
</script>