<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Evento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #17a2b8;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        form input,
        form textarea,
        form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form textarea {
            resize: vertical;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #17a2b8;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        button:hover {
            background-color: #138496;
        }

        .back-home {
            display: inline-block;
            margin-top: 15px;
            color: #17a2b8;
            text-decoration: none;
        }

        .back-home:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Criar Novo Evento</h1>

        <form method="POST" action="{{ route('events.store') }}">
            @csrf

            <label for="eventname">Nome do Evento</label>
            <input type="text" name="eventname" id="eventname" required>

            <label for="description">Descrição</label>
            <textarea name="description" id="description" required></textarea>

            <label for="startdate">Data de Início</label>
            <input type="datetime-local" name="startdate" id="startdate" required>

            <label for="enddate">Data de Fim</label>
            <input type="datetime-local" name="enddate" id="enddate" required>

            <label for="capacity">Capacidade</label>
            <input type="number" name="capacity" id="capacity" min="1" required>

            <label for="price">Preço</label>
            <input type="number" name="price" id="price" min="0" required>

            <label for="ispublic">Visibilidade</label>
            <select name="ispublic" id="ispublic" required>
                <option value="1">Público</option>
                <option value="0">Privado</option>
            </select>

            <label for="location">Localização</label>
            <input type="text" name="location" id="location" required>

            <label for="eventstatus">Status</label>
            <select name="eventstatus" id="eventstatus" required>
                <option value="Upcoming">Upcoming</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <button type="submit">Criar Evento</button>
        </form>

        <a class="back-home" href="{{ route('dashboard') }}">← Voltar à Página Inicial</a>
    </div>
</body>
</html>
