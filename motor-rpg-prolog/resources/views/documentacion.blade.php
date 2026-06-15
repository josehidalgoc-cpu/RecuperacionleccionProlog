<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación - Motor RPG Prolog</title>
    <style>
        body { background-color: #1a1a2e; color: #e2e8f0; font-family: 'Courier New', Courier, monospace; padding: 20px; line-height: 1.6; }
        .container { max-width: 950px; margin: 0 auto; background: #16213e; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        h1 { color: #ffffff; text-align: center; background: #e94560; padding: 10px; border-radius: 4px; margin-top: 0; font-size: 22px; }
        h2 { color: #ff6b81; border-bottom: 2px solid #0f3460; padding-bottom: 6px; margin-top: 35px; font-size: 19px; }
        h3 { color: #4eed50; margin-top: 20px; font-size: 16px; }
        p { color: #c4cfe0; }
        code { background: #0f3460; padding: 2px 6px; border-radius: 3px; color: #ff6b81; }
        pre { background: #0f3460; padding: 12px; border-radius: 5px; overflow-x: auto; border-left: 5px solid #e94560; font-size: 13px; color: #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 14px; }
        th, td { border: 1px solid #0f3460; padding: 8px 10px; text-align: left; }
        th { background: #0f3460; color: #fff; }
        tr:nth-child(even) { background: #1f2b4d; }
        .badge { display: inline-block; background: #0f3460; color: #4eed50; padding: 2px 8px; border-radius: 3px; font-size: 12px; margin-right: 6px; }
        .back-link { display: inline-block; margin-bottom: 15px; color: #ff6b81; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
        .nota { background: #1a1a2e; border-left: 5px solid #4eed50; padding: 10px 15px; margin-top: 10px; border-radius: 4px; font-size: 13px; color: #8a99ad; }
    </style>
</head>
<body>

<div class="container">
    <a href="{{ route('game.index') }}" class="back-link">&larr; Volver al juego</a>

    <h1>📖 Documentación Lógica del Proyecto</h1>
    <p>
        Esta página resume las reglas de Prolog que conforman la base de conocimiento del juego,
        agrupadas por bloque, junto con los nuevos comportamientos agregados para
        personajes, enemigos, misiones y manejo de armas.
    </p>

    <h2>1. Base de conocimiento (hechos)</h2>
    <p>Datos fijos que describen el mundo del juego:</p>
    <table>
        <tr><th>Predicado</th><th>Significado</th></tr>
        <tr><td><code>personaje(Nombre, Nivel, Vida)</code></td><td>Define un personaje jugable con su nivel y puntos de vida.</td></tr>
        <tr><td><code>enemigo(Nombre, Vida)</code></td><td>Define un enemigo y su cantidad de vida.</td></tr>
        <tr><td><code>tipo_enemigo(Nombre, Tipo)</code></td><td>Clasifica al enemigo como <code>normal</code>, <code>elite</code> o <code>jefe</code>.</td></tr>
        <tr><td><code>mision(ID, Nombre, Dificultad, XP)</code></td><td>Define una misión, su dificultad (nivel requerido) y su recompensa en XP.</td></tr>
        <tr><td><code>recompensa_oro(ID, Oro)</code></td><td>Define el oro adicional otorgado por completar una misión.</td></tr>
        <tr><td><code>inventario(Personaje, Lista)</code></td><td>Inventario base/original de cada personaje.</td></tr>
        <tr><td><code>weaponforce(Arma, Fuerza)</code></td><td>Catálogo de armas válidas y su poder de ataque.</td></tr>
        <tr><td><code>requiere(MisionID, Objeto)</code></td><td>Objetos de inventario necesarios para completar una misión.</td></tr>
    </table>

    <h2>2. Reglas aritméticas y recursivas (base original)</h2>

    <h3>2.1 puede_aceptar/2</h3>
    <pre>puede_aceptar(Personaje,ID_Mision):-
    personaje(Personaje, Nivel,_),
    mision(ID_Mision,_,Dificultad,_),
    Nivel>=Dificultad.</pre>
    <p>Compara el nivel del personaje contra la dificultad de la misión usando el operador relacional <code>&gt;=</code>.</p>

    <h3>2.2 xp_acumulada/2 (recursiva)</h3>
    <pre>xp_acumulada(0,0).
xp_acumulada(N,Total):-
    N>0, N1 is N-1,
    xp_acumulada(N1,Prev),
    Total is Prev + (30*N).</pre>
    <p>Patrón recursivo tipo factorial: caso base en 0, paso recursivo que acumula <code>30*N</code> por cada misión.</p>

    <h3>2.3 tiene_requerido/2</h3>
    <p>Usa <code>member/2</code> para verificar si un objeto está dentro del inventario de un personaje.</p>

    <h2>3. Unificación y comparación</h2>
    <table>
        <tr><th>Regla</th><th>Lógica</th></tr>
        <tr><td><code>mismo_nivel(P1,P2)</code></td><td>Unifica el nivel de dos personajes distintos (<code>\==</code> evita comparar un personaje consigo mismo).</td></tr>
        <tr><td><code>es_balanceado(Personaje)</code></td><td>Usa comparación aritmética estricta <code>=:=</code> para verificar si la vida es exactamente 100.</td></tr>
    </table>

    <h2>4. Procesamiento de listas y NLP</h2>
    <table>
        <tr><th>Regla</th><th>Lógica</th></tr>
        <tr><td><code>fusionar_equipo(P1,P2,Equipo)</code></td><td>Usa <code>append/3</code> para combinar los inventarios de dos personajes.</td></tr>
        <tr><td><code>conjugar_accion(Verbo,Tiempo,Persona,Numero,Conj)</code></td><td>Estructura condicional <code>-&gt;/;</code> que conjuga el verbo "ser" según tiempo/persona/número, o devuelve el verbo tal cual si no es "ser".</td></tr>
        <tr><td><code>sumar_niveles(P1,P2,Total)</code></td><td>Suma aritmética de los niveles de dos personajes.</td></tr>
        <tr><td><code>generar_reporte(P1,P2,MisionID,Mensaje)</code></td><td>Combina varias reglas (<code>todos_pueden</code>, <code>sumar_niveles</code>, <code>conjugar_accion</code>) y usa <code>atomic_list_concat/3</code> para armar una oración en español.</td></tr>
    </table>

    <h2>5. Sistema de combate (taller original)</h2>
    <table>
        <tr><th>Regla</th><th>Lógica</th></tr>
        <tr><td><code>fuerza_total(Personaje,Total)</code></td><td>Suma la fuerza de todas las armas del inventario usando <code>findall/3</code> + <code>sumlist/2</code>.</td></tr>
        <tr><td><code>execute_singleplayer_attack/3</code></td><td>Compara fuerza vs vida del enemigo (<code>&gt;=</code>) y genera el mensaje de victoria/derrota con conjugación singular.</td></tr>
        <tr><td><code>excecute_grupal_attack/4</code></td><td>Igual que el anterior pero sumando la fuerza de dos personajes y usando conjugación plural.</td></tr>
    </table>

    <hr style="border-color:#0f3460; margin: 30px 0;">

    <h1 style="background:#0f3460;">🆕 Nuevas reglas agregadas en este taller</h1>

    <h2>6. Comportamiento de jugadores</h2>

    <h3>6.1 rango_jugador/2</h3>
    <pre>rango_jugador(Personaje, novato)     :- Nivel =< 4.
rango_jugador(Personaje, intermedio) :- Nivel > 4, Nivel =< 6.
rango_jugador(Personaje, veterano)   :- Nivel > 6.</pre>
    <p>Clasifica al personaje en 3 categorías según su nivel, usando comparaciones aritméticas encadenadas.</p>

    <h3>6.2 en_peligro/1</h3>
    <p>Verifica si la vida del personaje es menor a 80 (<code>Vida &lt; 80</code>). Usado por el comando <code>peligro</code>.</p>

    <h3>6.3 estrategia_recomendada/3</h3>
    <pre>estrategia_recomendada(Personaje, MisionID, solo)  :- Fuerza >= Dificultad * 10.
estrategia_recomendada(Personaje, MisionID, grupo) :- Fuerza <  Dificultad * 10.</pre>
    <p>Compara la fuerza total del personaje contra la dificultad de la misión multiplicada por un factor, decidiendo si conviene ir solo o en grupo.</p>

    <h2>7. Comportamiento de enemigos</h2>

    <h3>7.1 peligrosidad/2</h3>
    <pre>peligrosidad(Enemigo, baja)  :- Vida =< 60.
peligrosidad(Enemigo, media) :- Vida > 60, Vida =< 200.
peligrosidad(Enemigo, alta)  :- Vida > 200.</pre>
    <p>Clasifica al enemigo en 3 niveles de peligro según su vida.</p>

    <h3>7.2 puede_vencer_solo/2</h3>
    <p>Compara <code>fuerza_total(Personaje)</code> contra <code>enemigo(Vida)</code> con <code>&gt;=</code>. Es la base lógica usada en <code>execute_singleplayer_attack</code> y en el reporte de preparación.</p>

    <h3>7.3 turnos_para_vencer/3</h3>
    <pre>Turnos is ceiling(Vida / Fuerza).</pre>
    <p>Calcula cuántos "golpes" (turnos) necesita un personaje para derrotar a un enemigo, usando la función aritmética <code>ceiling/1</code> para redondear hacia arriba.</p>

    <h2>8. Misiones</h2>

    <h3>8.1 mision_completa_inventario/2</h3>
    <pre>mision_completa_inventario(Personaje, MisionID):-
    forall(requiere(MisionID, Objeto), tiene_requerido(Personaje, Objeto)).</pre>
    <p>Usa <code>forall/2</code> para verificar que <strong>todos</strong> los objetos requeridos por la misión estén en el inventario del personaje (no solo uno).</p>

    <h3>8.2 recompensa_total/4</h3>
    <p>Combina <code>puede_aceptar/2</code> con los hechos <code>mision/4</code> y <code>recompensa_oro/2</code> para devolver tanto XP como oro de una misión.</p>

    <h3>8.3 mejor_mision/2</h3>
    <pre>mejor_mision(Personaje, MejorMisionID):-
    findall(XP-ID, (puede_aceptar(Personaje, ID), mision(ID,_,_,XP)), Pares),
    sort(Pares, Ordenados),
    last(Ordenados, _-MejorMisionID).</pre>
    <p>Usa <code>findall/3</code> para listar todas las misiones que el personaje puede aceptar junto con su XP, las ordena con <code>sort/2</code> (orden ascendente por el primer elemento del par) y toma la última (mayor XP) con <code>last/2</code>.</p>

    <h2>9. Reglas integradoras</h2>

    <h3>9.1 reporte_preparacion/4</h3>
    <p>
        Combina <code>puede_aceptar/2</code> y <code>puede_vencer_solo/2</code> dentro de dos estructuras
        condicionales (<code>-&gt;/;</code>) para generar un mensaje único que indica si el jugador
        está listo para la misión y si puede vencer al enemigo asociado en solitario.
    </p>

    <h2>10. Creación de personajes (sistema dinámico)</h2>

    <div class="nota">
        Para que los datos persistan entre ejecuciones (recordar que cada consulta lanza un proceso
        <code>swipl</code> nuevo que termina con <code>halt</code>), se usa <code>assertz/1</code> para
        modificar la base de conocimiento en memoria, y además se escribe el hecho nuevo al archivo
        <code>jugadores_extra.pl</code>, el cual se carga automáticamente al iniciar
        (<code>:- catch(consult('jugadores_extra.pl'), _, true).</code>).
    </div>

    <h3>10.1 personaje/3 e inventario/2 como dynamic</h3>
    <pre>:- dynamic personaje/3.
:- dynamic inventario/2.
:- dynamic posee/2.</pre>
    <p>Declarar un predicado como <code>dynamic</code> permite usar <code>assertz/1</code> (agregar hechos) y <code>retract/1</code> (quitar hechos) sobre él en tiempo de ejecución.</p>

    <h3>10.2 existe_personaje/1</h3>
    <p>Verifica si ya hay un <code>personaje/3</code> con ese nombre, para evitar duplicados.</p>

    <h3>10.3 crear_personaje/3 y crear_personaje_persistente/3</h3>
    <pre>crear_personaje(Nombre, Nivel, Vida):-
    \+ existe_personaje(Nombre),
    assertz(personaje(Nombre, Nivel, Vida)).

crear_personaje_persistente(Nombre, Nivel, Vida):-
    crear_personaje(Nombre, Nivel, Vida),
    open('jugadores_extra.pl', append, Stream),
    format(Stream, "personaje('~w',~w,~w).~n", [Nombre, Nivel, Vida]),
    close(Stream).</pre>
    <p>
        <code>\+</code> es la negación por falla: la regla solo tiene éxito si <strong>no</strong> existe
        ya un personaje con ese nombre. El nivel y la vida los genera Laravel de forma aleatoria
        (nivel 1-10, vida 50-150) y se pasan como parámetros a la consulta Prolog.
    </p>

    <h2>11. Sistema de armas / inventario dinámico</h2>

    <h3>11.1 posee/2</h3>
    <p>
        Nuevo predicado dinámico que almacena, por separado del <code>inventario/2</code> original,
        las armas que un personaje equipa en tiempo de ejecución. Esto evita tener que reescribir
        listas completas: cada arma equipada es un hecho independiente
        <code>posee('Thalia','espada')</code>.
    </p>

    <h3>11.2 inventario_completo/2</h3>
    <pre>inventario_completo(Personaje, ListaCompleta):-
    ( inventario(Personaje, Base) -> true ; Base = [] ),
    findall(Arma, posee(Personaje, Arma), Extra),
    append(Base, Extra, ListaCompleta).</pre>
    <p>
        Une el inventario base (si existe; si no, lista vacía gracias al condicional <code>-&gt;/;</code>)
        con todas las armas dinámicas obtenidas vía <code>findall/3</code> sobre <code>posee/2</code>,
        combinándolas con <code>append/3</code>.
    </p>

    <h3>11.3 arma_valida/1</h3>
    <p>Verifica que el arma exista en el catálogo <code>weaponforce/2</code> antes de permitir equiparla.</p>

    <h3>11.4 equipar_arma/2</h3>
    <pre>equipar_arma(Personaje, Arma):-
    existe_personaje(Personaje),
    arma_valida(Arma),
    assertz(posee(Personaje, Arma)),
    open('jugadores_extra.pl', append, Stream),
    format(Stream, "posee('~w','~w').~n", [Personaje, Arma]),
    close(Stream).</pre>
    <p>
        Encadena tres validaciones (personaje existe, arma válida) antes de modificar la base de
        conocimiento. Igual que con la creación de personajes, se persiste el hecho en
        <code>jugadores_extra.pl</code>.
    </p>

    <h3>11.5 desequipar_arma/2</h3>
    <p>
        Usa <code>retract/1</code> para eliminar una ocurrencia de <code>posee(Personaje, Arma)</code>
        de la memoria del proceso actual. <span class="badge">Nota</span> No reescribe el archivo
        persistente; si se necesita que sea permanente habría que regenerar
        <code>jugadores_extra.pl</code> completo desde los hechos en memoria.
    </p>

    <h3>11.6 fuerza_total/2 (actualizado)</h3>
    <pre>fuerza_total(Personaje, Total) :-
    ( inventario(Personaje, Base) -> true ; Base = [] ),
    findall(Arma, posee(Personaje, Arma), Extra),
    append(Base, Extra, Lista),
    findall(F, (member(Arma2, Lista), weaponforce(Arma2, F)), Fuerzas),
    sumlist(Fuerzas, Total).</pre>
    <p>
        Se modificó la regla original para que combine inventario base + armas dinámicas antes de
        sumar las fuerzas. Esto hace que <code>atacar</code>, <code>ataque_grupal</code> y
        <code>preparacion</code> reflejen automáticamente las armas equipadas con el nuevo comando.
    </p>

    <h3>11.7 reporte_inventario/2</h3>
    <p>
        Regla de presentación: usa <code>inventario_completo/2</code> y <code>fuerza_total/2</code>,
        y con un condicional decide el texto según si la lista está vacía o no, formateando con
        <code>atomic_list_concat/3</code>.
    </p>

    <h2>12. Mapa de comandos del chatbot → predicados Prolog</h2>
    <table>
        <tr><th>Comando Laravel</th><th>Predicado(s) Prolog usados</th></tr>
        <tr><td><code>atacar P E</code></td><td><code>execute_singleplayer_attack/3</code> → <code>fuerza_total/2</code>, <code>conjugar_accion/5</code></td></tr>
        <tr><td><code>ataque_grupal P1 P2 E</code></td><td><code>excecute_grupal_attack/4</code> → <code>fuerza_total/2</code> (x2), <code>conjugar_accion/5</code></td></tr>
        <tr><td><code>preparacion P M E</code></td><td><code>reporte_preparacion/4</code> → <code>puede_aceptar/2</code>, <code>puede_vencer_solo/2</code></td></tr>
        <tr><td><code>rango P</code></td><td><code>rango_jugador/2</code></td></tr>
        <tr><td><code>peligro P</code></td><td><code>en_peligro/1</code></td></tr>
        <tr><td><code>crear Nombre</code></td><td><code>crear_personaje_persistente/3</code> → <code>existe_personaje/1</code>, <code>assertz/1</code></td></tr>
        <tr><td><code>equipar P Arma</code></td><td><code>equipar_arma/2</code> → <code>existe_personaje/1</code>, <code>arma_valida/1</code>, <code>assertz/1</code></td></tr>
        <tr><td><code>desequipar P Arma</code></td><td><code>desequipar_arma/2</code> → <code>retract/1</code></td></tr>
        <tr><td><code>inventario P</code></td><td><code>reporte_inventario/2</code> → <code>inventario_completo/2</code>, <code>fuerza_total/2</code></td></tr>
    </table>

</div>

</body>
</html>
