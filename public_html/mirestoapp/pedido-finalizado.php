<?php
session_start();

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : 'demo-resto';
$pedidoId = isset($_GET['pedido_id']) ? (int) $_GET['pedido_id'] : 0;
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido finalizado · MiRestoApp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --brand: #2f8a3b;
            --brand-dark: #246d2f;
            --brand-soft: #e9f6e8;
            --bg: #f3f7ee;
            --ink: #1f2a1f;
            --line: #dce8d9;
        }

        body {
            background: radial-gradient(circle at top right, #e8f5e4 0, transparent 36%), var(--bg);
            color: var(--ink);
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1rem;
        }

        .success-card {
            width: min(680px, 100%);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 16px 44px rgba(39, 79, 39, .14);
            padding: 2rem;
        }

        .success-icon {
            width: 76px;
            height: 76px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: #e7f8ef;
            color: #0d9a52;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            font-weight: 700;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
        }
    </style>
</head>

<body>
    <main class="success-card">
        <div class="success-icon">
            <i class="bi bi-check2-circle"></i>
        </div>
        <h1 class="h3 mb-2">¡Pedido finalizado con éxito!</h1>
        <p class="text-muted mb-3">Tu pedido fue recibido correctamente y ya está en proceso.</p>

        <?php if ($pedidoId > 0): ?>
            <div class="alert alert-light border mb-4">
                Número de pedido: <strong>#<?php echo $pedidoId; ?></strong>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-brand" href="mis-pedidos.php?slug=<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
                Ver mis pedidos
            </a>
            <a class="btn btn-outline-secondary" href="index.php?slug=<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
                Seguir comprando
            </a>
        </div>
    </main>
</body>

</html>