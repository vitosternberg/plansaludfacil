#!/usr/bin/env python3
"""
POC: Motor de Cotización Plan Salud Fácil
Simula la evaluación de planes de isapre para el último registro de la BD.
"""

import json, sys

# ─── Perfil del lead (ID 103: Kathya Andrade) ───
lead = {
    "id": 103,
    "nombre": "Kathya Andrade Olivares",
    "edad": 27,
    "renta": 1300000,
    "comuna": "Valparaíso",
    "cargas": 0,
    "sistema_actual": "Nueva Masvida",
    "intereses": ["Salud Mental", "Kinesiología y Deporte", "Telemedicina", "Excedentes"],
    "preexistencias": False,
}

# ─── Catálogo de planes simulados ───
planes = [
    {
        "nombre": "Plan Más Salud Total",
        "isapre": "Banmédica",
        "precio": 91000,
        "coberturas": {
            "ambulatorio": 90, "hospitalizacion": 95, "maternidad": 80,
            "dental": 50, "farmacia": 40, "kinesiologia": 90,
            "telemedicina": 100, "excedentes": 70, "salud_mental": 85
        },
        "tipo_red": "preferente",
        "copago": 3500
    },
    {
        "nombre": "Full Life 3000",
        "isapre": "Cruz Blanca",
        "precio": 78000,
        "coberturas": {
            "ambulatorio": 80, "hospitalizacion": 85, "maternidad": 70,
            "dental": 60, "farmacia": 50, "kinesiologia": 95,
            "telemedicina": 100, "excedentes": 80, "salud_mental": 50
        },
        "tipo_red": "libre",
        "copago": 4500
    },
    {
        "nombre": "Plan Vital Esencial",
        "isapre": "Colmena",
        "precio": 65000,
        "coberturas": {
            "ambulatorio": 70, "hospitalizacion": 80, "maternidad": 60,
            "dental": 30, "farmacia": 25, "kinesiologia": 50,
            "telemedicina": 60, "excedentes": 40, "salud_mental": 90
        },
        "tipo_red": "cerrada",
        "copago": 5000
    },
    {
        "nombre": "Óptimo Joven",
        "isapre": "Consalud",
        "precio": 71000,
        "coberturas": {
            "ambulatorio": 85, "hospitalizacion": 90, "maternidad": 75,
            "dental": 45, "farmacia": 35, "kinesiologia": 85,
            "telemedicina": 100, "excedentes": 65, "salud_mental": 80
        },
        "tipo_red": "preferente",
        "copago": 3800
    },
    {
        "nombre": "Premium Global",
        "isapre": "Nueva Masvida",
        "precio": 105000,
        "coberturas": {
            "ambulatorio": 95, "hospitalizacion": 100, "maternidad": 90,
            "dental": 70, "farmacia": 60, "kinesiologia": 80,
            "telemedicina": 70, "excedentes": 30, "salud_mental": 60
        },
        "tipo_red": "libre",
        "copago": 2500
    },
]

# ─── Mapeo de intereses a coberturas ───
interes_a_cobertura = {
    "Salud Mental": "salud_mental",
    "Kinesiología y Deporte": "kinesiologia",
    "Telemedicina": "telemedicina",
    "Excedentes": "excedentes",
    "Atención Ambulatoria": "ambulatorio",
    "Hospitalización": "hospitalizacion",
    "Maternidad": "maternidad",
    "Dental": "dental",
    "Farmacia": "farmacia",
}

