<?php
// components/comunas_options.php

$comunas_str = "Alto Hospicio;Ancud;Angol;Antofagasta;Arica;Batuco;Calama;Calera de Tango;Cartagena;Castro;Cauquenes;Cerrillos;Cerro Navia;Chiguayante;Chillan;Coihaique;Colina;ConCon;Concepcion;Conchali;Constitucion;Copiapo;Coquimbo;Curico;El Bosque;El Salvador;Estacion Central;Huechuraba;Illapel;Independencia;Iquique;Isla de Maipo;La Calera;La Cruz;La Ligua;La Pintana;La Reina;La Serena;La Union;Labranza;Lampa;Las Condes;Limache;Linares;Llanquihue;Lo Barnechea;Lo Prado;Los Andes;Los Angeles;Machali;Maipu;Malloco;Melipilla;Nogales;Nunoa;Osorno;Ovalle;Padre Hurtado;Parral;Pedro Aguirre Cerda;Penaflor;Penco;Providencia;Pudahuel;Puerto Montt;Puerto Varas;Punta Arenas;Putaendo;Quilicura;Quillon;Quillota;Quilpue;Quintero;Quinta Normal;Rancagua;Recoleta;Renca;Rengo;San Antonio;San Carlos;San Esteban;San Felipe;San Fernando;San Joaquin;San Miguel;San Pedro de la Paz;Santa Maria;Santiago Centro;Santo Domingo;Talagante;Talca;Talcahuano;Temuco;Tocopilla;Valdivia;Vallenar;Valparaiso;Victoria;Villa Alemana;Vina Del Mar;Vitacura;Quellon";
$comunas = array_filter(array_map('trim', explode(';', $comunas_str)));
sort($comunas);

echo '<option value="">Selecciona tu comuna</option>';
foreach ($comunas as $comuna) {
    echo '<option value="' . htmlspecialchars($comuna) . '">' . htmlspecialchars($comuna) . '</option>';
}
?>
