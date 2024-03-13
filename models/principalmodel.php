<?php

// require_once "models/logmodel.php";

class principalmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //*** CELULAR */

    function Validar_Celular($param)
    {
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
                    $fecha_creado = $result[0]["fecha_creado"];

                    // echo json_encode([$result,$daysDifference]);
                    // exit();

                    $parametro = array(
                        "cedula" => $CEDULA
                    );

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

                    if ($daysDifference >= 5) {
                        // $VAL_CEDULA = $this->Obtener_Datos_Cedula($parametro);

                        // if ($VAL_CEDULA[0] == 1) {
                        $VAL_CREDITO = $this->Obtener_Datos_Credito($parametro);
                        if ($VAL_CREDITO[0] == 1) {
                            // $DATOS_CEDULA = $VAL_CEDULA[1];
                            $DATOS_CREDITO = $VAL_CREDITO[1];

                            $nombre = $result[0]["nombre_cliente"];
                            $fecha_nacimiento = $result[0]["fecha_nacimiento"];
                            $codigo_dactilar = $result[0]["codigo_dactilar"];
                            $ip = $this->getRealIP();
                            $dispositivo = $_SERVER['HTTP_USER_AGENT'];

                            $credito_aprobado = $DATOS_CREDITO[0]["Aprobado"];

                            $query = $this->db->connect_dobra()->prepare('INSERT INTO 
                                creditos_solicitados
                                    (
                                        cedula, 
                                        numero, 
                                        correo,
                                        nombre_cliente, 
                                        fecha_nacimiento, 
                                        codigo_dactilar,
                                        credito_aprobado,
                                        ip,
                                        dispositivo
                                    ) 
                                    VALUES
                                    (
                                        :cedula, 
                                        :numero, 
                                        :correo, 
                                        :nombre_cliente, 
                                        :fecha_nacimiento, 
                                        :codigo_dactilar, 
                                        :credito_aprobado,
                                        :ip,
                                        :dispositivo
                                    );
                                    ');
                            $query->bindParam(":cedula", $CEDULA, PDO::PARAM_STR);
                            $query->bindParam(":numero", $celular, PDO::PARAM_STR);
                            $query->bindParam(":correo", $CORREO, PDO::PARAM_STR);
                            $query->bindParam(":nombre_cliente", $nombre, PDO::PARAM_STR);
                            $query->bindParam(":fecha_nacimiento", $fecha_nacimiento, PDO::PARAM_STR);
                            $query->bindParam(":codigo_dactilar", $codigo_dactilar, PDO::PARAM_STR);
                            $query->bindParam(":credito_aprobado", $credito_aprobado, PDO::PARAM_STR);
                            $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                            $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);

                            if ($query->execute()) {
                                $result = $query->fetchAll(PDO::FETCH_ASSOC);
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
                                // <div class="text-center">
                                //     <h1 class="text-primary">FELICITACIONES</h1>
                                //     <h3>Usted esta apto para acceder a un credito con nosotros</h3>
                                //     <h3>un asesor se contactara con usted en breve</h3>
                                // </div>';
                                $link = constant("URL") . "/public/img/SV24 - Mensajes LC_Proceso.png";
                                $html = '
                                <div class="text-center mt-3">
                                    <img style="width: 100%;" src="' . $link . '" alt="">
                                </div>';
                                // if ($DATOS_CREDITO[0]["Aprobado"] == 1) {
                                //     $html = '
                                //         <div class="text-center mt-3">
                                //             <img style="width: 100%;" src="' . $link . '" alt="">
                                //         </div>';
                                // } else {
                                //     $html = '  
                                //                 <div class="text-center">
                                //                     <h1 class="text-danger">Usted no cumple con todos los requisitos necesarios para acceder a un credito</h1>
                                //                     <h3>un asesor se contactara con usted en breve</h3>
                                //                     <h3></h3>
                                //                 </div>';
                                // }
                                echo json_encode([2, $result, $DATOS_CREDITO, $html]);
                                exit();
                            } else {
                                $err = $query->errorInfo();
                                echo json_encode([0, "error al verificar información", "Intentelo de nuevo", $err]);
                                exit();
                            }
                        } else {
                            echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", "error"]);
                            exit();
                        }
                        // } else {
                        //     echo json_encode([0, "No se pudo realizar la verificacion", "Asegurese que la cédula ingresada sea la correcta", "error"]);
                        //     exit();
                        // }
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
                        $html = '
                        <div class="text-center mt-3">
                            <img style="width: 100%;" src="' . $link . '" alt="">
                        </div>';
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
            $link = constant("URL") . "/public/img/SV24 - Mensajes LC_Proceso.png";

            $VAL_CONSULTA = $this->Validar_Cedula_Ya_Consulto($param);
            // echo json_encode([$VAL_CONSULTA]);
            // exit();
            if ($VAL_CONSULTA[0] == 1) {
                $VAL_CEDULA_ = $this->Validar_si_cedula_existe($param);
                // echo json_encode([$param]);
                // exit();
                if ($VAL_CEDULA_ == 0) {
                } else {
                    // $VAL_CEDULA = $this->consulta_api_cedula();
                    $VAL_CEDULA = $this->Obtener_Datos_Cedula($param);
                    // echo json_encode($VAL_CEDULA);
                    if ($VAL_CEDULA[0] == 1) {
                        $VAL_CREDITO = $this->Obtener_Datos_Credito($param);
                        if ($VAL_CREDITO[0] == 1) {
                            $DATOS_CEDULA = $VAL_CEDULA[1];
                            $DATOS_CREDITO = $VAL_CREDITO[1];
                            $cedula = trim($param["cedula"]);
                            $email = trim($param["email"]);
                            $celular = base64_decode(trim($param["celular"]));

                            $nombre = $DATOS_CEDULA[0]->NOMBRES;
                            $fecha_nacimiento = $DATOS_CEDULA[0]->FECHA_NACIM;
                            $codigo_dactilar = $DATOS_CEDULA[0]->INDIVIDUAL_DACTILAR;
                            $CANT_DOM = $DATOS_CEDULA[0]->CANT_DOM;
                            $ip = $this->getRealIP();
                            $dispositivo = $_SERVER['HTTP_USER_AGENT'];
                            $credito_aprobado = $DATOS_CREDITO[0]["Aprobado"];

                            $query = $this->db->connect_dobra()->prepare('UPDATE creditos_solicitados
                                SET
                                    numero = :numero, 
                                    correo = :correo,
                                    nombre_cliente = :nombre_cliente, 
                                    fecha_nacimiento = :fecha_nacimiento, 
                                    codigo_dactilar = :codigo_dactilar,
                                    credito_aprobado = :credito_aprobado,
                                    ip = :ip,
                                    dispositivo = :dispositivo
                                WHERE cedula = :cedula
                                ');
                            $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
                            $query->bindParam(":numero", $celular, PDO::PARAM_STR);
                            $query->bindParam(":correo", $email, PDO::PARAM_STR);
                            $query->bindParam(":nombre_cliente", $nombre, PDO::PARAM_STR);
                            $query->bindParam(":fecha_nacimiento", $fecha_nacimiento, PDO::PARAM_STR);
                            $query->bindParam(":codigo_dactilar", $codigo_dactilar, PDO::PARAM_STR);
                            $query->bindParam(":credito_aprobado", $credito_aprobado, PDO::PARAM_STR);
                            $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                            $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);

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


                                if ($DATOS_CREDITO[0]["Aprobado"] == 1) {
                                    $html = '
                                    <div class="text-center mt-3">
                                        <img style="width: 100%;" src="' . $link . '" alt="">
                                    </div>';
                                } else {
                                    $html = '  
                                    <div class="text-center">
                                        <h1 class="text-danger">Usted no cumple con todos los requisitos necesarios para acceder a un credito</h1>
                                        <h3>un asesor se contactara con usted en breve</h3>
                                        <h3></h3>
                                    </div>';
                                }
                                echo json_encode([1, $DATOS_CEDULA, $DATOS_CREDITO, $html]);
                                exit();
                            } else {
                                $err = $query->errorInfo();
                                echo json_encode([0, "error al verificar información", "Intentelo de nuevo", $err]);
                                exit();
                            }
                        } else {
                            echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", "error"]);
                            exit();
                        }
                    } else if ($VAL_CEDULA[0] == 0) {
                        $this->ELiminar_Cedulas_No_existen($param);
                        echo json_encode([0, "No se pudo realizar la verificacion", "Asegureseo que la cédula ingresada sea la correcta", "error", $VAL_CEDULA]);
                        exit();
                    } else {

                        $cedula = trim($param["cedula"]);
                        $email = trim($param["email"]);
                        $celular = base64_decode(trim($param["celular"]));
                        $ip = $this->getRealIP();
                        $dispositivo = $_SERVER['HTTP_USER_AGENT'];

                        $query = $this->db->connect_dobra()->prepare('UPDATE creditos_solicitados
                        SET
                            numero = :numero, 
                            correo = :correo,
                            ip = :ip,
                            dispositivo = :dispositivo,
                            estado = 2
                        WHERE cedula = :cedula
                        ');
                        $query->bindParam(":cedula", $cedula, PDO::PARAM_STR);
                        $query->bindParam(":numero", $celular, PDO::PARAM_STR);
                        $query->bindParam(":correo", $email, PDO::PARAM_STR);
                        $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                        $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);
                        if ($query->execute()) {

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
                            $html = '
                                <div class="text-center mt-3">
                                    <img style="width: 100%;" src="' . $link . '" alt="">
                                </div>';
                            echo json_encode([1, [], [], $html]);
                            exit();
                        }
                    }
                }
            } else {
                echo json_encode([0, $VAL_CONSULTA[1], "Asegureseo que la cédula ingresada sea la correcta", "error"]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", $e]);
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
                        return [0, "Esta cédula esta asociado a otro número queya realizo una consulta"];
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
            // sleep(4);
            $cedula = trim($param["cedula"]);
            $arr = "";
            while (true) {
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
                            // echo json_encode($en[1]);
                            // exit();
                            // if ($en[0] == 1) {
                            //     $arr = $en[1];
                            //     break;
                            // }
                        } else {
                            continue;
                        }
                    }
                }
                return [0, "INTENTE DE NUEVO"];
            }
        } catch (PDOException $e) {

            $e = $e->getMessage();
            return [0, "INTENTE DE NUEVO"];
        }
    }

    function Obtener_Datos_Credito($param)
    {
        $cedula = trim($param["cedula"]);
        $ARRAY = [array(
            "Aprobado" => 1,
            "motivo" => "Cumple los requisitos",
        )];
        return [1, $ARRAY];
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
        $msg .= "<img src='https://www.cartimex.com/assets/img/logo200.png' width='200' height='100' style='display:block; margin: 0 auto;'> <br><br>";
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
