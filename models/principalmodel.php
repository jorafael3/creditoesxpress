<?php

// require_once "models/logmodel.php";
require('public/fpdf/fpdf.php');

class principalmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //*** CELULAR */

    function Validar_Celular($param)
    {
        // $this->Obtener_Datos_Credito($param);
        try {
            $celular = trim($param["celular"]);
            $terminos = $param["terminos"];
            $ip = $this->getRealIP();
            $dispositivo = $_SERVER['HTTP_USER_AGENT'];

            $SI_CONSULTO = $this->Validar_si_consulto_credito($param);
            // $SI_CONSULTO = 1;

            if ($SI_CONSULTO == 1) {
                $this->Anular_Codigos($param);
                $codigo = $this->Api_Sms($celular);
                if ($codigo[0] == 1) {
                    $query = $this->db->connect_dobra()->prepare('INSERT INTO solo_telefonos 
                        (
                            numero, 
                            codigo, 
                            terminos, 
                            ip, 
                            dispositivo
                        ) 
                        VALUES
                        (
                            :numero, 
                            :codigo, 
                            :terminos,
                            :ip, 
                            :dispositivo 
                        );
                    ');
                    $query->bindParam(":numero", $celular, PDO::PARAM_STR);
                    $query->bindParam(":codigo", $codigo[1], PDO::PARAM_STR);
                    $query->bindParam(":terminos", $terminos, PDO::PARAM_STR);
                    $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                    $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);

                    if ($query->execute()) {
                        $result = $query->fetchAll(PDO::FETCH_ASSOC);
                        $cel = base64_encode($celular);
                        $codigo_temporal = "0000";
                        // $codigo_temporal = $this->Cargar_Codigo_Temporal($param);
                        $html = '
                            <div class="fv-row mb-10 text-center">
                                <label class="form-label fw-bold fs-2">Ingresa el código enviado a tu celular</label><br>
                                <label class="text-muted fw-bold fs-6">Verifica el número celular</label>
                                <input type="hidden" id="CEL_1" value="' . $cel . '">
                                <input type="hidden" id="CEL_1" value="' . $codigo_temporal . '">
                            </div>
                            <div class="row justify-content-center mb-5">
                                        <div class="col-md-12">
                                            <div class="row justify-content-center">
                                                <div class="col-auto">
                                                    <input type="text" maxlength="1" class="form-control code-input" />
                                                </div>
                                                <div class="col-auto">
                                                    <input type="text" maxlength="1" class="form-control code-input" />
                                                </div>
                                                <div class="col-auto">
                                                    <input type="text" maxlength="1" class="form-control code-input" />
                                                </div>
                                                <div class="col-auto">
                                                    <input type="text" maxlength="1" class="form-control code-input" />
                                                </div>
                                            </div>
                                        </div>
                            </div>';
                        echo json_encode([1, $celular, $html]);
                        exit();
                    } else {
                        $err = $query->errorInfo();
                        echo json_encode([0, "Error al generar solicitud, intentelo de nuevo", "error", $err]);
                        exit();
                    }
                }
            } else {
                echo json_encode([0, "Error al generar código, por favor intentelo en un momento", "error"]);
                exit();
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Api_Sms($celular)
    {

        try {

            $url = 'https://api.smsplus.net.ec/sms/client/api.php/sendMessage';
            // $url = 'http://186.3.87.6/sms/ads/api.php/getMessage';

            $codigo = rand(1000, 9999);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $username = '999990165';
            $password = 'bt3QVPyQ6L8e97hs';

            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
            ];
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $phoneNumber = $celular;
            $messageId = "144561";
            // $transactionId = 141569;
            $dataVariable = [$codigo];
            $transactionId = uniqid();

            $dataWs = [
                'phoneNumber' => $phoneNumber,
                'messageId' => $messageId,
                'transactionId' => $transactionId,
                'dataVariable' => $dataVariable,
            ];

            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataWs));

            // Set Basic Authentication
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

            // for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            // $resp = '{"codError":100,"desError":"OK","transactionId":"240305230212179130"}';

            $responseData = json_decode($resp, true);

            // Verificar si la solicitud fue exitosa
            // Verificar el código de error y mostrar la respuesta
            if (isset($responseData['codError'])) {
                if ($responseData['codError'] == 100) {
                    // echo "Mensaje enviado correctamente. Transaction ID: ";
                    // echo json_encode("");
                    return [1, $codigo, $responseData];
                } else {
                    return [0, 0];
                    // echo "Error: " . $responseData['desError'];
                }
            } else {
                return [0, 0];
                // echo "Error desconocido al enviar el mensaje.";
            }
        } catch (Exception $e) {

            $e = $e->getMessage();
            return [0, 0];
        }
        // echo json_encode($resp);
        // exit();
    }

    function Cargar_Codigo_Temporal($param)
    {
        try {
            $celular = trim($param["celular"]);

            $query = $this->db->connect_dobra()->prepare('SELECT * FROM solo_telefonos
                Where numero = :numero and estado = 1');
            $query->bindParam(":numero", $celular, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return ($result[0]["codigo"]);
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, "Error al generar solicitud, intentelo de nuevo", "error", $err]);
                exit();
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    //************************************************* */

    function Validar_si_consulto_credito($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $celular = trim($param["celular"]);
            $query = $this->db->connect_dobra()->prepare('SELECT * FROM creditos_solicitados
            WHERE numero = :numero
            order by fecha_creado desc
            limit 1');
            $query->bindParam(":numero", $celular, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) == 0) {
                    return 1;
                } else {
                    $currentDateTime = new DateTime();
                    $FECHA = $result[0]["fecha_creado"];
                    $formattedDateTime = new DateTime($FECHA);
                    $difference = $currentDateTime->diff($formattedDateTime);
                    $daysDifference = $difference->days;

                    $CREDITO = $result[0]["credito_aprobado"];
                    $CEDULA = $result[0]["cedula"];
                    $CORREO = $result[0]["correo"];
                    $codigo_dactilar = $result[0]["codigo_dactilar"];
                    $fecha_creado = $result[0]["fecha_creado"];
                    $nombre_cliente = $result[0]["nombre_cliente"];
                    $localidad = $result[0]["localidad"];
                    $CREDITO_APROBADO = $result[0]["credito_aprobado"];
                    $fecha_nacimiento = $result[0]["fecha_nacimiento"];

                    $query_cant_con = $this->db->connect_dobra()->prepare("INSERT INTO cantidad_consultas
                    (
                        numero,
                        cantidad
                    )VALUES
                    (
                        :numero,
                        1
                    )");
                    $query_cant_con->bindParam(":numero", $celular, PDO::PARAM_STR);
                    $query_cant_con->execute();

                    if ($daysDifference == 0) {
                        $VAL_CEDULA = array(
                            "CEDULA" => $CEDULA,
                            "FECHA_NACIM" => $fecha_nacimiento,
                            "INDIVIDUAL_DACTILAR" => $codigo_dactilar,
                            "CANT_DOM" => $localidad,
                            "NOMBRES" => $nombre_cliente,
                        );

                        $param = array(
                            "celular" => $celular,
                            "email" => $CORREO,
                            "cedula" => $CEDULA,
                        );
                        $VAL_CREDITO = $this->Obtener_Datos_Credito($VAL_CEDULA, $param, 0);
                        // echo json_encode([$VAL_CREDITO, $VAL_CEDULA]);
                        // exit();
                        if ($VAL_CREDITO[0] == 1) {
                            $this->Guardar_Datos_Banco($VAL_CEDULA, $VAL_CREDITO, $param, 0);
                        } else if ($VAL_CREDITO[0] == 0) {
                            $this->ELiminar_Cedulas_No_existen_2($param);
                            echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", $VAL_CREDITO]);
                            exit();
                        }
                    } else {
                        // $html = '
                        // <div class="alert alert-primary" role="alert">
                        //     <div class="p-3">
                        //         <h4 class="text-dark">Este número ya ha hecho una consulta anterior</h4>
                        //         <h4 class="text-dark">se registro con los siguientes datos:</h4>
                        //         <hr>
                        //         <h4 class="text-dark">Fecha: ' . $fecha_creado . '</h4>
                        //         <h4 class="text-dark">Cédula: ' . $CEDULA . '</h4>
                        //         <h4 class="text-dark">Correo: ' . $CORREO . '</h4>
                        //     </div> 
                        // </div> 
                        // <div class="text-center mt-3">
                        //     <h1 class="text-primary">FELICITACIONES</h1>
                        //     <h3>Usted esta apto para acceder a un credito con nosotros</h3>
                        //     <h3>un asesor se contactara con usted en breve</h3>
                        // </div>';
                        $link = constant("URL") . "/public/img/SV24 - Mensajes LC_Proceso.png";
                        // if ($CREDITO == 1) {
                        //     $html = '
                        //     <div class="text-center mt-3">
                        //         <img style="width: 100%;" src="' . $link . '" alt="">
                        //     </div>';
                        // } else {
                        //     $html = '  
                        //     <div class="text-center mt-3">
                        //         <h1 class="text-danger">Usted no cumple con todos los requisitos necesarios para acceder a un credito</h1>
                        //         <h3>un asesor se contactara con usted en breve</h3>
                        //         <h3></h3>
                        //     </div>';
                        // }
                        // $html = '
                        // <div class="text-center mt-3">
                        //     <img style="width: 100%;" src="' . $link . '" alt="">
                        // </div>';
                        if ($CREDITO_APROBADO > 0) {
                            $html = '
                            <div class="text-center mt-3">
                                <h1 style="font-size:60px" class="text-primary">Felicidades! </h1>
                                <h2>Tienes credito disponible</h2>
                                <img style="width: 100%;" src="' . $link . '" alt="">
                            </div>';
                        } else {
                            $html = '
                            <div class="text-center">
                                <h1 class="text-danger">Lamentablemente el perfil con la cédula entregada no aplica para el crédito, no cumple con las políticas del banco.</h1>
                                <h3><i class="bi bi-tv fs-1"></i> Mire el siguiente video ➡️ </h3>
                                <a class="fs-3" href="https://youtu.be/EMaHXoCefic">https://youtu.be/EMaHXoCefic ��</a>
                                <h3 class="mt-3">Le invitamos a llenar la siguiente encuesta ➡️ </h3>
                                <a class="fs-3" href="https://forms.gle/s3GwuwoViF4Z2Jpt6">https://forms.gle/s3GwuwoViF4Z2Jpt6</a>
                                <h3></h3>
                            </div>';
                        }

                        echo json_encode([2, $result, $result, $html]);
                        exit();
                    }
                }
            } else {
                return 0;
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    //*** PONE EN 0 LOS CODIGOS ANTERIORES PARA PODER VALIDAR EL NUEVO
    function Anular_Codigos($param)
    {
        try {
            $celular = trim($param["celular"]);
            $query = $this->db->connect_dobra()->prepare('UPDATE solo_telefonos
            SET
                estado = 0
            WHERE numero = :numero
            ');
            $query->bindParam(":numero", $celular, PDO::PARAM_STR);
            if ($query->execute()) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Validar_Codigo($CODIGO_JUNTO, $celular)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT ID from solo_telefonos
            where numero = :numero and codigo = :codigo and estado = 1');
            $query->bindParam(":numero", $celular, PDO::PARAM_STR);
            $query->bindParam(":codigo", $CODIGO_JUNTO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $cel = base64_encode($celular);
                $html = '
                <div class="fv-row mb-10">
                    <label class="form-label d-flex align-items-center">
                            <span class="required fw-bold fs-2">Cédula</span>
                        </label>
                        <input type="hidden" id="CEL" value="' . $cel . '">
                        <input placeholder="xxxxxxxxxx" id="CEDULA" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                    </div>
                    <div class="fv-row mb-10">
                        <label class="form-label d-flex align-items-center">
                            <span class="fw-bold fs-2">Número de teléfono</span><br>
                        </label>
                        <h6 class="text-muted">Ten en cuenta que este número se asociará a la cédula que ingrese para proximas consultas</h6>
                        <input readonly id="" type="text" class="form-control form-control-solid" name="input1" value="' . $celular . '" />
                    </div>
                    <div class="fv-row mb-10">
                        <label class="form-label d-flex align-items-center">
                            <span class="fw-bold fs-2">Correo </span>
                            <span class="text-muted fw-bold fs-5">(opcional)</span>
                        </label>
                        <input placeholder="xxxxxxx@mail.com" id="CORREO" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                    </div>
                ';
                if (count($result) > 0) {
                    echo json_encode([1, $celular, $html, $result]);
                    exit();
                } else {
                    echo json_encode([0, "El codigo ingresado no es el correcto", "error"]);
                    exit();
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, "Error al generar solicitud, intentelo de nuevo", "error", $err]);
                exit();
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    //********** CEDULA *********/

    function Validar_Cedula($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $link = constant("URL") . "/public/img/SV24 - Mensajes LC_Proceso.png";
            $RUTA_ARCHIVO = trim($param["cedula"]) . "_" . date("YmdHis") . ".pdf";

            $VAL_CONSULTA = $this->Validar_Cedula_Ya_Consulto($param);
            // echo json_encode([$VAL_CONSULTA]);
            // exit();
            if ($VAL_CONSULTA[0] == 1) {
                //* INSERTA SOLO CEDULA EN TABLA
                $VAL_CEDULA_ = $this->Validar_si_cedula_existe($param);
                // echo json_encode($VAL_CEDULA_);
                // exit();
                if ($VAL_CEDULA_ == 0) {
                } else {
                    // $VAL_CEDULA = $this->consulta_api_cedula();
                    $VAL_CEDULA = $this->Obtener_Datos_Cedula($param);
                    // echo json_encode($VAL_CEDULA);
                    // exit();
                    if ($VAL_CEDULA[0] == 1) {
                        $VAL_CREDITO = $this->Obtener_Datos_Credito($VAL_CEDULA[1][0], $param, 1);
                        // echo json_encode([$VAL_CREDITO, $VAL_CEDULA]);
                        // exit();
                        if ($VAL_CREDITO[0] == 1) {
                            $this->Guardar_Datos_Banco($VAL_CEDULA, $VAL_CREDITO, $param, 1);
                        } else if ($VAL_CREDITO[0] == 0) {
                            $this->ELiminar_Cedulas_No_existen_2($param);
                            echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", $VAL_CREDITO]);
                            exit();
                        } else if ($VAL_CREDITO[0] == 2) {
                            $this->ELiminar_Cedulas_No_existen_2($param);
                            echo json_encode([0, "No se pudo realizar la verificacion", "Este número de cédula ha excedido la cantidad de consultas diarias, intentelo luego", $VAL_CREDITO]);
                            exit();
                        } else if ($VAL_CREDITO[0] == 3) {
                            $this->ELiminar_Cedulas_No_existen_2($param);
                            echo json_encode([0, "No se pudo realizar la verificacion", "Por el momento nose puede realizar la operacion", $VAL_CREDITO]);
                            exit();
                        }
                    } else if ($VAL_CEDULA[0] == 0) {
                        $this->ELiminar_Cedulas_No_existen($param);
                        echo json_encode([0, "No se pudo realizar la verificacion", "Asegureseo que la cédula ingresada sea la correcta", "error", $VAL_CEDULA]);
                        exit();
                    } else {

                        $_inci = array(
                            "ERROR_TYPE" => "ENCRIP",
                            "ERROR_CODE" => "",
                            "ERROR_TEXT" => "ERROR AL OBTERN CEDULA ENCRIPTADA",
                        );
                        $INC = $this->INCIDENCIAS($_inci);
                        $this->ELiminar_Cedulas_No_existen_2($param);
                        echo json_encode([0, "No se pudo realizar la verificacion", "intentelo en un momento", "error", $VAL_CEDULA]);
                        exit();

                        // $cedula = trim($param["cedula"]);
                        // $email = trim($param["email"]);
                        // $celular = base64_decode(trim($param["celular"]));
                        // $ip = $this->getRealIP();
                        // $dispositivo = $_SERVER['HTTP_USER_AGENT'];

                        // $query = $this->db->connect_dobra()->prepare('UPDATE creditos_solicitados
                        // SET
                        //     numero = :numero, 
                        //     correo = :correo,
                        //     ip = :ip,
                        //     dispositivo = :dispositivo,
                        //     estado = 2
                        // WHERE cedula = :cedula
                        // ');
                        // $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
                        // $query->bindParam(":numero", $celular, PDO::PARAM_STR);
                        // $query->bindParam(":correo", $email, PDO::PARAM_STR);
                        // $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                        // $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);
                        // if ($query->execute()) {

                        //     $query_cant_con = $this->db->connect_dobra()->prepare("INSERT INTO cantidad_consultas
                        //     (
                        //         numero,
                        //         cantidad
                        //     )VALUES
                        //     (
                        //         :numero,
                        //         1
                        //     )");
                        //     $query_cant_con->bindParam(":numero", $celular, PDO::PARAM_STR);
                        //     $query_cant_con->execute();
                        //     $html = '
                        //         <div class="text-center mt-3">
                        //             <img style="width: 100%;" src="' . $link . '" alt="">
                        //         </div>';
                        //     // $this->Generar_Documento($RUTA_ARCHIVO);
                        //     echo json_encode([1, [], [], $html]);
                        //     exit();
                        // }
                    }
                }
            } else {
                echo json_encode([0, $VAL_CONSULTA[1], "Asegurese que la cédula ingresada sea la correcta", "error"]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, "No se pudo realizar la verificaciolln", "Intentelo de nuevo", $e]);
            exit();
        }
    }

    function Validar_Cedula_Ya_Consulto($param)
    {
        try {
            $cedula = trim($param["cedula"]);
            $celular = base64_decode(trim($param["celular"]));
            $query = $this->db->connect_dobra()->prepare('SELECT * from
                creditos_solicitados
                WHERE cedula = :cedula
                and estado = 1
                order by fecha_creado desc
                limit 1
            ');
            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    if ($result[0]["numero"] != $celular) {
                        return [0, "Esta cédula esta asociado a otro número que ya realizo una consulta"];
                    } else {
                        return [1, ""];
                    }
                } else {
                    return [1, ""];
                }
                // $telf = $result[0][""];
                // return $result;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Validar_si_cedula_existe($param)
    {
        try {
            $cedula = trim($param["cedula"]);
            $query = $this->db->connect_dobra()->prepare('SELECT * from
                creditos_solicitados
                WHERE cedula = :cedula
                and estado = 1
            ');
            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    return [1, $result];
                } else {
                    $query = $this->db->connect_dobra()->prepare('INSERT INTO 
                    creditos_solicitados
                    (
                        cedula
                    ) 
                    VALUES
                    (
                        :cedula
                    );
                    ');
                    $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
                    if ($query->execute()) {
                        return [0];
                    } else {
                        return [0];
                    }
                }
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function consulta_api_cedula($cedula_encr)
    {
        // $cedula_encr = "yt3TIGS4cvQQt3+q6iQ2InVubHr4hm4V7cxn1V3jFC0=";
        $old_error_reporting = error_reporting();
        // Desactivar los mensajes de advertencia
        error_reporting($old_error_reporting & ~E_WARNING);
        // Realizar la solicitud
        // Restaurar el nivel de informe de errores original

        try {
            $url = 'https://consultadatosapi.azurewebsites.net/api/GetDataBasica?code=Hp37f_WfqrsgpDyl8rP9zM1y-JRSJTMB0p8xjQDSEDszAzFu7yW3XA==&id=' . $cedula_encr . '&emp=SALVACERO&subp=DATOSCEDULA';
            // $url = 'https://apidatoscedula20240216081841.azurewebsites.net/api/GetData?code=FXs4nBycLJmBacJWuk_olF_7thXybtYRFDDyaRGKbnphAzFuQulUlA==&id=' . $cedula_encr . '&emp=SALVACERO&subp=DATOSCEDULA';
            try {
                // Realizar la solicitud
                $response = file_get_contents($url);
                error_reporting($old_error_reporting);
                if ($response === false) {
                    // $data = json_decode($response);
                    return [2, []];
                } else {
                    $data = json_decode($response);
                    if (isset($data->error)) {
                        return [0, $data->error, $cedula_encr];
                    } else {
                        if (count(($data->DATOS)) > 0) {
                            return [1, $data->DATOS];
                        } else {
                            return [0, $data->DATOS];
                        }
                    }
                }
            } catch (Exception $e) {
                // Capturar y manejar la excepción
                echo json_encode([0, "ssssss"]);
                exit();
            }
        } catch (Exception $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Obtener_Datos_Cedula($param)
    {
        try {
            set_time_limit(30);
            $start_time = microtime(true);

            // sleep(4);
            $cedula = trim($param["cedula"]);
            $arr = "";
            while (true) {
                $current_time = microtime(true);
                $elapsed_time = $current_time - $start_time;
                // Verificar si el tiempo transcurrido excede el límite de tiempo máximo permitido (por ejemplo, 120 segundos)
                if (round($elapsed_time, 0) >= 30) {
                    return [2, "La consulta excedió el tiempo máximo permitido"];
                }
                // echo json_encode("Tiempo transcurrido: " . $elapsed_time . " segundos\n");

                $query = $this->db->connect_dobra()->prepare("SELECT 
                cedula,
                cedula_encr
                FROM creditos_solicitados
                WHERE cedula = :cedula
                and estado = 1");
                $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    if (count($result) > 0) {
                        $encry = trim($result[0]["cedula_encr"]);
                        if ($encry != null) {
                            $en = $this->consulta_api_cedula($encry);
                            return $en;
                        } else {
                            continue;
                        }
                    }
                } else {
                    return [0, "INTENTE DE NUEVO"];
                }
                return [0, "INTENTE DE NUEVO"];
            }
        } catch (Exception $e) {

            $e = $e->getMessage();
            return [0, "INTENTE DE NUEVO"];
        }
    }

    function encryptCedula($cedula)
    {
        // Contenido de la clave pública
        $public_key_file = dirname(__DIR__) . "/models/PBKey.txt";
        // Lee el contenido del archivo PEM
        $public_key_content = file_get_contents($public_key_file);
        // Elimina espacios en blanco adicionales alrededor del contenido
        $public_key_content = trim($public_key_content);

        $rsaKey = openssl_pkey_get_public($public_key_content);
        if (!$rsaKey) {
            // Manejar el error de obtener la clave pública
            return [0, openssl_error_string(), $public_key_file];
        }
        // // Divide el texto en bloques para encriptar
        $encryptedData = '';
        $encryptionSuccess = openssl_public_encrypt($cedula, $encryptedData, $rsaKey);

        // Obtener detalles del error, si hubo alguno
        // $error = openssl_error_string();
        // if ($error) {
        //     // Manejar el error de OpenSSL
        //     return $error;
        // }

        // Liberar la clave pública RSA de la memoria
        openssl_free_key($rsaKey);

        if ($encryptionSuccess === false) {
            // Manejar el error de encriptación
            return [0, null, $public_key_file];
        }

        // Devolver la cédula encriptada
        return [1, base64_encode($encryptedData)];
        // echo json_encode(base64_encode($encryptedData));
        // exit();
        // return ($encrypted);
    }

    function Obtener_Datos_Credito($param, $param_DATOS, $val)
    {
        try {
            // $old_error_reporting = error_reporting();
            // Desactivar los mensajes de advertencia
            // error_reporting($old_error_reporting & ~E_WARNING);
            if ($val == 1) {
                $cedula = $param->CEDULA;
                $nacimiento = $param->FECHA_NACIM;
                $CELULAR = base64_decode($param_DATOS["celular"]);
            } else {
                $cedula = $param["CEDULA"];
                $nacimiento = $param["FECHA_NACIM"];
                $CELULAR = ($param_DATOS["celular"]);
            }



            // $cedula = "0930254909";
            $cedula_ECrip = $this->encryptCedula($cedula);
            if ($cedula_ECrip[0] == 0) {
                return [0, $cedula_ECrip, [], []];
            } else {
                $cedula_ECrip = $cedula_ECrip[1];
            }

            $fecha = DateTime::createFromFormat('d/m/Y', $nacimiento);
            $fecha_formateada = $fecha->format('Ymd');
            $ingresos = "500";
            $Instruccion = "SECU";

            $SEC = $this->Get_Secuencial_Api_Banco();
            $SEC = intval($SEC[0]["valor"]) + 1;

            $data = array(
                "transaccion" => 4001,
                "idSession" => "1",
                "secuencial" => $SEC,
                "mensaje" => array(
                    "IdCasaComercialProducto" => 8,
                    "TipoIdentificacion" => "CED",
                    "IdentificacionCliente" => $cedula_ECrip, // Encriptar la cédula
                    "FechaNacimiento" => $fecha_formateada,
                    "ValorIngreso" => $ingresos,
                    "Instruccion" =>  $Instruccion,
                    "Celular" =>  $CELULAR
                )
            );

            // echo json_encode($data);
            // exit();
            // Convertir datos a JSON
            $data_string = json_encode($data);
            // URL del API
            $url = 'https://bs-autentica.com/cco/apiofertaccoqa1/api/CasasComerciales/GenerarCalificacionEnPuntaCasasComerciales';
            // API Key
            $api_key = '0G4uZTt8yVlhd33qfCn5sazR5rDgolqH64kUYiVM5rcuQbOFhQEADhMRHqumswphGtHt1yhptsg0zyxWibbYmjJOOTstDwBfPjkeuh6RITv32fnY8UxhU9j5tiXFrgVz';
            // Inicializa la sesión cURL
            $ch = curl_init($url);

            curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            // Configura las opciones de la solicitud
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                'ApiKeySuscripcion: ' . $api_key
            ));
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');

            // Ejecuta la solicitud y obtiene la respuesta
            $response = (curl_exec($ch));
            // Cierra la sesión cURL
            $error = (curl_error($ch));
            curl_close($ch);
            // Imprime la respuesta
            // echo $response;
            // return [1, $ARRAY];
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            $response_array = json_decode($response, true);

            $this->Update_Secuencial_Api_Banco($SEC);

            // echo json_encode($response_array);
            // exit();
            // if (extension_loaded('curl')) {
            //     echo "cURL está habilitado en este servidor.";
            // } else {
            //     echo "cURL no está habilitado en este servidor.";
            // }

            // Verificar si hay un error en la respuesta
            if (isset($response_array['esError'])) {

                $_inci = array(
                    "ERROR_TYPE" => "API_SOL",
                    "ERROR_CODE" => $response_array['codigo'],
                    "ERROR_TEXT" => $response_array['esError'] . "-"
                        . $response_array['descripcion'] . "-"
                        . $response_array['idSesion'] . "-"
                        . $response_array['secuencial'],
                );
                date_default_timezone_set('America/Guayaquil');
                $hora_actual = date('G');

                if ($response_array['esError'] == true) {
                    if ($response_array['descripcion'] == "No tiene oferta") {
                        $INC = $this->INCIDENCIAS($_inci);
                        return [2, $response_array, $data, $INC];
                    } else if ($response_array['descripcion'] == "Ha ocurrido un error" && $hora_actual >= 21) {
                        $INC = $this->INCIDENCIAS($_inci);
                        return [3, $response_array, $data, $INC, $hora_actual];
                    }
                } else {
                    $INC = $this->INCIDENCIAS($_inci);
                    return [1, $response_array, $data];
                }
            } else {
                // $INC = $this->INCIDENCIAS($_inci);

                return [0, $response_array, $data,$error,$verboseLog,extension_loaded('curl')];
            }
        } catch (Exception $e) {
            // Captura la excepción y maneja el error
            // echo "Error: " . $e->getMessage();
            $param = array(
                "ERROR_TYPE" => "API_SOL_FUNCTION",
                "ERROR_CODE" => "",
                "ERROR_TEXT" => $e->getMessage(),
            );
            $this->INCIDENCIAS($param);
            return [0, "Error al procesar la solictud banco", $e->getMessage()];
        }
    }

    function Obtener_Datos_Credito_($param, $param_DATOS, $val)
    {
        try {
            if ($val == 1) {
                $cedula = $param->CEDULA;
                $nacimiento = $param->FECHA_NACIM;
                $CELULAR = base64_decode($param_DATOS["celular"]);
            } else {
                $cedula = $param["CEDULA"];
                $nacimiento = $param["FECHA_NACIM"];
                $CELULAR = ($param_DATOS["celular"]);
            }

            $cedula_ECrip = $this->encryptCedula($cedula);
            if ($cedula_ECrip[0] == 0) {
                return [0, $cedula_ECrip, [], []];
            } else {
                $cedula_ECrip = $cedula_ECrip[1];
            }

            $fecha = DateTime::createFromFormat('d/m/Y', $nacimiento);
            $fecha_formateada = $fecha->format('Ymd');
            $ingresos = "1500";
            $Instruccion = "SECU";

            $SEC = $this->Get_Secuencial_Api_Banco();
            $SEC = intval($SEC[0]["valor"]) + 1;

            $data = array(
                "transaccion" => 4001,
                "idSession" => "1",
                "secuencial" => $SEC,
                "mensaje" => array(
                    "IdCasaComercialProducto" => 8,
                    "TipoIdentificacion" => "CED",
                    "IdentificacionCliente" => $cedula_ECrip, // Encriptar la cédula
                    "FechaNacimiento" => $fecha_formateada,
                    "ValorIngreso" => $ingresos,
                    "Instruccion" =>  $Instruccion,
                    "Celular" =>  $CELULAR
                )
            );
            $url = 'https://bs-autentica.com/cco/apiofertaccoqa1/api/CasasComerciales/GenerarCalificacionEnPuntaCasasComerciales';

            $api_key = '0G4uZTt8yVlhd33qfCn5sazR5rDgolqH64kUYiVM5rcuQbOFhQEADhMRHqumswphGtHt1yhptsg0zyxWibbYmjJOOTstDwBfPjkeuh6RITv32fnY8UxhU9j5tiXFrgVz';

            // Convertir datos a JSON
            $data_string = json_encode($data);

            // Configurar el contexto del flujo (stream context)
            $context = stream_context_create([
                'http' => [
                    'method' => 'PUT',
                    'header' => 'Content-Type: application/json' . "\r\n" .
                        'Content-Length: ' . strlen($data_string) . "\r\n" .
                        'ApiKeySuscripcion: ' . $api_key . "\r\n",
                    'content' => $data_string
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);

            // Realizar la solicitud HTTP utilizando file_get_contents
            $response = file_get_contents($url, false, $context);
            $error = error_get_last();
            $error_message = $error['message'];
            // Manejar la respuesta
            $response_array = json_decode($response, true);
            $verboseLog = ""; // En este caso, no hay información de verbose

            $this->Update_Secuencial_Api_Banco($SEC);

            if (isset($response_array['esError'])) {
                $_inci = array(
                    "ERROR_TYPE" => "API_SOL",
                    "ERROR_CODE" => $response_array['codigo'],
                    "ERROR_TEXT" => $response_array['esError'] . "-" .
                        $response_array['descripcion'] . "-" .
                        $response_array['idSesion'] . "-" .
                        $response_array['secuencial'],
                );
                date_default_timezone_set('America/Guayaquil');
                $hora_actual = date('G');

                if ($response_array['esError'] == true) {
                    if ($response_array['descripcion'] == "No tiene oferta") {
                        $INC = $this->INCIDENCIAS($_inci);
                        return [2, $response_array, $data, $INC];
                    } elseif ($response_array['descripcion'] == "Ha ocurrido un error" && $hora_actual >= 21) {
                        $INC = $this->INCIDENCIAS($_inci);
                        return [3, $response_array, $data, $INC, $hora_actual];
                    }
                } else {
                    $INC = $this->INCIDENCIAS($_inci);
                    return [1, $response_array, $data];
                }
            } else {
                return [0, $response_array, $data, $error_message, $verboseLog];
            }
        } catch (Exception $e) {
            $param = array(
                "ERROR_TYPE" => "API_SOL_FUNCTION",
                "ERROR_CODE" => "",
                "ERROR_TEXT" => $e->getMessage(),
            );
            $this->INCIDENCIAS($param);
            return [0, "Error al procesar la solictud banco", $e->getMessage()];
        }
    }


    function Get_Secuencial_Api_Banco()
    {
        try {
            // sleep(4);
            // $cedula = trim($param["cedula"]);
            $arr = "";
            $query = $this->db->connect_dobra()->prepare("SELECT * FROM parametros where id = 1");
            // $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return [0, "INTENTE DE NUEVO"];
        }
    }

    function Update_Secuencial_Api_Banco($SEC)
    {
        try {
            // sleep(4);
            // $cedula = trim($param["cedula"]);
            $arr = "";
            $query = $this->db->connect_dobra()->prepare("UPDATE parametros 
                SET valor = :valor
            where id = 1");
            $query->bindParam(":valor", $SEC, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return [0, "INTENTE DE NUEVO"];
        }
    }

    function ELiminar_Cedulas_No_existen($param)
    {

        try {
            $cedula = trim($param["cedula"]);
            $query = $this->db->connect_dobra()->prepare('UPDATE creditos_solicitados
            set estado = 0
            where cedula = :cedula
            ');
            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function ELiminar_Cedulas_No_existen_2($param)
    {

        try {
            $cedula = trim($param["cedula"]);
            $query = $this->db->connect_dobra()->prepare('DELETE FROM creditos_solicitados
            where cedula = :cedula AND numero is null
            ');
            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Guardar_Datos_Banco($VAL_CEDULA, $VAL_CREDITO, $param, $val)
    {

        try {

            // echo json_encode($VAL_CEDULA);
            // exit();

            $link = constant("URL") . "/public/img/SV24 - Mensajes LC_Proceso.png";
            $RUTA_ARCHIVO = trim($param["cedula"]) . "_" . date("YmdHis") . ".pdf";
            $DATOS_CREDITO = $VAL_CREDITO[1];

            if ($val == 1) {
                $DATOS_CEDULA = $VAL_CEDULA[1];
                $cedula = trim($param["cedula"]);
                $email = trim($param["email"]);
                $celular = base64_decode(trim($param["celular"]));

                $nombre = $DATOS_CEDULA[0]->NOMBRES;
                $fecha_nacimiento = $DATOS_CEDULA[0]->FECHA_NACIM;
                $codigo_dactilar = $DATOS_CEDULA[0]->INDIVIDUAL_DACTILAR;
                $CANT_DOM = $DATOS_CEDULA[0]->CANT_DOM;
            } else {
                $DATOS_CEDULA = $VAL_CEDULA;

                $cedula = trim($param["cedula"]);
                $email = trim($param["email"]);
                $celular = (trim($param["celular"]));

                $nombre = $DATOS_CEDULA["NOMBRES"];
                $fecha_nacimiento = $DATOS_CEDULA["FECHA_NACIM"];
                $codigo_dactilar = $DATOS_CEDULA["INDIVIDUAL_DACTILAR"];
                $CANT_DOM = $DATOS_CEDULA["CANT_DOM"];
            }

            $ip = $this->getRealIP();
            $dispositivo = $_SERVER['HTTP_USER_AGENT'];

            $credito_aprobado = floatval($DATOS_CREDITO["mensaje"]["montoMaximo"]) > 0 ? 1 : 0;
            $credito_aprobado_texto = floatval($DATOS_CREDITO["mensaje"]["montoMaximo"]) > 0 ? "APROBADO" : "RECHAZADO";

            $API_SOL_descripcion = $DATOS_CREDITO["descripcion"];
            $API_SOL_campania = $DATOS_CREDITO["mensaje"]["campania"];
            $API_SOL_identificacion = $DATOS_CREDITO["mensaje"]["identificacion"];
            $API_SOL_lote = $DATOS_CREDITO["mensaje"]["lote"];
            $API_SOL_montoMaximo = $DATOS_CREDITO["mensaje"]["montoMaximo"];
            $API_SOL_nombreCampania = $DATOS_CREDITO["mensaje"]["nombreCampania"];
            $API_SOL_plazoMaximo = $DATOS_CREDITO["mensaje"]["plazoMaximo"];
            $API_SOL_promocion = $DATOS_CREDITO["mensaje"]["promocion"];
            $API_SOL_segmentoRiesgo = $DATOS_CREDITO["mensaje"]["segmentoRiesgo"];
            $API_SOL_subLote = $DATOS_CREDITO["mensaje"]["subLote"];
            $API_SOL_idSesion = $DATOS_CREDITO["idSesion"];

            // echo json_encode($DATOS_CREDITO);
            // exit();

            if ($val == 1) {
                $sql = "UPDATE creditos_solicitados
                SET
                    numero = :numero, 
                    correo = :correo,
                    nombre_cliente = :nombre_cliente, 
                    fecha_nacimiento = :fecha_nacimiento, 
                    codigo_dactilar = :codigo_dactilar,
                    ip = :ip,
                    dispositivo = :dispositivo,
                    ruta_archivo =:ruta_archivo,
                    localidad =:localidad,
    
                    API_SOL_descripcion =:API_SOL_descripcion,
                    API_SOL_campania =:API_SOL_campania,
                    API_SOL_identificacion =:API_SOL_identificacion,
                    API_SOL_lote =:API_SOL_lote,
                    API_SOL_montoMaximo =:API_SOL_montoMaximo,
                    API_SOL_nombreCampania =:API_SOL_nombreCampania,
                    API_SOL_plazoMaximo =:API_SOL_plazoMaximo,
                    API_SOL_promocion =:API_SOL_promocion,
                    API_SOL_segmentoRiesgo =:API_SOL_segmentoRiesgo,
                    API_SOL_subLote =:API_SOL_subLote,
                    API_SOL_idSesion =:API_SOL_idSesion,
                    credito_aprobado = :credito_aprobado,
                    credito_aprobado_texto = :credito_aprobado_texto
                WHERE cedula = :cedula";
            } else {
                $sql = "INSERT INTO creditos_solicitados 
                (
                    numero, 
                    correo, 
                    nombre_cliente, 
                    fecha_nacimiento, 
                    codigo_dactilar, 
                    ip, 
                    dispositivo, 
                    ruta_archivo, 
                    localidad, 
                    API_SOL_descripcion, 
                    API_SOL_campania, 
                    API_SOL_identificacion, 
                    API_SOL_lote, 
                    API_SOL_montoMaximo, 
                    API_SOL_nombreCampania, 
                    API_SOL_plazoMaximo, 
                    API_SOL_promocion, 
                    API_SOL_segmentoRiesgo, 
                    API_SOL_subLote, 
                    API_SOL_idSesion, 
                    credito_aprobado, 
                    credito_aprobado_texto, 
                    cedula
                ) 
                VALUES 
                (
                    :numero, 
                    :correo, 
                    :nombre_cliente, 
                    :fecha_nacimiento, 
                    :codigo_dactilar,
                    :ip, 
                    :dispositivo, 
                    :ruta_archivo,
                    :localidad, 
                    :API_SOL_descripcion,
                    :API_SOL_campania, 
                    :API_SOL_identificacion, 
                    :API_SOL_lote, 
                    :API_SOL_montoMaximo, 
                    :API_SOL_nombreCampania,
                    :API_SOL_plazoMaximo,
                    :API_SOL_promocion, 
                    :API_SOL_segmentoRiesgo,
                    :API_SOL_subLote, 
                    :API_SOL_idSesion, 
                    :credito_aprobado, 
                    :credito_aprobado_texto, 
                    :cedula
                )";
            }




            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            $query->bindParam(":numero", $celular, PDO::PARAM_STR);
            $query->bindParam(":correo", $email, PDO::PARAM_STR);
            $query->bindParam(":nombre_cliente", $nombre, PDO::PARAM_STR);
            $query->bindParam(":fecha_nacimiento", $fecha_nacimiento, PDO::PARAM_STR);
            $query->bindParam(":codigo_dactilar", $codigo_dactilar, PDO::PARAM_STR);
            $query->bindParam(":ip", $ip, PDO::PARAM_STR);
            $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);
            $query->bindParam(":ruta_archivo", $RUTA_ARCHIVO, PDO::PARAM_STR);
            $query->bindParam(":localidad", $CANT_DOM, PDO::PARAM_STR);

            $query->bindParam(":API_SOL_descripcion", $API_SOL_descripcion, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_campania", $API_SOL_campania, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_identificacion", $API_SOL_identificacion, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_lote", $API_SOL_lote, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_montoMaximo", $API_SOL_montoMaximo, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_nombreCampania", $API_SOL_nombreCampania, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_plazoMaximo", $API_SOL_plazoMaximo, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_promocion", $API_SOL_promocion, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_segmentoRiesgo", $API_SOL_segmentoRiesgo, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_subLote", $API_SOL_subLote, PDO::PARAM_STR);
            $query->bindParam(":API_SOL_idSesion", $API_SOL_idSesion, PDO::PARAM_STR);
            $query->bindParam(":credito_aprobado", $credito_aprobado, PDO::PARAM_STR);
            $query->bindParam(":credito_aprobado_texto", $credito_aprobado_texto, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $query_cant_con = $this->db->connect_dobra()->prepare("INSERT INTO cantidad_consultas
                (
                    numero,
                    cantidad
                )VALUES
                (
                    :numero,
                    1
                )");
                $query_cant_con->bindParam(":numero", $celular, PDO::PARAM_STR);
                $query_cant_con->execute();


                if ($credito_aprobado == 1) {

                    $html = '
                <div class="text-center mt-3">
                    <h1 style="font-size:60px" class="text-primary">Felicidades! </h1>
                    <h2>Tienes credito disponible</h2>
                    <img style="width: 100%;" src="' . $link . '" alt="">
                </div>';
                } else {
                    $html = '  
                <div class="text-center">
                    <h1 class="text-danger">Lamentablemente el perfil con la cédula entregada no aplica para el crédito, no cumple con las políticas del banco.</h1>
                    <h3><i class="bi bi-tv fs-1"></i> Mire el siguiente video ➡️ </h3>
                    <a class="fs-3" href="https://youtu.be/EMaHXoCefic">https://youtu.be/EMaHXoCefic ��</a>
                    <h3 class="mt-3">Le invitamos a llenar la siguiente encuesta ➡️ </h3>
                    <a class="fs-3" href="https://forms.gle/s3GwuwoViF4Z2Jpt6">https://forms.gle/s3GwuwoViF4Z2Jpt6</a>
                    <h3></h3>
                </div>';
                }
                $this->Generar_Documento($RUTA_ARCHIVO, $nombre, $cedula);
                if ($val == 1) {
                    echo json_encode([1, $DATOS_CEDULA, $DATOS_CREDITO, $html]);
                    exit();
                } else {
                    echo json_encode([2, $DATOS_CEDULA, $DATOS_CREDITO, $html]);
                    exit();
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, "error al verificar información", "Intentelo de nuevo", $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, "No se pudo realizar la verificaciolln", "Intentelo de nuevo", $e]);
            exit();
        }
    }


    function Generar_Documento($RUTA_ARCHIVO, $nombre, $cedula)
    {

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Título
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, utf8_decode('AUTORIZACIÓN PARA EL TRATAMIENTO DE DATOS PERSONALES'), 0, 1, 'C');
        $pdf->Cell(0, 2, utf8_decode('SALVACERO CIA. LTDA.'), 0, 1, 'C');
        $pdf->Ln(3);

        // Contenido
        $pdf->SetFont('Arial', '', 9);
        $contenido = utf8_decode("
        Declaración de Capacidad legal y sobre la Aceptación:\n
        Por medio de la presente autorizo de manera libre, voluntaria, previa, informada e inequívoca a SALVACERO CIA. LTDA.
        para que en los términos legalmente establecidos realice el tratamiento de mis datos personales como parte de la relación
        precontractual, contractual y post contractual para:\n
        El procesamiento, análisis, investigación, estadísticas, referencias y demás trámites para facilitar, promover, permitir o
        mantener las relaciones con SALVACERO CIA. LTDA.\n
        Cuantas veces sean necesarias, gestione, obtenga y valide de cualquier entidad pública y/o privada que se encuentre
        facultada en el país, de forma expresa a la Dirección General de Registro Civil, Identificación y Cedulación, a la Dirección
        Nacional de Registros Públicos, al Servicio de Referencias Crediticias, a los burós de información crediticia, instituciones
        financieras de crédito, de cobranza, compañías emisoras o administradoras de tarjetas de crédito, personas naturales y los
        establecimientos de comercio, personas señaladas como referencias, empleador o cualquier otra entidad y demás fuentes
        legales de información autorizadas para operar en el país, información y/o documentación relacionada con mi perfil, capacidad
        de pago y/o cumplimiento de obligaciones, para validar los datos que he proporcionado, y luego de mi aceptación sean
        registrados para el desarrollo legítimo de la relación jurídica o comercial, así como para realizar actividades de tratamiento
        sobre mi comportamiento crediticio, manejo y movimiento de cuentas bancarias, tarjetas de crédito, activos, pasivos,
        datos/referencias personales y/o patrimoniales del pasado, del presente y las que se generen en el futuro, sea como deudor
        principal, codeudor o garante, y en general, sobre el cumplimiento de mis obligaciones. Faculto expresamente a SALVACERO
        CIA. LTDA. para transferir o entregar a las mismas personas o entidades, la información relacionada con mi comportamiento
        crediticio.\n
        Tratar, transferir y/o entregar la información que se obtenga en virtud de esta solicitud incluida la relacionada con mi
        comportamiento crediticio y la que se genere durante la relación jurídica y/o comercial a autoridades competentes, terceros,
        socios comerciales y/o adquirientes de cartera, para el tratamiento de mis datos personales conforme los fines detallados en
        esta autorización o que me contacten por cualquier medio para ofrecerme los distintos servicios y productos que integran su
        portafolio y su gestión, relacionados o no con los servicios financieros. En caso de que el SALVACERO CIA. LTDA. ceda o
        transfiera cartera adeudada por mí, el cesionario o adquiriente de dicha cartera queda desde ahora expresamente facultado
        para realizar las mismas actividades establecidas en esta autorización.\n
        Fines informativos, marketing, publicitarios y comerciales a través del servicio de telefonía, correo electrónico, mensajería
        SMS, WhatsApp, redes sociales y/o cualquier otro medio de comunicación electrónica.\n
        Entiendo y acepto que mi información personal podrá ser almacenada de manera digital, y accederán a ella los funcionarios
        de SALVACERO CIA. LTDA., estando obligados a cumplir con la legislación aplicable a las políticas de confidencialidad,
        protección de datos y sigilo bancario. En caso de que exista una negativa u oposición para el tratamiento de estos datos, no
        podré disfrutar de los servicios o funcionalidades que SALVACERO CIA. LTDA. ofrece y no podrá suministrarme productos,
        ni proveerme sus servicios o contactarme y en general cumplir con varias de las finalidades descritas en la Política.\n
        SALVACERO CIA. LTDA. conservará la información personal al menos durante el tiempo que dure la relación comercial y el
        que sea necesario para cumplir con la normativa respectiva del sector relativa a la conservación de archivos.\n
        Declaro conocer que para el desarrollo de los propósitos previstos en el presente documento y para fines precontractuales,
        contractuales y post contractuales es indispensable el tratamiento de mis datos personales conforme a la Política disponible
        en la página web de SALVACERO CIA. LTDA.\n
        Asimismo, declaro haber sido informado por el SALVACERO CIA. LTDA. de los derechos con que cuento para conocer,
        actualizar y rectificar mi información personal; así como, si no deseo continuar recibiendo información comercial y/o
        publicidad, deberé remitir mi requerimiento a través del proceso de atención de derechos ARSO+ en cualquier momento y
        sin costo alguno, utilizando la página web https://www.salvacero.com/terminos o comunicado escrito a Srs. Salvacero y
        enviando un correo electrónico a la dirección marketing@salvacero.com\n
        En virtud de que, para ciertos productos y servicios SALVACERO CIA. LTDA. requiere o solicita el tratamiento de datos
        personales de un tercero que como cliente podré facilitar, como por ejemplo referencias comerciales o de contacto, garantizo
        que, si proporciono datos personales de terceras personas, les he solicitado su aceptación e informado acerca de las
        finalidades y la forma en la que SALVACERO CIA. LTDA. necesita tratar sus datos personales.\n
        Para la comunicación de sus datos personales se tomarán las medidas de seguridad adecuadas conforme la normativa
        vigente. 
        ");
        $pdf->MultiCell(0, 4, $contenido);
        $pdf->Ln(3);

        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, utf8_decode('AUTORIZACIÓN EXPLÍCITA DE TRATAMIENTO DE DATOS PERSONALES'), 0, 1, 'C');
        $pdf->Cell(0, 2, utf8_decode('SALVACERO CIA. LTDA.'), 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('Arial', '', 9);
        $contenido = utf8_decode("
        Declaro que soy el titular de la información reportada, y que la he suministrado de forma voluntaria, completa, confiable,
        veraz, exacta y verídica:\n
        Como titular de los datos personales, particularmente el código dactilar, no me encuentro obligado a otorgar mi autorización
        de tratamiento a menos que requiera consultar y/o aplicar a un producto y/o servicio financiero. A través de la siguiente
        autorización libre, especifica, previa, informada, inequívoca y explícita, faculto al tratamiento (recopilación, acceso, consulta,
        registro, almacenamiento, procesamiento, análisis, elaboración de perfiles, comunicación o transferencia y eliminación) de
        mis datos personales incluido el código dactilar con la finalidad de: consultar y/o aplicar a un producto y/o servicio financiero
        y ser sujeto de decisiones basadas única o parcialmente en valoraciones que sean producto de procesos automatizados,
        incluida la elaboración de perfiles. Esta información será conservada por el plazo estipulado en la normativa aplicable.\n
        Así mismo, declaro haber sido informado por SALVACERO CIA. LTDA. de los derechos con que cuento para conocer,
        actualizar y rectificar mi información personal, así como, los establecidos en el artículo 20 de la LOPDP y remitir mi
        requerimiento a través del proceso de atención de derechos ARSO+; en cualquier momento y sin costo alguno, utilizando la
        página web https://www.salvacero.com/terminos, comunicado escrito o en cualquiera de las agencias de SALVACERO CIA.
        LTDA.\n
        Para proteger esta información tenemos medidas técnicas y organizativas de seguridad adaptadas a los riesgos como, por
        ejemplo: anonimización, cifrado, enmascarado y seudonimización.\n
        Con la lectura de este documento manifiesto que he sido informado sobre el Tratamiento de mis Datos Personales, y otorgo
        mi autorización y aceptación de forma voluntaria y verídica, tanto para la SALVACERO CIA. LTDA. y para cualquier cesionario
        o endosatario, especialmente Banco Solidario S.A. En señal de aceptación suscribo el presente documento.
        ");

        $pdf->MultiCell(0, 4, $contenido);
        $pdf->Ln(3);
        date_default_timezone_set('America/Guayaquil');
        // Información del cliente
        $pdf->SetFont('Arial', 'I', 11);
        $nombreCliente = $nombre; // Aquí debes poner el nombre del cliente
        $fechaConsulta = date("Y-m-d h:i A"); // Fecha de la consulta
        $direccionIP = $this->getRealIP(); // Dirección IP del cliente


        // $fecha = DateTime::createFromFormat('YmdHis', $fechaConsulta);
        // $fechaFormateada = $fecha->format('Y-m-d H:i A');
        // Información del cliente
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, '      CLIENTE: ', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, "      " . utf8_decode($nombreCliente) . " - " . $cedula, 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, "      " . utf8_decode('ACEPTÓ TERMINOS Y CONDICIONES: '), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, "      " . $fechaConsulta, 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, utf8_decode('      DIRECCIÓN IP: '), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6,  "      " . $direccionIP, 0, 1, 'L');


        $nombreArchivo = $RUTA_ARCHIVO; // Nombre del archivo PDF
        $rutaCarpeta = dirname(__DIR__) . '/recursos/docs/'; // Ruta de la carpeta donde se guardará el archivo (debes cambiar esto)

        if (chmod($rutaCarpeta, 0777)) {
            // echo "Permisos cambiados exitosamente.";
        }

        $pdf->Output($rutaCarpeta . $nombreArchivo, 'F');
    }


    function Generar_pdf($param)
    {
        $nombre = $param["nombre_cliente"];
        $cedula = $param["cedula"];
        $fechaConsulta = $param["fecha_creado"];
        $ip = $param["ip"];

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Título
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, utf8_decode('AUTORIZACIÓN PARA EL TRATAMIENTO DE DATOS PERSONALES'), 0, 1, 'C');
        $pdf->Cell(0, 2, utf8_decode('BANCO SOLIDARIO S.A.'), 0, 1, 'C');
        $pdf->Ln(3);

        // Contenido
        $pdf->SetFont('Arial', '', 9);
        $contenido = utf8_decode("
        Declaración de Capacidad legal y sobre la Aceptación:\n
        Por medio de la presente autorizo de manera libre, voluntaria, previa, informada e inequívoca a BANCO SOLIDARIO
        S.A. para que en los términos legalmente establecidos realice el tratamiento de mis datos personales como parte de
        la relación precontractual, contractual y post contractual para: \n
        El procesamiento, análisis, investigación, estadísticas, referencias y demás trámites para facilitar, promover, permitir
        o mantener las relaciones con el BANCO. \n
        Cuantas veces sean necesarias, gestione, obtenga y valide de cualquier entidad pública y/o privada que se encuentre
        facultada en el país, de forma expresa a la Dirección General de Registro Civil, Identificación y Cedulación, a la Dirección
        Nacional de Registros Públicos, al Servicio de Referencias Crediticias, a los burós de información crediticia, instituciones
        financieras de crédito, de cobranza, compañías emisoras o administradoras de tarjetas de crédito, personas naturales
        y los establecimientos de comercio, personas señaladas como referencias, empleador o cualquier otra entidad y demás
        fuentes legales de información autorizadas para operar en el país, información y/o documentación relacionada con mi
        perfil, capacidad de pago y/o cumplimiento de obligaciones, para validar los datos que he proporcionado, y luego de
        mi aceptación sean registrados para el desarrollo legítimo de la relación jurídica o comercial, así como para realizar
        actividades de tratamiento sobre mi comportamiento crediticio, manejo y movimiento de cuentas bancarias, tarjetas
        de crédito, activos, pasivos, datos/referencias personales y/o patrimoniales del pasado, del presente y las que se
        generen en el futuro, sea como deudor principal, codeudor o garante, y en general, sobre el cumplimiento de mis
        obligaciones. Faculto expresamente al Banco para transferir o entregar a las mismas personas o entidades, la
        información relacionada con mi comportamiento crediticio. Esta expresa autorización la otorgo al Banco o a cualquier
        cesionario o endosatario. \n
        Tratar, transferir y/o entregar la información que se obtenga en virtud de esta solicitud incluida la relacionada con mi
        comportamiento crediticio y la que se genere durante la relación jurídica o comercial a autoridades competentes,
        terceros, socios comerciales y/o adquirientes de cartera, para el tratamiento de mis datos personales conforme los
        fines detallados en esta autorización o que me contacten por cualquier medio para ofrecerme los distintos servicios y
        productos que integran su portafolio y su gestión, relacionados o no con los servicios financieros del BANCO. En caso
        de que el BANCO ceda o transfiera cartera adeudada por mí, el cesionario o adquiriente de dicha cartera queda desde
        ahora expresamente facultado para realizar las mismas actividades establecidas en esta autorización.\n
        Entiendo y acepto que mi información personal podrá ser almacenada de manera impresa o digital, y accederán a ella
        los funcionarios de BANCO SOLIDARIO, estando obligados a cumplir con la legislación aplicable a las políticas de
        confidencialidad, protección de datos y sigilo bancario. En caso de que exista una negativa u oposición para el
        tratamiento de estos datos, no podré disfrutar de los servicios o funcionalidades que el BANCO ofrece y no podrá
        suministrarme productos, ni proveerme sus servicios o contactarme y en general cumplir con varias de las finalidades
        descritas en la Política. \n
        El BANCO conservará la información personal al menos durante el tiempo que dure la relación comercial y el que sea
        necesario para cumplir con la normativa respectiva del sector relativa a la conservación de archivos. \n
        Declaro conocer que para el desarrollo de los propósitos previstos en el presente documento y para fines
        precontractuales, contractuales y post contractuales es indispensable el tratamiento de mis datos personales
        conforme a la Política disponible en la página web del BANCO www.banco-solidario.com/transparencia Asimismo,
        declaro haber sido informado por el BANCO de los derechos con que cuento para conocer, actualizar y rectificar mi
        información personal; así como, si no deseo continuar recibiendo información comercial y/o publicidad, deberé remitir
        mi requerimiento a través del proceso de atención de derechos ARSO+ en cualquier momento y sin costo alguno,
        utilizando la página web (www.banco-solidario.com), teléfono: 1700 765 432, comunicado escrito o en cualquiera de
        las agencias del BANCO. \n
        En virtud de que, para ciertos productos y servicios el BANCO requiere o solicita el tratamiento de datos personales
        de un tercero que como cliente podré facilitar, como por ejemplo referencias comerciales o de contacto, garantizo
        que, si proporciono datos personales de terceras personas, les he solicitado su aceptación e informado acerca de las
        finalidades y la forma en la que el BANCO necesita tratar sus datos personales. \n
        Para la comunicación de sus datos personales se tomarán las medidas de seguridad adecuadas conforme la normativa
        vigente.\n
       
        ");
        $pdf->MultiCell(0, 4, $contenido);
        $pdf->Ln(3);

        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, utf8_decode('AUTORIZACIÓN EXPLÍCITA DE TRATAMIENTO DE DATOS PERSONALES'), 0, 1, 'C');
        $pdf->Cell(0, 2, utf8_decode('BANCO SOLIDARIO S.A.'), 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('Arial', '', 9);
        $contenido = utf8_decode("
        Declaro que soy el titular de la información reportada, y que la he suministrado de forma voluntaria, completa,
        confiable, veraz, exacta y verídica:\n
        Como titular de los datos personales, particularmente el código dactilar, dato biométrico facial, no me encuentro
        obligado a otorgar mi autorización de tratamiento a menos que requiera consultar y/o aplicar a un producto y/o
        servicio financiero. A través de la siguiente autorización libre, especifica, previa, informada, inequívoca y explícita,
        faculto al tratamiento (recopilación, acceso, consulta, registro, almacenamiento, procesamiento, análisis, elaboración
        de perfiles, comunicación o transferencia y eliminación) de mis datos personales incluido el código dactilar con la
        finalidad de: consultar y/o aplicar a un producto y/o servicio financiero y ser sujeto de decisiones basadas única o
        parcialmente en valoraciones que sean producto de procesos automatizados, incluida la elaboración de perfiles. Esta
        información será conservada por el plazo estipulado en la normativa aplicable. \n
        Así mismo, declaro haber sido informado por el BANCO de los derechos con que cuento para conocer, actualizar y
        rectificar mi información personal, así como, los establecidos en el artículo 20 de la LOPDP y remitir mi requerimiento
        a través del proceso de atención de derechos ARSO+; en cualquier momento y sin costo alguno, utilizando la página
        web (www.banco-solidario.com), teléfono: 1700 765 432, comunicado escrito o en cualquiera de las agencias del
        BANCO. \n
        Para proteger esta información conozco que el Banco cuenta con medidas técnicas y organizativas de seguridad
        adaptadas a los riesgos como, por ejemplo: anonimización, cifrado, enmascarado y seudonimización. \n
        Con la lectura de este documento manifiesto que he sido informado sobre el Tratamiento de mis Datos Personales, y
        otorgo mi autorización y aceptación de forma voluntaria y verídica. En señal de aceptación suscribo el presente
        documento. 
        ");

        $pdf->MultiCell(0, 4, $contenido);
        $pdf->Ln(3);

        date_default_timezone_set('America/Guayaquil');

        $fecha = DateTime::createFromFormat('YmdHis', $fechaConsulta);
        $fechaFormateada = $fecha->format('Y-m-d H:i A');
        // Información del cliente
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, '      CLIENTE: ', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, "      " . utf8_decode($nombre) . " - " . $cedula, 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, "      " . utf8_decode('ACEPTÓ TERMINOS Y CONDICIONES: '), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, "      " . $fechaFormateada, 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, utf8_decode('      DIRECCIÓN IP: '), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6,  "      " . $ip, 0, 1, 'L');


        $nombreArchivo = $cedula . "_" . $fechaConsulta . ".pdf"; // Nombre del archivo PDF
        $rutaCarpeta = dirname(__DIR__) . '/recursos/docs/'; // Ruta de la carpeta donde se guardará el archivo (debes cambiar esto)

        if (chmod($rutaCarpeta, 0777)) {
            // echo "Permisos cambiados exitosamente.";
        }

        $pdf->Output($rutaCarpeta . $nombreArchivo, 'F');

        try {
            $cedula = trim($param["cedula"]);
            $query = $this->db->connect_dobra()->prepare('UPDATE creditos_solicitados
            set ruta_archivo = :ruta_archivo
            where cedula = :cedula
            ');
            $query->bindParam(":ruta_archivo", $nombreArchivo, PDO::PARAM_STR);
            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(1);
                exit();
                // return 1;
            } else {
                // return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    function INCIDENCIAS($param)
    {
        try {
            $ERROR_TYPE = trim($param["ERROR_TYPE"]);
            $ERROR_CODE = trim($param["ERROR_CODE"]);
            $ERROR_TEXT = trim($param["ERROR_TEXT"]);

            $query = $this->db->connect_dobra()->prepare('INSERT INTO incidencias 
            (
                ERROR_TYPE, 
                ERROR_CODE, 
                ERROR_TEXT
            ) 
            VALUES
            (
                :ERROR_TYPE, 
                :ERROR_CODE, 
                :ERROR_TEXT
            )
            ');
            $query->bindParam(":ERROR_TYPE", $ERROR_TYPE, PDO::PARAM_STR);
            $query->bindParam(":ERROR_CODE", $ERROR_CODE, PDO::PARAM_STR);
            $query->bindParam(":ERROR_TEXT", $ERROR_TEXT, PDO::PARAM_STR);

            if ($query->execute()) {
                // $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }




    function CONVERT_($string)
    {
        //$string = "jorge";
        $convertedString = substr($string, 0, 2) . str_repeat("*", strlen($string) - 2) . substr($string, -1);
        return $convertedString;
        // $string = "DE LA ESE ROMERO NEHEMIAS FERNANDO";
        // $result = preg_replace('/([A-Z])([A-Z]+)/', '$1*' . str_repeat('*', strlen('$2')), $string);
        // echo $result; // Output: "DE LA E*E R****O N******S F******O"
    }

    function CONVERT_M($string)
    {
        //$string = "jorge";
        $convertedString = substr($string, 0, 1) . str_repeat("*", strlen($string) - 0) . substr($string, -1);
        return $convertedString;
        // $string = "DE LA ESE ROMERO NEHEMIAS FERNANDO";
        // $result = preg_replace('/([A-Z])([A-Z]+)/', '$1*' . str_repeat('*', strlen('$2')), $string);
        // echo $result; // Output: "DE LA E*E R****O N******S F******O"
    }

    function CONVERT_C($string)
    {
        //$string = "jorge";
        if ($string == "") {
            return "";
        } else {
            $convertedString = substr($string, 0, 2) . str_repeat("*", strlen($string) - 2) . substr($string, -3);
            return $convertedString;
        }
    }

    function CONVERT_E($string)
    {
        //$string = "jorge";
        if ($string == "") {
            return "";
        } else {
            $convertedString = substr($string, 0, 2) . str_repeat("*", strlen($string) - 2) . substr($string, -2);
            return $convertedString;
        }
    }

    function Validar_Actualizacion($param)
    {
        try {
            $cedula = $param["cedula"];

            $query = $this->db->connect_dobra()->prepare('SELECT Ruc 
                from WEB_CARTIMEX_DATOS_POLITICAS with(nolock)
                where Ruc = :ruc
            ');
            $query->bindParam("ruc", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    return (["Los datos ya han sido actualizados", "info"]);
                    exit();
                } else {
                    $g = $this->Guardar_datos($param);
                    return $g;
                }
                // $this->Generador_pdf();

            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Validar_Cliente($param)
    {
        try {
            $cedula = $param["cedula"];
            $query = $this->db->connect_dobra()->prepare('SELECT Ruc 
                from CLI_CLIENTES with(nolock)
                where Ruc = :ruc
            ');
            $query->bindParam("ruc", $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    $g = $this->Guardar_datos($param);
                    return $g;
                } else {
                    return (["Cédula no existe", "error"]);
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Guardar_datos($param)
    {

        try {
            $cedula = $_SESSION["CED"];
            $email = $param["email"];
            $telefono = $param["telefono"];
            $check_g = $param["check_g"];
            $check_pd = $param["check_pd"];
            $ip = $this->getRealIP();
            $OS = $_SERVER['HTTP_USER_AGENT'];
            $metodo = "ACTUALIZACION_DATOS";
            $empresa = "CARTIMEX";

            $apellido = "";
            $VAL = 0;
            $DATOS = $this->VALIDAR_CEDULA_G($cedula);
            if (trim($email) == "") {
                if ($DATOS[0]["Email"] == "") {
                    $VAL = $VAL + 1;
                    echo json_encode(["Debe ingresar un email", "error"]);
                    exit();
                } else {
                    $email = $DATOS[0]["Email"];
                    // echo json_encode([$email, "success"]);
                    // exit();
                }
            }
            if (trim($telefono) == "") {
                if ($DATOS[0]["Email"] == "") {
                    $VAL = $VAL + 1;
                    echo json_encode(["Debe ingresar un teléfono", "error"]);
                    exit();
                } else {
                    $telefono = $DATOS[0]["Celular"];
                }
            }

            // echo json_encode([$cedula, $email, $telefono]);
            // exit();

            if ($VAL == 0) {
                $query = $this->db->connect_dobra()->prepare('INSERT INTO WEB_CARTIMEX_DATOS_POLITICAS
                (
                    ruc,
                    email,
                    telefono,
                    politica_general,
                    politica_pr_datos,
                    nombres,
                    apellidos,
                    empresa,
                    ip,
                    so,
                    metodo
                )
                    VALUES
                (
                    :ruc,
                    :email,
                    :telefono,
                    :politica_general,
                    :politica_pr_datos,
                    :nombres,
                    :apellidos,
                    :empresa,
                    :ip,
                    :so,
                    :metodo
                )
            ');

                $query->bindParam(":ruc", $cedula, PDO::PARAM_STR);
                $query->bindParam(":email", $email, PDO::PARAM_STR);
                $query->bindParam(":telefono", $telefono, PDO::PARAM_STR);
                $query->bindParam(":politica_general", $check_g, PDO::PARAM_STR);
                $query->bindParam(":politica_pr_datos", $check_pd, PDO::PARAM_STR);
                $query->bindParam(":nombres", $DATOS[0]["Nombres"], PDO::PARAM_STR);
                $query->bindParam(":apellidos", $apellido, PDO::PARAM_STR);
                $query->bindParam(":empresa", $empresa, PDO::PARAM_STR);
                $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                $query->bindParam(":so", $OS, PDO::PARAM_STR);
                $query->bindParam(":metodo", $metodo, PDO::PARAM_STR);

                if ($query->execute()) {
                    $EMAIL = $this->Enviar_correo($email, $DATOS[0]["Nombres"]);
                    echo json_encode(["Datos actualizados", "success"]);
                    exit();
                } else {
                    $err = $query->errorInfo();
                    echo json_encode($err);
                    exit();
                }
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function VALIDAR_CEDULA_G($CED)
    {
        try {


            $query = $this->db->connect_dobra()->prepare('{CALL SGO_Consulta_ActualizacionDatos (?) }');
            $query->bindParam(1, $CED, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Enviar_correo($email, $nombre)
    {


        $msg = "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;'>";
        $msg .= "<h1 style='text-align:center; color: #24448c;'>Actualización de datos</h1><br><br>";
        $msg .= "<p style='text-align: justify;'>Estimad@ " . $nombre . "</p>";
        $msg .= "<p style='text-align: justify;'>Sus datos han sido actualizados correctamente</p>";
        $msg .= "<p>Fecha y hora de envío: " . date('d/m/Y H:i:s') . "</p>";
        $msg .= "<p style='text-align: justify;'>Si recibe este correo y usted no ha sido quien actualizó los datos envienos un correo a datospersonales@cartimex.net</p>";
        $msg .= "<div style='text-align:center;'>";
        $SO = PHP_OS;
        //$msg .= "<a href='http://186.3.23.4:82/rolesrh/Verificar.php?rolid=" . base64_encode($ID_rol) . "&rol_nombre=" . base64_encode($ROL_NOMBRE) . "&cap=" . base64_encode($carpeta_CERT) . "' target='_blank' style='display: inline-block; padding: 10px 20px; background-color: #24448c; color: #fff; text-decoration: none; border-radius: 5px;'>Haz clik aqui</a>";
        $msg .= "<p style='text-align:center;'><strong>Cartimex S.A</strong></p>";
        $msg .= "</div>";


        include 'vendor/autoload.php';
        $m = new PHPMailer;
        $m->CharSet = 'UTF-8';
        $m->isSMTP();
        $m->SMTPAuth = true;
        $m->Host = 'mail.cartimex.com';
        $m->Username = 'sgo';
        $m->Password = 'sistema2021*';
        $m->SMTPSecure = 'ssl';
        $m->Port = 465;
        $m->From = 'sgo@cartimex.com';
        $m->addBCC($email);
        $m->FromName = 'Cartimex - Actualizacion de datos';
        // $m->addAddress('ktomala@cartimex.com');
        // $m->addAddress('jalvaradoe3@gmail.com');
        $m->isHTML(true);
        $fecha_rol = date('Ym', strtotime('-1 month'));
        $titulo = strtoupper('Actualizacion de datos');
        $m->Subject = $titulo;
        $m->Body = $msg;
        //$m->addAttachment($atta);
        // $m->send();
        if ($m->send()) {
            // echo "<pre>";
            // $mensaje = ("Correo enviado ");
            // echo "</pre>";
            // echo $mensaje;
            return 1;
        } else {
            //echo "Ha ocurrido un error al enviar el correo electrónico.";
            return 0;
        }
    }

    function getRealIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }
}
