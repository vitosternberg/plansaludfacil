<?php
/**
 * api/cotizar.php
 * Endpoint que recibe datos del lead y devuelve recomendaciones del motor Python.
 * 
 * Método: POST
 * Body (JSON): { "nombre": "...", "edad": 27, "renta": 1300000, "cargas": 0, "intereses": [...], "uf_value": 38500 }
 * 
 * Respuesta (JSON): { "recomendaciones": [...], "cotizacion_legal_7pct": 91000, ... }
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa POST.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Leer input JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['renta'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos del lead. Campos requeridos: renta.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Valores por defecto
$lead = [
    'nombre'      => $input['nombre'] ?? 'Usuario',
    'edad'        => (int)($input['edad'] ?? 30),
    'renta'       => (int)($input['renta'] ?? 1000000),
    'cargas'      => (int)($input['cargas'] ?? 0),
    'uf_value'    => (int)($input['uf_value'] ?? 38500),
    'intereses'   => $input['intereses'] ?? ['Hospitalización', 'Atención Ambulatoria'],
    'edad_cargas' => $input['edad_cargas'] ?? null,
];

// Ejecutar motor PHP (sin dependencia de Python — compatible con shared hosting)
require_once __DIR__ . '/../core/cotizador_engine.php';

$result = motor_cotizar($lead);

if (isset($result['error'])) {
    http_response_code(500);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// Enriquecer con datos adicionales para el frontend
$result['timestamp'] = date('c');
$result['total_planes_evaluados'] = 2231;

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
