<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor RPG - Prolog & Laravel</title>
    <style>
        body { background-color: #1a1a2e; color: #e2e8f0; font-family: 'Courier New', Courier, monospace; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #16213e; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        h1 { color: #ffffff; text-align: center; background: #e94560; padding: 10px; border-radius: 4px; margin-top: 0; font-size: 22px; }
        .console { background: #0f3460; padding: 15px; border-radius: 5px; min-height: 150px; margin-bottom: 20px; border-left: 5px solid #e94560; }
        .input-group { display: flex; gap: 10px; }
        input[type="text"] { flex: 1; padding: 10px; background: #1a1a2e; border: 1px solid #e94560; color: #fff; border-radius: 4px; font-size: 16px; font-family: inherit; }
        button { padding: 10px 20px; background: #e94560; border: none; color: white; cursor: pointer; border-radius: 4px; font-weight: bold; font-family: inherit; }
        button:hover { background: #ff6b81; }
        .help { background: #1a1a2e; padding: 15px; margin-top: 20px; border-radius: 4px; font-size: 14px; color: #8a99ad; line-height: 1.6; }
        ul { padding-left: 20px; }
        code { background: #16213e; padding: 2px 6px; border-radius: 3px; color: #ff6b81; }
    </style>
</head>
<body>

<div class="container">
    <h1>⚔️ Motor de Juego
    <p style="text-align: right; margin-top: -10px;">
        <a href="{{ route('game.docs') }}" style="color: #ff6b81; font-weight: bold; text-decoration: none;">📖 Ver documentación lógica del proyecto &rarr;</a>
    </p>

    <div class="console">
        @if(session('comando_previo'))
            <p style="color: #8a99ad; margin-bottom: 5px;">> {{ session('comando_previo') }}</p>
        @endif

        @if(session('resultado'))
            <p style="color: #4eed50; font-size: 18px; margin-top: 5px;"><strong>Resultado:</strong> {{ session('resultado') }}</p>
        @endif

        @if(session('error'))
            <p style="color: #e94560; margin-top: 5px;"><strong>Error:</strong> {{ session('error') }}</p>
        @endif

        @if(!session('resultado') && !session('error'))
            <p style="color: #8a99ad;">Esperando instrucciones del jugador...</p>
        @endif
    </div>

    <form action="{{ route('game.query') }}" method="POST">
        @csrf
        <div class="input-group">
            <input type="text" name="comando" placeholder="Escribe un comando... (Ej: atacar Elara Orca)" autofocus autocomplete="off">
            <button type="submit">Enviar</button>
        </div>
    </form>

    <div class="help">
        <h3 style="margin-top: 0; color: #fff;">📜 Comandos Disponibles:</h3>
        <ul>
            <li><code>atacar [Personaje] [Enemigo]</code> <br> Ejemplo: <code>atacar Elara Orca</code></li>
            <li><code>ataque_grupal [P1] [P2] [Enemigo]</code> <br> Ejemplo: <code>ataque_grupal Elara Kael Ballena</code></li>
            <li><code>preparacion [Personaje] [MisionID] [Enemigo]</code> <br> Ejemplo: <code>preparacion Elara m2 Orca</code></li>
            <li><code>rango [Personaje]</code> <br> Ejemplo: <code>rango Lyra</code></li>
            <li><code>peligro [Personaje]</code> <br> Ejemplo: <code>peligro Doran</code></li>
            <li><code>crear [Nombre]</code> <br> Crea un nuevo personaje con nivel y vida aleatorios. <br> Ejemplo: <code>crear Thalia</code></li>
            <li><code>equipar [Personaje] [Arma]</code> <br> Equipa un arma valida (espada, escudo, pocion, arco, flechas, varita, grimorio, amuleto, alvin). <br> Ejemplo: <code>equipar Thalia espada</code></li>
            <li><code>desequipar [Personaje] [Arma]</code> <br> Quita un arma equipada dinamicamente. <br> Ejemplo: <code>desequipar Thalia espada</code></li>
            <li><code>inventario [Personaje]</code> <br> Muestra las armas equipadas y la fuerza total. <br> Ejemplo: <code>inventario Thalia</code></li>
        </ul>
    </div>
</div>

</body>
</html>
code></li>
        </ul>
    </div>
</div>

</body>
</html>>
    </div>
</div>

</body>
</html>>
    </div>
</div>

</body>
</html>