# ─── Motor de puntuación ───
def score_plan(plan, lead):
    score = 0
    reasons = []
    coberturas = plan["coberturas"]
    
    # 1. Puntuar según intereses del lead (peso: 60% del score)
    total_interes = 0
    for interes in lead["intereses"]:
        key = interes_a_cobertura.get(interes)
        if key and key in coberturas:
            cov = coberturas[key]
            total_interes += cov
            if cov >= 90:
                reasons.append(f"Excelente cobertura en {interes.lower()} ({cov}%)")
            elif cov >= 70:
                reasons.append(f"Buena cobertura en {interes.lower()} ({cov}%)")
    
    avg_interes = total_interes / len(lead["intereses"]) if lead["intereses"] else 0
    score += avg_interes * 0.60
    
    # 2. Puntuar por precio vs cotización legal 7% (peso: 25%)
    # El 7% va íntegro a la isapre. No hay excedentes para el afiliado.
    # Plan ideal: precio cercano al 7% sin requerir cotización adicional.
    cotizacion_legal = lead["renta"] * 0.07
    if plan["precio"] <= cotizacion_legal:
        score += 25
        reasons.append(f"Tu cotización legal de 7% (${int(cotizacion_legal):,}) cubre el valor del plan")
    elif plan["precio"] <= cotizacion_legal * 1.15:
        score += 15
        extra = int(plan["precio"] - cotizacion_legal)
        reasons.append(f"Requiere ${extra:,} de cotización adicional mensual sobre tu 7%")
    else:
        score += 5
        extra = int(plan["precio"] - cotizacion_legal)
        reasons.append(f"Requiere ${extra:,} de cotización adicional mensual sobre tu 7%")
    
    # 3. Bonus por edad joven con plan de red libre/preferente (peso: 10%)
    if lead["edad"] < 35 and plan["tipo_red"] in ("libre", "preferente"):
        score += 10
        reasons.append(f"Red {plan['tipo_red']} ideal para tu edad (mayor libertad de elección)")
    
    # 4. Bonus por sin cargas → plan individual optimizado (peso: 5%)
    if lead["cargas"] == 0 and plan["precio"] < 90000:
        score += 5
        reasons.append("Plan individual optimizado sin costos por cargas")
    
    return round(score, 1), reasons


# ─── Ejecutar evaluación ───
print("=" * 70)
print("  PLAN SALUD FÁCIL — Motor de Cotización (POC)")
print("=" * 70)
print()
print(f"  Lead:  {lead['nombre']} (#{lead['id']})")
print(f"  Edad:  {lead['edad']} años")
print(f"  Renta: ${lead['renta']:,}")
print(f"  Comuna: {lead['comuna']}")
print(f"  Cargas: {lead['cargas']}")
print(f"  Intereses: {', '.join(lead['intereses'])}")
print(f"  7% legal: ${int(lead['renta'] * 0.07):,}")
print()

# Evaluar todos los planes
resultados = []
for plan in planes:
    s, razones = score_plan(plan, lead)
    resultados.append((plan, s, razones))

resultados.sort(key=lambda x: x[1], reverse=True)
mejor = resultados[0]

print("─" * 70)
print(f"  🏆 PLAN MÁS AFÍN: {mejor[0]['nombre']} ({mejor[0]['isapre']})")
print(f"     Afinidad: {mejor[1]:.1f}/100")
print(f"     Precio:   ${mejor[0]['precio']:,}/mes")
print(f"     Red:      {mejor[0]['tipo_red'].capitalize()}")
print(f"     Copago:   ${mejor[0]['copago']:,}")
print()
print("  ¿Por qué este plan?")
for r in mejor[2]:
    print(f"     ✓ {r}")
print()

print("─" * 70)
print("  🔄 OTRAS ALTERNATIVAS")
print()
for i, (plan, score, razones) in enumerate(resultados[1:4], 1):
    diff = mejor[1] - score
    print(f"  {i}. {plan['nombre']} ({plan['isapre']})")
    print(f"     Precio: ${plan['precio']:,}/mes | Afinidad: {score:.1f}/100 | {diff:.1f} pts bajo el mejor")
    # Mejor cobertura de este plan
    best_cov = max(plan['coberturas'], key=lambda k: plan['coberturas'][k])
    print(f"     Destaca en: {best_cov.replace('_',' ').title()} ({plan['coberturas'][best_cov]}%)")
    print(f"     Red: {plan['tipo_red'].capitalize()} | Copago: ${plan['copago']:,}")
    print()

print("─" * 70)
print("  📊 COMPARATIVA DE COBERTURAS DE INTERÉS")
print()
header = f"  {'Cobertura':<22}"
for plan, _, _ in resultados[:3]:
    header += f" {plan['nombre'][:18]:<20}"
print(header)
print("  " + "-" * (22 + 20*3))
for interes in lead['intereses']:
    key = interes_a_cobertura.get(interes)
    if key:
        row = f"  {interes:<22}"
        for plan, _, _ in resultados[:3]:
            cov = plan['coberturas'].get(key, '-')
            bar = '█' * (cov // 10)
            row += f" {bar:<10} {cov}%".ljust(20)
        print(row)

print()
print("=" * 70)
print("  POC completada. El motor asignó planes simulados basados en:")
print("  - Intereses de cobertura del lead (60% del score)")
print("  - Precio vs cotización legal del 7% (25% del score)")
print("  - Edad y tipo de red (10% del score)")
print("  - Optimización por cargas (5% del score)")
print("=" * 70)
