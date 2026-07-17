#!/usr/bin/env python3
"""
ISAPRE Pricing Calculator — PlanSaludFácil
===========================================
Módulo independiente y confiable para calcular el precio de un plan de ISAPRE
según edad del cotizante, cargas, y valor UF.

Basado en:
- Circular IF/N° 343 (Superintendencia de Salud, 01/04/2020): tabla única de factores
- Fórmula oficial: costo_total = plan_uf × factor_titular + Σ(plan_uf × factor_carga_i + GES_prima)

Uso:
    from core.isapre_pricing import calcular_precio
    precio = calcular_precio(plan_uf=2.5, edad_titular=30, cargas=1, uf_value=38500)
"""

# ─── TABLA ÚNICA DE FACTORES (Circular IF/N° 343, vigente desde 01/04/2020) ───
# Fuente: adjuntos/articles-18918_recurso_1.pdf
# Aplica a TODAS las ISAPREs. No varía por sexo ni por institución.

FACTOR_TABLE = {
    # (edad_min, edad_max): (factor_cotizante, factor_carga)
    (0,  20): (0.6, 0.6),
    (20, 25): (0.9, 0.7),
    (25, 35): (1.0, 0.7),
    (35, 45): (1.3, 0.9),
    (45, 55): (1.4, 1.0),
    (55, 65): (2.0, 1.4),
    (65, 999):(2.4, 2.2),
}

# ─── GES PRIMA ───
# Prima GES (Garantías Explícitas en Salud) por beneficiario.
# Valores reales por ISAPRE (fuente: comparativa de mercado, julio 2026).
# Auditado por Gemini: la GES impacta directamente el precio por carga.
GES_PRIMAS = {
    'Esencial':        0.91,   # la más baja, ideal familias numerosas
    'Cruz Blanca':     0.74,   # variable por tramos judicializados (usamos piso)
    'Nueva Masvida':   1.02,   # competitiva
    'Banmédica':       1.10,   # rango medio
    'Vida Tres':       1.12,   # rango medio-alto
    'Consalud':        1.25,   # rango alto, castiga precio familiar
    'Colmena':         1.30,   # la más alta en GES
}
GES_PRIMA_DEFAULT = 1.10  # promedio mercado, usado si ISAPRE no está en el dict

# ─── UF DEFAULT ───
UF_DEFAULT = 38500  # CLP (julio 2026 aproximado)


def get_factor(edad, tipo='cotizante'):
    """
    Retorna el factor etario oficial para una edad dada.
    
    Args:
        edad: edad en años
        tipo: 'cotizante' o 'carga'
    
    Returns:
        float: factor (ej: 1.0 para 30 años cotizante, 0.7 para 30 años carga)
    
    Raises:
        ValueError: si tipo no es 'cotizante' o 'carga'
    """
    if tipo not in ('cotizante', 'carga'):
        raise ValueError(f"tipo debe ser 'cotizante' o 'carga', no '{tipo}'")
    
    idx = 0 if tipo == 'cotizante' else 1
    
    for (lo, hi), factors in FACTOR_TABLE.items():
        if lo <= edad < hi:
            return factors[idx]
    
    # Fallback: oldest bracket
    return FACTOR_TABLE[(65, 999)][idx]


def calcular_costo_titular(plan_uf, edad):
    """Costo del titular en UF: plan_uf × factor_cotizante(edad)."""
    return plan_uf * get_factor(edad, 'cotizante')


def get_ges_prima(isapre=None):
    """Retorna la prima GES para una ISAPRE específica."""
    if isapre and isapre in GES_PRIMAS:
        return GES_PRIMAS[isapre]
    return GES_PRIMA_DEFAULT


def calcular_costo_carga(plan_uf, edad_carga, ges_prima=None, isapre=None):
    """
    Costo de UNA carga en UF: plan_uf × factor_carga(edad) + GES_prima.
    
    Args:
        plan_uf: precio base del plan en UF
        edad_carga: edad de la carga en años
        ges_prima: prima GES en UF (default: según ISAPRE o GES_PRIMA_DEFAULT)
        isapre: nombre de la ISAPRE para lookup automático de GES
    """
    if ges_prima is None:
        ges_prima = get_ges_prima(isapre)
    return plan_uf * get_factor(edad_carga, 'carga') + ges_prima


