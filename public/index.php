<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$arquivo = __DIR__ . '/../personagens.json';

// Criar o arquivo JSON caso ainda não exista
if (!file_exists($arquivo)) {
    $personagensIniciais = [
        [
            'id' => 1,
            'nome' => 'Kairon',
            'classe' => 'Caçador de Recompensas',
            'nivel' => 10
        ]
    ];

    file_put_contents(
        $arquivo,
        json_encode(
            $personagensIniciais,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );
}

// Ler personagens do arquivo JSON
function lerPersonagens($arquivo)
{
    return json_decode(file_get_contents($arquivo), true) ?? [];
}

// GET /personagens
$app->get('/personagens', function ($request, $response) use ($arquivo) {

    $personagens = lerPersonagens($arquivo);

    $response->getBody()->write(
        json_encode(
            $personagens,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

// GET /personagens/{id}
$app->get('/personagens/{id}', function ($request, $response, array $args) use ($arquivo) {

    $personagens = lerPersonagens($arquivo);
    $id = (int) $args['id'];

    foreach ($personagens as $personagem) {

        if ($personagem['id'] === $id) {

            $response->getBody()->write(
                json_encode(
                    $personagem,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(
            ['erro' => 'Personagem não encontrado'],
            JSON_UNESCAPED_UNICODE
        )
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

// POST /personagens
$app->post('/personagens', function ($request, $response) use ($arquivo) {

    $personagens = lerPersonagens($arquivo);

    $dados = $request->getParsedBody();

    $ids = array_column($personagens, 'id');
    $novoId = empty($ids) ? 1 : max($ids) + 1;

    $novoPersonagem = [
        'id' => $novoId,
        'nome' => $dados['nome'] ?? 'Sem nome',
        'classe' => $dados['classe'] ?? 'Sem classe',
        'nivel' => $dados['nivel'] ?? 1
    ];

    $personagens[] = $novoPersonagem;

    file_put_contents(
        $arquivo,
        json_encode(
            $personagens,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );

    $response->getBody()->write(
        json_encode(
            $novoPersonagem,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201);
});

// PUT /personagens/{id}
$app->put('/personagens/{id}', function ($request, $response, array $args) use ($arquivo) {

    $personagens = lerPersonagens($arquivo);
    $id = (int) $args['id'];
    $dados = $request->getParsedBody();

    foreach ($personagens as &$personagem) {

        if ($personagem['id'] === $id) {

            $personagem['nome'] = $dados['nome'] ?? $personagem['nome'];
            $personagem['classe'] = $dados['classe'] ?? $personagem['classe'];
            $personagem['nivel'] = $dados['nivel'] ?? $personagem['nivel'];

            file_put_contents(
                $arquivo,
                json_encode(
                    $personagens,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );

            $response->getBody()->write(
                json_encode(
                    $personagem,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(
            ['erro' => 'Personagem não encontrado'],
            JSON_UNESCAPED_UNICODE
        )
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});
// DELETE /personagens/{id}
$app->delete('/personagens/{id}', function ($request, $response, array $args) use ($arquivo) {

    $personagens = lerPersonagens($arquivo);
    $id = (int) $args['id'];

    foreach ($personagens as $indice => $personagem) {

        if ($personagem['id'] === $id) {

            unset($personagens[$indice]);

            $personagens = array_values($personagens);

            file_put_contents(
                $arquivo,
                json_encode(
                    $personagens,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );

            return $response->withStatus(204);
        }
    }

    $response->getBody()->write(
        json_encode(
            ['erro' => 'Personagem não encontrado'],
            JSON_UNESCAPED_UNICODE
        )
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});
$app->run();