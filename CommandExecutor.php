<?php
namespace App\Services\ConnectorV2;

/**
 * Command Executor - Envía comandos al execute.php del cliente
 * 
 * Responsabilidades:
 * - Construir request HTTP con autenticación
 * - Enviar comando + contexto al cliente
 * - Manejar timeouts y errores de red
 * - Parsear respuesta JSON
 */
class CommandExecutor {
    private $url_executor;
    private $api_key;
    private $timeout;
    
    public function __construct($cliente_url, $api_key, $timeout = 10) {
        // Construir URL del executor
        $this->url_executor = rtrim($cliente_url, '/') . '/execute.php';
        $this->api_key = $api_key;
        $this->timeout = $timeout;
    }
    
    /**
     * Ejecuta un comando en el cliente
     * 
     * @param string $command Comando a ejecutar (ej: GET_LEAD_COUNT)
     * @param array $context Parámetros adicionales (opcional)
     * @return array Respuesta del cliente
     * @throws Exception Si hay error de red o respuesta inválida
     */
    public function execute($command, $context = []) {
        // Validar comando
        if (empty($command)) {
            throw new \Exception('Command cannot be empty');
        }
        
        // Preparar payload
        $payload = [
            'cmd' => $command,
            'ctx' => json_encode($context)
        ];
        
        // Inicializar cURL con headers seguros
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->url_executor,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'X-Auth: ' . base64_encode($this->api_key),
                'User-Agent: OmniflowApp/2.0',
                'Accept: application/json'
            ]
        ]);
        
        // Ejecutar request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // Validar respuesta HTTP
        if ($response === false) {
            throw new \Exception("cURL error: $curl_error");
        }
        
        if ($http_code >= 500) {
            throw new \Exception("Server error (HTTP $http_code)");
        }
        
        if ($http_code === 403 || $http_code === 401) {
            throw new \Exception("Authentication failed (HTTP $http_code)");
        }
        
        if ($http_code >= 400) {
            throw new \Exception("Client error (HTTP $http_code): $response");
        }
        
        // Parsear JSON
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response: " . json_last_error_msg());
        }
        
        // Validar estructura de respuesta
        if (isset($data['error'])) {
            throw new \Exception("Command failed: " . $data['error']);
        }
        
        return $data;
    }
    
    /**
     * Ejecuta múltiples comandos en paralelo (futuro)
     * 
     * @param array $commands Array de [cmd, ctx] pairs
     * @return array Resultados indexados por posición
     */
    public function executeBatch($commands) {
        // TODO: Implementar con curl_multi
        throw new \Exception('Batch execution not implemented yet');
    }
    
    /**
     * Ping al cliente para verificar conectividad
     * 
     * @return bool True si el cliente responde
     */
    public function ping() {
        try {
            $result = $this->execute('GET_LEAD_COUNT');
            return isset($result['success']) && $result['success'];
        } catch (\Exception $e) {
            return false;
        }
    }
}
