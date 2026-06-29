                                            <?php
                                            /**
                                             * =======================================================================
                                             * OMNIFLOW COMMAND EXECUTOR (v2.0)
                                             * =======================================================================
                                             * Proxy minimalista - Solo ejecuta comandos predefinidos
                                             * NO contiene lógica de negocio (protegida en Heroku)
                                             * 
                                             * Arquitectura: Command Pattern
                                             * - Heroku envía comandos abstractos (GET_LEAD_COUNT)
                                             * - Este archivo ejecuta queries hardcodeados
                                             * - Retorna resultados a Heroku para procesamiento
                                             */

                                            header('Content-Type: application/json; charset=utf-8');

                                            // 1. VALIDAR MÉTODO
                                            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                                                http_response_code(405);
                                                die(json_encode(['error' => 'Method not allowed']));
                                            }

                                            // 2. AUTENTICACIÓN
                                            // Heroku envía X-Auth con base64(cliente_api_key) desde tabla cliente_credenciales
                                            // Este es el ÚNICO método de autenticación válido (no usar API_SECRET_KEY local)
                                            $authHeader = $_SERVER['HTTP_X_AUTH'] ?? '';

                                            if (empty($authHeader)) {
                                                http_response_code(401);
                                                die(json_encode(['error' => 'Missing authentication header']));
                                            }

                                            // Decodificar el API key del header
                                            $provided_key = base64_decode($authHeader);

                                            if (empty($provided_key) || strlen($provided_key) < 32) {
                                                http_response_code(401);
                                                die(json_encode(['error' => 'Invalid authentication format']));
                                            }

                                            // VALIDACIÓN: Verificar API key contra tabla local (si existe tabla de clientes)
                                            // Por ahora, aceptar cualquier key válida de 128+ chars desde Heroku
                                            // TODO: Agregar validación contra tabla local de clientes autorizados
                                            if (strlen($provided_key) < 128) {
                                                http_response_code(403);
                                                die(json_encode(['error' => 'Unauthorized - Invalid API key']));
                                            }

                                            require_once __DIR__ . '/omniflow_config.php';

                                            // 3. PARSEAR COMANDO
                                            $cmd = $_POST['cmd'] ?? '';
                                            $ctx = $_POST['ctx'] ?? '{}';
                                            $ctx = is_string($ctx) ? json_decode($ctx, true) : $ctx;
                                            $ctx = $ctx ?: [];

                                            if (empty($cmd)) {
                                                http_response_code(400);
                                                die(json_encode(['error' => 'Missing command']));
                                            }

                                            // 4. FUNCIÓN DE CONEXIÓN (MANTENER DEL CONECTOR ORIGINAL)
                                            function get_client_db_connection() {
                                                $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, defined('DB_PORT') ? DB_PORT : 3306);
                                                
                                                if ($db->connect_error) {
                                                    throw new Exception('Database connection failed: ' . $db->connect_error);
                                                }
                                                
                                                $db->set_charset('utf8mb4');
                                                return $db;
                                            }

                                            // 5. CONECTAR BD
                                            try {
                                                $db = get_client_db_connection();
                                            } catch (Exception $e) {
                                                http_response_code(500);
                                                die(json_encode(['error' => $e->getMessage()]));
                                            }

                                            // 6. EJECUTAR COMANDO (WHITELIST ESTRICTA)
                                            $stmt = null;
                                            $result = null;

                                            try {
                                                switch ($cmd) {
                                                    // ===== DASHBOARD COMMANDS =====
                                                    
                                                    case 'GET_LEAD_COUNT':
                                                        // Total de leads
                                                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM procesar_formularios");
                                                        break;
                                                        
                                                    case 'GET_LEADS_BY_STATE':
                                                        // Conteo agrupado por estado
                                                        $stmt = $db->prepare("
                                                            SELECT estado, COUNT(*) as count 
                                                            FROM procesar_formularios 
                                                            GROUP BY estado
                                                        ");
                                                        break;
                                                        
                                                    case 'FETCH_LAST':
                                                        // Últimos 10 leads (sin columnas inexistentes)
                                                        $stmt = $db->prepare("
                                                            SELECT 
                                                                pf.id, 
                                                                pf.nombre, 
                                                                pf.correo, 
                                                                pf.celular,
                                                                pf.fecha_creacion, 
                                                                pf.estado
                                                            FROM procesar_formularios pf 
                                                            ORDER BY pf.fecha_creacion DESC 
                                                            LIMIT 10
                                                        ");
                                                        break;
                                                        
                                                    // ===== LEAD CRUD COMMANDS =====
                                                    
                                                    case 'GET_LEAD_DETAIL':
                                                        // Detalle completo de un lead
                                                        $lead_id = intval($ctx['id'] ?? 0);
                                                        if ($lead_id <= 0) {
                                                            throw new Exception('Invalid lead ID');
                                                        }
                                                        $stmt = $db->prepare("
                                                            SELECT pf.*, tf.nombre_formulario 
                                                            FROM procesar_formularios pf
                                                            LEFT JOIN tipos_formulario tf ON pf.id_formulario_tipo = tf.id
                                                            WHERE pf.id = ?
                                                        ");
                                                        $stmt->bind_param('i', $lead_id);
                                                        break;
                                                        
                                                    case 'INSERT_LEAD':
                                                        // Insertar nuevo lead (solo columnas que existen)
                                                        $nombre = $ctx['nombre'] ?? '';
                                                        $correo = $ctx['correo'] ?? '';
                                                        $celular = $ctx['celular'] ?? '';
                                                        $pais = $ctx['pais'] ?? '';
                                                        $estado = $ctx['estado'] ?? 'Nuevo';
                                                        $id_form = intval($ctx['id_formulario_tipo'] ?? 1);
                                                        $datos_adicionales = $ctx['datos_adicionales'] ?? '{}';
                                                        
                                                        $stmt = $db->prepare("
                                                            INSERT INTO procesar_formularios 
                                                            (nombre, correo, celular, pais, estado, id_formulario_tipo, datos_adicionales, fecha_creacion) 
                                                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                                                        ");
                                                        $stmt->bind_param('ssssssi', 
                                                            $nombre, 
                                                            $correo, 
                                                            $celular, 
                                                            $pais,
                                                            $estado,
                                                            $datos_adicionales,
                                                            $id_form
                                                        );
                                                        break;
                                                        
                                                    case 'UPDATE_LEAD_STATE':
                                                        // Actualizar estado de lead
                                                        $lead_id = intval($ctx['id'] ?? 0);
                                                        $estado = $ctx['estado'] ?? '';
                                                        
                                                        if ($lead_id <= 0 || empty($estado)) {
                                                            throw new Exception('Invalid parameters');
                                                        }
                                                        
                                                        $stmt = $db->prepare("UPDATE procesar_formularios SET estado = ? WHERE id = ?");
                                                        $stmt->bind_param('si', $estado, $lead_id);
                                                        break;
                                                        
                                                    // ===== COMANDOS FUTUROS (PLACEHOLDER) =====
                                                    
                                                    case 'GET_LEADS_FILTERED':
                                                        // TODO: Implementar cuando migremos búsqueda con filtros
                                                        throw new Exception('Command not implemented yet');
                                                        break;
                                                        
                                                    case 'REGISTER_VISIT':
                                                        // TODO: Implementar cuando migremos tracking de visitas
                                                        throw new Exception('Command not implemented yet');
                                                        break;
                                                        
                                                    default:
                                                        http_response_code(400);
                                                        throw new Exception('Unknown command: ' . $cmd);
                                                }
                                                
                                                // 7. EJECUTAR QUERY
                                                if (!$stmt) {
                    $error_msg = 'Failed to prepare statement';
                    if ($db->error) {
                        $error_msg .= ': ' . $db->error;
                    }
                    throw new Exception($error_msg);
                }
                
                $stmt->execute();
                
                // 8. PROCESAR RESULTADO SEGÚN TIPO DE QUERY
                $response = ['success' => true];
                
                if (strpos($cmd, 'GET_') === 0 || strpos($cmd, 'FETCH_') === 0) {
                    // SELECT queries - retornar datos
                    $result = $stmt->get_result();
                    $data = [];
                    
                    while ($row = $result->fetch_assoc()) {
                        $data[] = $row;
                    }
                    
                    $response['data'] = $data;
                    $response['count'] = count($data);
                    
                } elseif (strpos($cmd, 'INSERT_') === 0) {
                    // INSERT queries - retornar ID insertado
                    $response['insert_id'] = $stmt->insert_id;
                    $response['affected_rows'] = $stmt->affected_rows;
                    
                } elseif (strpos($cmd, 'UPDATE_') === 0 || strpos($cmd, 'DELETE_') === 0) {
                    // UPDATE/DELETE queries - retornar filas afectadas
                    $response['affected_rows'] = $stmt->affected_rows;
                }
                
                $stmt->close();
                $db->close();
                
                echo json_encode($response);
                                                
                                            } catch (Exception $e) {
                                                if ($stmt) $stmt->close();
                                                if ($db) $db->close();
                                                
                                                http_response_code(500);
                                                echo json_encode([
                                                    'error' => $e->getMessage(),
                                                    'command' => $cmd
                                                ]);
                                            }
                                            ?>
