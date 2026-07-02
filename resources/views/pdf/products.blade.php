<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Productos</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 16px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <h1>Listado de productos</h1>

    <table>
        <thead>
            <tr>
                <th>Código de producto</th>
                <th>Nombre del producto</th>
                <th>Precio</th>
                <th>Fecha de creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format((float) $product->price, 2, '.', '') }}</td>
                    <td>{{ $product->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
