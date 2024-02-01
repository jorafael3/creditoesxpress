<?php


class Principal extends Controller
{

    function __construct()
    {

        parent::__construct();
    }
    function render()
    {

        $this->view->render('principal/nueva');
    }

    function render_Ac()
    {

        $this->view->render('principal/actualizado');
    }

    function Guardar_datos()
    {
        global $globalVar;
        // if (isset($_POST["n"])) {
        //     $ced = $_POST["cedula"];
        //     print_r($ced);
        //     echo $ced;

        //     $this->view->ced = $ced;
        // } else {
        //     $this->view->render('principal/nueva');
        // }

        // $this->view->render('principal/nueva');
        $array = json_decode(file_get_contents("php://input"), true);
        // var_dump($array);

        // if (($array["email"]) == null || ($array["email"]) == "") {
        //     echo json_encode(["Debe ingresar email", "error"]);
        // } else  if (trim($array["telefono"]) == null || trim($array["telefono"]) == "") {
        //     echo json_encode(["Debe ingresar numero de teléfono", "error"]);
        // } else  
        if (($array["check_g"]) == false) {
            echo json_encode(["Debe aceptar los términos y condiciones para continuar", "error"]);
        } else {





            if (trim($array["email"]) == "") {

                if (trim($array["telefono"]) == "") {
                    $Ventas =  $this->model->Validar_Actualizacion($array);
                    echo json_encode($Ventas);
                } else {
                    $TEL = $this->validateEcuadorianCellphone($array["telefono"]);
                    if ($TEL == 0) {
                        echo json_encode(["El teléfono no tiene un formato valido", "error"]);
                    } else {
                        $Ventas =  $this->model->Validar_Actualizacion($array);
                        echo json_encode($Ventas);
                    }
                }
            } else {
                $EMAIL = $this->is_valid_email($array["email"]);
                if ($EMAIL == false) {
                    echo json_encode(["El correo no tiene un formato valido", "error"]);
                } else {
                    if (trim($array["telefono"]) == "") {
                        $Ventas =  $this->model->Validar_Actualizacion($array);
                        echo json_encode($Ventas);
                    } else {
                        $TEL = $this->validateEcuadorianCellphone($array["telefono"]);
                        if ($TEL == 0) {
                            echo json_encode(["El teléfono no tiene un formato valido", "error"]);
                        } else {
                            $Ventas =  $this->model->Validar_Actualizacion($array);
                            echo json_encode($Ventas);
                        }
                    }
                    //$Ventas =  $this->model->Validar_Actualizacion($array);
                    //echo json_encode($Ventas);
                }
            }



            // echo json_encode(["asd","success"]);
            // if ($Ventas[1] == "success") {
            //     $this->view->render('principal/actualizado');
            // } else {
            //     echo json_encode($Ventas);
            // }
        }
        // $l = constant("url");
        // header("Location: " . $l);
        // echo $this->render_Ac();

        //$this->CrecimientoCategoriasIndex();
    }
    function is_valid_email($str)
    {
        $matches = null;
        return (1 === preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $str, $matches));
    }
    function validateEcuadorianCellphone($cellphone)
    {
        // Regular expression pattern for a valid Ecuadorian cellphone number
        $pattern = '/^(09|\+5939)\d{8}$/';

        // Check if the provided cellphone number matches the pattern
        return preg_match($pattern, $cellphone);
    }

    function Validar_Cedula()
    {
        global $globalVar;
        $array = json_decode(file_get_contents("php://input"), true);
        if (trim($array["cedula"]) == null || trim($array["cedula"]) == "") {
            echo json_encode(["Debe ingresar un numero de ruc / Cédula", "error"]);
        } else {
            $length = strlen(trim($array["cedula"]));
            if ($length >= 10 && $length <= 13) {
                if (ctype_digit(trim($array["cedula"]))) {
                    $_SESSION["CED"] = "";
                    $_SESSION["CED"] = trim($array["cedula"]);
                    $Ventas =  $this->model->Validar_Cedula($array);
                } else {
                    echo json_encode(["La cédula solo debe ser numérica", "error"]);
                }
            } else {
                echo json_encode(["La cédula ingresada no tiene la cantidad de numeros correcta", "error"]);
            }
        }
    }
}