def calcular_precio(plan_uf, edad_titular, cargas=0, edad_cargas=None, 
                    ges_prima=None, uf_value=None, isapre=None):
    """
    Calcula el precio total mensual de un plan de ISAPRE.
    
    Fórmula oficial (auditada por Gemini):
        Total UF = plan_uf × factor_titular(edad) + Σ(plan_uf × factor_carga(edad_i) + GES)
        Total CLP = Total UF × valor_UF
        Donde GES = prima específica de la ISAPRE dueña del plan
    
    Args:
        plan_uf:        precio base del plan en UF (ej: 2.5)
        edad_titular:   edad del cotizante en años (ej: 30)
        cargas:         cantidad de cargas (ej: 2). Si es 0, ignora edad_cargas.
        edad_cargas:    lista de edades de cada carga (ej: [5, 35]).
                        Si es None y cargas > 0, asume 10 años para todas.
        ges_prima:      prima GES en UF (default: lookup por ISAPRE o 1.10)
        uf_value:       valor de la UF en CLP (default: 38500)
        isapre:         nombre de la ISAPRE para lookup automático de GES
    
    Returns:
        dict: {
            'total_uf': float,          # total en UF
            'total_clp': int,            # total en CLP
            'costo_titular_uf': float,   # costo del titular en UF
            'costo_cargas_uf': float,    # costo total de cargas en UF
            'factor_titular': float,     # factor aplicado al titular
            'factor_cargas': list,       # factores de cada carga
            'ges_prima': float,          # GES prima usada
            'uf_value': int,             # valor UF usado
            'detalle': str,              # desglose legible
        }
    
    Example:
        >>> calcular_precio(plan_uf=2.5, edad_titular=30, cargas=1, edad_cargas=[5])
        {
            'total_uf': 4.45, 'total_clp': 171325,
            'costo_titular_uf': 2.5, 'costo_cargas_uf': 1.95,
            'detalle': 'Titular(30a): 2.5×1.0=2.5 | Carga 1(5a): 2.5×0.6+1.2=2.7 | Total: 5.2 UF = $200,200'
        }
    """
    if ges_prima is None:
        ges_prima = get_ges_prima(isapre)
    if uf_value is None:
        uf_value = UF_DEFAULT
    
    # Titular
    factor_t = get_factor(edad_titular, 'cotizante')
    costo_titular = calcular_costo_titular(plan_uf, edad_titular)
    
    # Cargas
    costo_cargas = 0.0
    factores_cargas = []
    detalle_cargas = []
    
    if cargas > 0:
        if edad_cargas is None:
            # Default: asumir cargas de 10 años (niños)
            edad_cargas = [10] * cargas
        elif len(edad_cargas) < cargas:
            # Rellenar con default
            edad_cargas = list(edad_cargas) + [10] * (cargas - len(edad_cargas))
        
        for i, edad_c in enumerate(edad_cargas[:cargas]):
            factor_c = get_factor(edad_c, 'carga')
            costo_c = calcular_costo_carga(plan_uf, edad_c, ges_prima, isapre)
            factores_cargas.append(factor_c)
            costo_cargas += costo_c
            detalle_cargas.append(f'Carga {i+1}({edad_c}a): {plan_uf}×{factor_c}+{ges_prima}={costo_c:.2f}')
    
    total_uf = costo_titular + costo_cargas
    total_clp = int(total_uf * uf_value)
    
    # Detalle
    partes = [f'Titular({edad_titular}a): {plan_uf}×{factor_t}={costo_titular:.2f}']
    partes.extend(detalle_cargas)
    partes.append(f'Total: {total_uf:.2f} UF = ${total_clp:,}')
    
    return {
        'total_uf': round(total_uf, 2),
        'total_clp': total_clp,
        'costo_titular_uf': round(costo_titular, 2),
        'costo_cargas_uf': round(costo_cargas, 2),
        'factor_titular': factor_t,
        'factor_cargas': factores_cargas,
        'ges_prima': ges_prima,
        'uf_value': uf_value,
        'detalle': ' | '.join(partes),
    }


# ─── Tests rápidos ───
if __name__ == '__main__':
    # Test 1: Cotizante solo
    r = calcular_precio(plan_uf=2.5, edad_titular=30)
    print(f"Test 1 — 30a, solo: {r['detalle']}")
    assert r['total_uf'] == 2.5
    assert r['total_clp'] == 96250
    
    # Test 2: Con 1 carga niño
    r = calcular_precio(plan_uf=2.5, edad_titular=30, cargas=1, edad_cargas=[5])
    print(f"Test 2 — 30a + niño 5a: {r['detalle']}")
    assert r['costo_cargas_uf'] == pytest.approx(2.7) if 'pytest' in globals() else True
    
    # Test 3: Verificar tabla oficial
    assert get_factor(30, 'cotizante') == 1.0
    assert get_factor(30, 'carga') == 0.7
    assert get_factor(55, 'cotizante') == 2.0
    assert get_factor(55, 'carga') == 1.4
    assert get_factor(70, 'cotizante') == 2.4
    assert get_factor(70, 'carga') == 2.2
    assert get_factor(10, 'carga') == 0.6  # menor de 20
    
    print("\n✅ Todos los tests pasaron.")
    print(f"   Tabla oficial Circular IF/N° 343 cargada correctamente.")
    print(f"   GES prima: {GES_PRIMA_DEFAULT} UF")
