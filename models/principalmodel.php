<?php


// require_once "models/logmodel.php";



class principalmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Validar_Celular($param)
    {
        try {
            $celular = trim($param["celular"]);
            $codigo = rand(1000, 9999);
            $terminos = $param["terminos"];
            $ip = $this->getRealIP();
            $dispositivo = $_SERVER['HTTP_USER_AGENT'];

            $this->Anular_Codigos($param);

            $query = $this->db->connect_dobra()->prepare('INSERT INTO solo_telefonos 
            (
                numero, 
                codigo, 
                terminos, 
                ip, 
                dispositivo
            ) 
            VALUES(
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
                        <label class="form-label fw-bold fs-2">Ingresa el codigo Enviado a tu celular</label><br>
                        <label class="text-muted fw-bold fs-6">Verifica el numero</label>
                        <input type="hidden" id="CEL_1" value="' . $cel . '">
                    </div>
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-6">
                            <div class="verification-code">
                                <input type="text" maxlength="1" class="form-control code-input" />
                                <input type="text" maxlength="1" class="form-control code-input" />
                                <input type="text" maxlength="1" class="form-control code-input" />
                                <input type="text" maxlength="1" class="form-control code-input" />
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
        } catch (PDOException $e) {

            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

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
                    <input id="CEDULA" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
                </div>
                <div class="fv-row mb-10">
                    <label class="form-label d-flex align-items-center">
                        <span class="required fw-bold fs-2">Correo (opcional)</span>
                    </label>
                    <input id="CORREO" type="text" class="form-control form-control-solid" name="input1" placeholder="" value="" />
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





    function Validar_Cedula($param)
    {
        // $c = $this->CONVERT_($param["cedula"]);
        // return $c;
        // echo json_encode($c);
        // exit();
        try {
            $cedula = $param["cedula"];
            $query = $this->db->connect_dobra()->prepare('{CALL SGO_Consulta_ActualizacionDatos (?) }');
            $query->bindParam(1, $cedula, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    $NOMBRE = explode(" ", $result[0]["Nombres"]);
                    $NOMBRE_FINAL = [];
                    for ($i = 0; $i < count($NOMBRE); $i++) {
                        // print_r($n);
                        if (strlen($NOMBRE[$i]) <= 2) {
                            $string = $NOMBRE[$i];
                            $n = $string[0] . "*";
                        } else if (strlen($NOMBRE[$i]) == 3) {
                            $string = $NOMBRE[$i];
                            $n = $string[0] . "**";
                        } else {
                            $n = $this->CONVERT_($NOMBRE[$i]);
                        }
                        array_push($NOMBRE_FINAL, $n);
                        // $NOMBRE_FINAL = $NOMBRE_FINAL . " " . $this->CONVERT_($n); 0957374382
                    }

                    // foreach ($result as $key => $value) {
                    //     if ($key != "Celular" || $key != "Cédula" || $key != "Email" || $key != "Nombres") {
                    //     } else {
                    //         print_r($value);
                    //         $NOMBRE =  $NOMBRE . $value;
                    //     }
                    // }

                    $email = $result[0]["Email"];
                    $EMAIL_TOTAL = "";
                    if ($email != "") {
                        $email = explode("@", $email);
                        $email1 = $this->CONVERT_E($email[0]);
                        $email2 = $this->CONVERT_E($email[1]);
                        $EMAIL_TOTAL =  $email1 . "@" . $email2;
                    }
                    $CELULAR = $result[0]["Celular"];
                    if ($CELULAR == "") {
                        $CELULAR = $this->CONVERT_C($result[0]["Celular"]);
                    }
                    $ARRAY = [
                        array(
                            "Nombre" => implode(" ", $NOMBRE_FINAL),
                            "Celular" => $CELULAR,
                            "Email" => $EMAIL_TOTAL,
                        )
                    ];

                    // echo json_encode($ARRAY);
                    // exit();
                    if (count($result) > 0) {
                        $INJ = '
                        <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">Nombres</span>
                            </label>
                            <input disabled autocomplete="off" onkeypress="return soloLetras(event)" onblur="limpia()" id="nombres" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                        </div> 
                        <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">Email</span>
                            </label>
                            <input autocomplete="off" id="email" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                        </div>
    
                        <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">Teléfono</span>
                            </label>
                            <input onkeypress="return valideKey(event);" maxlength="10" id="telefono" required type="text" class="form-control form-control-solid" placeholder="" name="cedula">
                        </div>
                       
                        <div class="fv-row mb-8 fv-plugins-icon-container">
                            <label class="form-check form-check-inline">
                                <input checked id="check_pd" class="form-check-input" type="checkbox" name="toc" value="1">
                                <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1 fs-7">
                                He leído y acepto el tratamiendo de datos personales, recibir información 
                                personalizada, descuentos y promociones exclusivas de CARTIMEX por cualquier medio.</span>
                            </label>
                        </div>
                        <div class="fv-row mb-8 fv-plugins-icon-container">
                            <label class="form-check form-check-inline">
                                <input checked id="check_g" class="form-check-input" type="checkbox" name="toc" value="1">
                                <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">
                                Términos y Condiciones</a></span>
                                <a href="#!"   onclick="openmodal1()" class="ms-1 link-primary">
                                Términos y Políticas de Privacidad*</a></span>
                            </label>
                        </div>
    
                        <div class="text-center">
                            <button onclick="Guardar_datos()" type="submit" id="kt_modal_new_target_submit" class="btn btn-primary" name="n">
                                <span class="indicator-label">Continuar </span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
    
                            ';

                        echo json_encode([$ARRAY, $INJ]);
                        exit();
                    } else {
                        echo json_encode([]);
                        exit();
                    }
                    // $this->Generador_pdf();
                } else {
                    echo json_encode(["La cédula no es correcta o no existe", "error"]);
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
