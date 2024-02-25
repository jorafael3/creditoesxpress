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
            $codigo = rand(1000, 9999);
            $terminos = $param["terminos"];
            $ip = $this->getRealIP();
            $dispositivo = $_SERVER['HTTP_USER_AGENT'];

            $SI_CONSULTO = $this->Validar_si_consulto_credito($param);

            if ($SI_CONSULTO == 1) {
                $this->Anular_Codigos($param);
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
                $query->bindParam(":codigo", $codigo, PDO::PARAM_STR);
                $query->bindParam(":terminos", $terminos, PDO::PARAM_STR);
                $query->bindParam(":ip", $ip, PDO::PARAM_STR);
                $query->bindParam(":dispositivo", $dispositivo, PDO::PARAM_STR);

                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    $cel = base64_encode($celular);
                    $html = '
                        <div class="fv-row mb-10 text-center">
                            <label class="form-label fw-bold fs-2">Ingresa el código enviado a tu celular</label><br>
                            <label class="text-muted fw-bold fs-6">Verifica el número celular</label>
                            <input type="hidden" id="CEL_1" value="' . $cel . '">
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
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

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
                    // Convert the date string to a Unix timestamp
                    $formattedDateTime = new DateTime($FECHA);
                    $difference = $currentDateTime->diff($formattedDateTime);
                    $daysDifference = $difference->days;

                    // echo json_encode($daysDifference);
                    // exit();

                    $CREDITO = $result[0]["credito_aprobado"];
                    $CEDULA = $result[0]["cedula"];
                    $CORREO = $result[0]["correo"];
                    $fecha_creado = $result[0]["fecha_creado"];

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
                        $VAL_CEDULA = $this->Obtener_Datos_Cedula($parametro);
                        if ($VAL_CEDULA[0] == 1) {
                            $VAL_CREDITO = $this->Obtener_Datos_Credito($parametro);
                            if ($VAL_CREDITO[0] == 1) {
                                $DATOS_CEDULA = $VAL_CEDULA[1];
                                $DATOS_CREDITO = $VAL_CREDITO[1];

                                $nombre = $DATOS_CEDULA[0]["nombre"];
                                $fecha_nacimiento = $DATOS_CEDULA[0]["fecha_nacimiento"];
                                $codigo_dactilar = $DATOS_CEDULA[0]["codigo_dactilar"];
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


                                    if ($DATOS_CREDITO[0]["Aprobado"] == 1) {
                                        $html = '  
                                                <div class="alert alert-primary" role="alert">
                                                    <div class="p-3">
                                                        <h4 class="text-dark">Este número ya ha hecho una consulta anterior</h4>
                                                        <h4 class="text-dark">se registro con los siguientes datos:</h4>
                                                        <hr>
                                                        <h4 class="text-dark">Fecha: ' . $fecha_creado . '</h4>
                                                        <h4 class="text-dark">Cédula: ' . $CEDULA . '</h4>
                                                        <h4 class="text-dark">Correo: ' . $CORREO . '</h4>
                                                    </div> 
                                                </div> 
                                                <div class="text-center">
                                                    <h1 class="text-primary">FELICITACIONES</h1>
                                                    <h3>Usted esta apto para acceder a un credito con nosotros</h3>
                                                    <h3>un asesor se contactara con usted en breve</h3>
                                                </div>';
                                    } else {
                                        $html = '  
                                                <div class="alert alert-danger" role="alert">
                                                    <div class="p-3">
                                                        <h4 class="text-dark">Este número ya ha hecho una consulta anterior}</h4>
                                                        <h4 class="text-dark">se registro con los siguientes datos:</h4>
                                                        <hr>
                                                        <h4 class="text-dark">Fecha: ' . $fecha_creado . '</h4>
                                                        <h4 class="text-dark">Cédula: ' . $CEDULA . '</h4>
                                                        <h4 class="text-dark">Correo: ' . $CORREO . '</h4>
                                                    </div> 
                                                </div> 
                                                <div class="text-center">
                                                    <h1 class="text-danger">Usted no cumple con todos los requisitos necesarios para acceder a un credito</h1>
                                                    <h3>un asesor se contactara con usted en breve</h3>
                                                    <h3></h3>
                                                </div>';
                                    }
                                    echo json_encode([2, $DATOS_CEDULA, $DATOS_CREDITO, $html]);
                                } else {
                                    $err = $query->errorInfo();
                                    echo json_encode([0, "error al verificar información", "Intentelo de nuevo", $err]);
                                    exit();
                                }
                            } else {
                                echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", "error"]);
                                exit();
                            }
                        } else {
                            echo json_encode([0, "No se pudo realizar la verificacion", "Asegurese que la cédula ingresada sea la correcta", "error"]);
                            exit();
                        }
                    } else {

                        if ($CREDITO == 1) {
                            $html = '
                            <div class="alert alert-primary" role="alert">
                                <div class="p-3">
                                    <h4 class="text-dark">Este número ya ha hecho una consulta anterior</h4>
                                    <h4 class="text-dark">se registro con los siguientes datos:</h4>
                                    <hr>
                                    <h4 class="text-dark">Fecha: ' . $fecha_creado . '</h4>
                                    <h4 class="text-dark">Cédula: ' . $CEDULA . '</h4>
                                    <h4 class="text-dark">Correo: ' . $CORREO . '</h4>
                                </div> 
                            </div> 
                            <div class="text-center mt-3">
                                <h1 class="text-primary">FELICITACIONES</h1>
                                <h3>Usted esta apto para acceder a un credito con nosotros</h3>
                                <h3>un asesor se contactara con usted en breve</h3>
                            </div>';
                        } else {
                            $html = '  
                            <div class="alert alert-danger" role="alert">
                                <div class="p-3">
                                    <h4 class="text-dark">Este número ya ha hecho una consulta anterior}</h4>
                                    <h4 class="text-dark">se registro con los siguientes datos:</h4>
                                    <hr>
                                    <h4 class="text-dark">Fecha: ' . $fecha_creado . '</h4>
                                    <h4 class="text-dark">Cédula: ' . $CEDULA . '</h4>
                                    <h4 class="text-dark">Correo: ' . $CORREO . '</h4>
                                </div> 
                            </div> 
                            <div class="text-center mt-3">
                                <h1 class="text-danger">Usted no cumple con todos los requisitos necesarios para acceder a un credito</h1>
                                <h3>un asesor se contactara con usted en breve</h3>
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
                        <h6 class="text-muted">Este número se asociará a la cédula que ingrese</h6>
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

    //** CEDULA */

    function Validar_Cedula($param)
    {
        try {

            $VAL_CEDULA = $this->Obtener_Datos_Cedula($param);
            if ($VAL_CEDULA[0] == 1) {
                $VAL_CREDITO = $this->Obtener_Datos_Credito($param);
                if ($VAL_CREDITO[0] == 1) {
                    $DATOS_CEDULA = $VAL_CEDULA[1];
                    $DATOS_CREDITO = $VAL_CREDITO[1];
                    $cedula = trim($param["cedula"]);
                    $email = trim($param["email"]);
                    $celular = base64_decode(trim($param["celular"]));

                    $nombre = $DATOS_CEDULA[0]["nombre"];
                    $fecha_nacimiento = $DATOS_CEDULA[0]["fecha_nacimiento"];
                    $codigo_dactilar = $DATOS_CEDULA[0]["codigo_dactilar"];
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
                            <div class="text-center">
                                <h1 class="text-primary">FELICITACIONES</h1>
                                <h3>Usted esta apto para acceder a un credito con nosotros</h3>
                                <h3>un asesor se contactara con usted en breve</h3>
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
                    } else {
                        $err = $query->errorInfo();
                        echo json_encode([0, "error al verificar información", "Intentelo de nuevo", $err]);
                        exit();
                    }
                } else {
                    echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", "error"]);
                    exit();
                }
            } else {
                echo json_encode([0, "No se pudo realizar la verificacion", "Asegurese que la cédula ingresada sea la correcta", "error"]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, "No se pudo realizar la verificacion", "Intentelo de nuevo", $e]);
            exit();
        }
    }

    function Obtener_Datos_Cedula($param)
    {
        $cedula = trim($param["cedula"]);
        $ARRAY = [array(
            "nombre" => "Jorge Alvarado",
            "fecha_nacimiento" => "1994-12-04",
            "codigo_dactilar" => "vasdsw",
        )];

        if (count($ARRAY) == 0) {
            return [0, $ARRAY];
        } else {
            return [1, $ARRAY];
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


    //https://apidatoscedula20240216081841.azurewebsites.net/api/GetData?code=FXs4nBycLJmBacJWuk_olF_7thXybtYRFDDyaRGKbnphAzFuQulUlA==&id=bzj8gix/XnXTZF6EZclk7UPQxvRaupeSC1LLlsaWhRI=&emp=SALVACERO&subp=DATOSCEDULA