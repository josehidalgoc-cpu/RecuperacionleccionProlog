<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class GameController extends Controller
{
    // 1. Muestra la interfaz del juego
    public function index()
    {
        return view('game');
    }

    // 1.1 Muestra la pagina de documentacion logica del proyecto
    public function documentacion()
    {
        return view('documentacion');
    }

    // 2. Recibe el comando del formulario, lo traduce y consulta a Prolog
    public function query(Request $request)
    {
        $request->validate([
            'comando' => 'required|string'
        ]);

        $input = $request->input('comando');

        // Convertimos el texto del usuario a un predicado de Prolog
        $prologQuery = $this->parseCommand($input);

        if (!$prologQuery) {
            return back()->with('error', 'Comando no reconocido. Intenta con: "atacar Elara Orca"');
        }

        // Ruta exacta de tu base de conocimiento
        $scriptPath = storage_path('app/prolog/recuperacionProlog.pl');

        // Comando para ejecutar SWI-Prolog en la terminal de tu sistema
        // -q evita mensajes de bienvenida, -g ejecuta la consulta, -t halt. cierra Prolog al terminar
        $command = "\"C:\\Archivos de programa\\swipl\\bin\\swipl.exe\" -q -s \"{$scriptPath}\" -g \"{$prologQuery}\" -t halt.";

        // Ejecutamos el proceso en el sistema operativo
        $result = Process::run($command);

        if ($result->successful()) {
            $output = trim($result->output());
            return back()->with([
                'resultado' => $output ? $output : "Consulta ejecutada con éxito (True).",
                'comando_previo' => $input
            ]);
        }

        // Si SWI-Prolog falla o no está instalado/en el PATH, saltará este error
        return back()->with('error', 'Error en el motor Prolog: ' . $result->errorOutput());
    }

    // 3. Traductor: Mapea lo que escribes en el chat con tus reglas de Prolog
    private function parseCommand($input)
    {
        // Limpiamos espacios y dividimos el comando por palabras
        $parts = explode(' ', preg_replace('/\s+/', ' ', trim($input)));
        $accion = strtolower($parts[0] ?? '');

        switch ($accion) {
            case 'atacar':
                // Ej: "atacar Elara Orca" -> ejecuta el ataque individual y escribe el mensaje final
                if (count($parts) >= 3) {
                    $personaje = $parts[1];
                    // Juntamos el resto de las palabras con un espacio
                    $enemigo = implode(' ', array_slice($parts, 2));
                    return "execute_singleplayer_attack('{$personaje}', '{$enemigo}', R), write(R)";
                }
                break;

            case 'ataque_grupal':
                // Ej: "ataque_grupal Elara Kael Orca"
                if (count($parts) >= 4) {
                    $p1 = $parts[1];
                    $p2 = $parts[2];
                    // Agarramos desde la cuarta palabra (índice 3) en adelante y las unimos con espacios
                    $enemigo = implode(' ', array_slice($parts, 3));
                    return "excecute_grupal_attack('{$p1}', '{$p2}', '{$enemigo}', R), write(R)";
                }
                break;

            case 'preparacion':
                // Ej: "preparacion Elara m2 Orca"
                if (count($parts) >= 4) {
                    $personaje = $parts[1];
                    $mision = $parts[2];
                    $enemigo = implode(' ', array_slice($parts, 3));
                    return "reporte_preparacion('{$personaje}', '{$mision}', '{$enemigo}', R), write(R)";
                }
                break;

            case 'rango':
                // Ej: "rango Elara"
                if (count($parts) >= 2) {
                    return "rango_jugador('{$parts[1]}', R), write(R)";
                }
                break;

            case 'peligro':
                // Ej: "peligro Kael" (Maneja un "if/else" rápido en Prolog para retornar texto)
                if (count($parts) >= 2) {
                    return "(en_peligro('{$parts[1]}') -> write('¡Sí, está en peligro!') ; write('No, está a salvo.'))";
                }
                break;

            case 'crear':
                // Ej: "crear Thalia" -> crea un personaje nuevo con nivel y vida aleatorios
                if (count($parts) >= 2) {
                    // Permite nombres con espacios: "crear Lady Thalia"
                    $nombre = implode(' ', array_slice($parts, 1));

                    // Generamos nivel y vida aleatorios en PHP (no en Prolog)
                    $nivel = random_int(1, 10);
                    $vida  = random_int(50, 150);

                    // Si el personaje ya existe, devolvemos un mensaje claro
                    // crear_personaje_persistente falla (false) si el nombre ya existe
                    return "(crear_personaje_persistente('{$nombre}', {$nivel}, {$vida}) "
                         . "-> format('Personaje creado: ~w (Nivel: ~w, Vida: ~w)', ['{$nombre}', {$nivel}, {$vida}]) "
                         . "; write('Ya existe un personaje con ese nombre.'))";
                }
                break;

            case 'equipar':
                // Ej: "equipar Thalia espada" -> agrega un arma al inventario del personaje
                if (count($parts) >= 3) {
                    $personaje = $parts[1];
                    // El arma puede tener espacios? por convencion las armas son una sola palabra
                    $arma = $parts[2];
                    return "(equipar_arma('{$personaje}', '{$arma}') "
                         . "-> format('~w equipo: ~w', ['{$personaje}', '{$arma}']) "
                         . "; write('No se pudo equipar. Verifica que el personaje y el arma existan.'))";
                }
                break;

            case 'desequipar':
                // Ej: "desequipar Thalia espada" -> quita un arma equipada dinamicamente
                if (count($parts) >= 3) {
                    $personaje = $parts[1];
                    $arma = $parts[2];
                    return "(desequipar_arma('{$personaje}', '{$arma}') "
                         . "-> format('~w ya no tiene equipado: ~w', ['{$personaje}', '{$arma}']) "
                         . "; write('No se encontro esa arma equipada dinamicamente para ese personaje.'))";
                }
                break;

            case 'inventario':
                // Ej: "inventario Thalia" -> muestra armas equipadas y fuerza total
                if (count($parts) >= 2) {
                    $personaje = implode(' ', array_slice($parts, 1));
                    return "(reporte_inventario('{$personaje}', R) -> write(R) ; write('Personaje no encontrado.'))";
                }
                break;
        }

        return null; // Si no entra en ningún caso, el comando no existe
    }
}
