<?php
function getConf()
{
    $config = parse_ini_file(key_exists("CONFIG_FILE_PATH", $_ENV) ? $_ENV["CONFIG_FILE_PATH"] : "config.ini");
    $config["auth_url"] = key_exists("AUTH_URL", $_ENV) ? $_ENV["AUTH_URL"] : $config["auth_url"];
    $config["auth_realm"] = key_exists("AUTH_REALM", $_ENV) ? $_ENV["AUTH_REALM"] : $config["auth_realm"];
    $config["auth_client_id"] = key_exists("AUTH_CLIENT_ID", $_ENV) ? $_ENV["AUTH_CLIENT_ID"] : $config["auth_client_id"];
    $config["auth_username"] = key_exists("AUTH_USERNAME", $_ENV) ? $_ENV["AUTH_USERNAME"] : $config["auth_username"];
    $config["auth_password"] = key_exists("AUTH_PASSWORD", $_ENV) ? $_ENV["AUTH_PASSWORD"] : $config["auth_password"];
    $config["cm_url"] = key_exists("CM_URL", $_ENV) ? $_ENV["CM_URL"] : $config["cm_url"];
    $config["cm_key"] = key_exists("CM_KEY", $_ENV) ? $_ENV["CM_KEY"] : $config["cm_key"];
    return $config;
}

function getToken()
{
    $config = getConf();
    $auth_url = $config["auth_url"];
    $auth_realm = $config["auth_realm"];
    $auth_client_id = $config["auth_client_id"];
    $auth_username = $config["auth_username"];
    $auth_password = $config["auth_password"];

    $curl_url = $auth_url . "/realms/" . $auth_realm . "/protocol/openid-connect/token";
    $curl_postfields = "grant_type=password&client_id=" . $auth_client_id .
        "&username=" . urlencode($auth_username) .
        "&password=" . urlencode($auth_password);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $curl_url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_postfields);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    curl_errno($curl) > 0 && error_log("while getting token : " . curl_error($curl));
    curl_close($curl);

    return json_decode($response)->access_token;
}

function getPayload()
{
    $payload = file_get_contents(key_exists("PAYLOAD_FILE_PATH", $_ENV) ? $_ENV["PAYLOAD_FILE_PATH"] : "payload.json");
    return json_decode($payload, true);
}

function getFormUrl()
{
    $token = getToken();
    $config = getConf();
    $cm_key =  $config["cm_key"];

    $context = getPayload();
    $context["subject"] = array_key_exists("uuid", $_GET) ? $_GET["uuid"] : uniqid();

    $curl_url = $config["cm_url"] . "/consents";
    $curl_postfields = json_encode($context);
    if(!empty($cm_key)){
        $curl_httpheaders = array("CM-Key: $cm_key", "Content-Type: application/json");
    } else {
        $curl_httpheaders = array("Authorization: Bearer $token", "Content-Type: application/json");
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $curl_url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_postfields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $curl_httpheaders);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADERFUNCTION,
        function ($curl, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        }
    );
    curl_exec($curl);
    curl_errno($curl) > 0 && error_log("while getting form : " . curl_error($curl));
    curl_close($curl);
    return $headers["location"][0];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Simple Consent Integration v2 Example</title>
    <script type="text/javascript" src="handlers.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/4.2.11/iframeResizer.js"></script>

    <style>
        body {
                background-image: url('https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
        }
        iframe.seamless {
                background-color: transparent;
                border: 0px none transparent;
                padding: 0px;
                overflow: hidden;
                width: 900px;
        }
        div.iframe-container {
                display: grid;
                place-items: center;
        }
    </style>
</head>
<body>
    <div class=iframe-container>
        <h2 style="text-align: center">Simple Consent v2 Integration Example</h2>
        <iframe src="<?php echo getFormUrl() ?>"
                width="100%"
                title="Simple Consent v2 Integration Example Iframe"
                id="consent"
                name="consent"
                onload="initIframeResizer('#consent');"
                class=seamless
        ></iframe>
    </div>
</body>
</html>
