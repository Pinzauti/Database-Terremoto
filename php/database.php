<?php
if (in_array('curl', get_loaded_extensions())) {
    $etichette = array("alloggi", "contatti", "fabbisogni", "donazioni");
    foreach ($etichette as $labels) {
        error_reporting(0);
        set_time_limit(120);
        ini_set('max_execution_time', 120);
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.82 Safari/537.36';
        $username = '[USERNAME]';
        $password = '[PASSWORD]';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/emergenzeHack/terremotocentro_segnalazioni/issues?per_page=1000&labels=Accettato,Form,$labels");
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept=> application/json', 'Content-Type=> application/json', 'X-Accepted-OAuth-Scopes: repo'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        $result = curl_exec($ch);
        $stripyaml = str_replace('<pre><yamldata>', '', $result);
        $contents = str_replace('</yamldata></pre>', '', $stripyaml);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status_code === 200) {
            $json = json_decode($contents, true);
            unlink("$labels.csv");
            $file = "$labels.csv";
            $message = "";
            if ($labels === "alloggi") {
                $message = "Telefono,Email,Descrizione,Indirizzo,Latidudine,Longitudine,Link,Immagine,Data,Labels,GitHub\r\n";
            } else if ($labels === "contatti") {
                $message = "Nome,Telefono,Email,Descrizione,Indirizzo,Latitudine,Longitudine,Link,Data,Labels,GitHub\r\n";
            } else if ($labels === "fabbisogni") {
                $message = "Telefono,Email,Cosa,Descrizione,Indirizzo,Latiduine,Longitudine,Link,Immagine,Data,Labels,GitHub\r\n";
            } else if ($labels === "donazioni") {
                $message = "Telefono,Email,Cosa,Descrizione,Indirizzo,Latitudine,Longitudine,Link,Immagine,Data,Labels,GitHub\r\n";
            } else {
                http_response_code(400);
                echo "Errore interno!";
                exit;
            }
            file_put_contents($file, $message, FILE_APPEND);
            foreach ($json as $key => $value) {
                $valori = str_replace('http:', 'http%3A', $value);
                $valori = str_replace('https:', 'https%3A', $valori);
                $segnalazione = explode("\n", $valori['body']);
                $issue = $valori['url'];
                $issue = str_replace('%3A', ':', $issue);
                foreach ($segnalazione as $risultato) {
                    $campi = explode(":", $risultato);
                    $sostituzione = str_replace('%3A', ':', $campi[1]);
                    $file = "$labels.csv";
                    $message = "\"$sostituzione\",";
                    file_put_contents($file, $message, FILE_APPEND);
                }
                foreach ($valori['labels'] as $etichette) {
                    $file = "$labels.csv";
                    $message = $etichette['name']. "-";
                    file_put_contents($file, $message, FILE_APPEND);
                }
                $file = "$labels.csv";
                $message = ",$issue\r\n";
                file_put_contents($file, $message, FILE_APPEND);
            }
            curl_close($ch);
            http_response_code(200);
        } else {
            http_response_code(400);
            curl_close($ch);
            echo "Non riesco a recuperare i dati!";
            exit;
        }
    }
} else {
    http_response_code(400);
    echo "CURL non è installato/attivato su questo server!";
    exit;
}

