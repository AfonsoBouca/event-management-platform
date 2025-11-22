<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0 20px;
        }

        h2 {
            color: #333;
            margin-top: 30px;
            text-align: center;
        }

        p {
            text-align: center;
            color: #666;
        }

        ul {
            list-style: none;
            padding: 0;
            max-width: 800px;
            margin: 20px auto;
        }

        li {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        li strong {
            font-size: 1.1em;
            color: #17a2b8;
        }

        li p, li span {
            color: #555;
            margin: 5px 0;
            font-size: 0.95em;
        }

        /* Botão voltar para homepage */
        .back-btn {
            display: inline-block;
            margin: 20px auto;
            padding: 8px 15px;
            background-color: #17a2b8;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #138496;
        }
    </style>
</head>
<body>
    <h2>Results for: "{{ $query }}"</h2>

    @if($events->isEmpty())
        <p>No events found.</p>
    @else
        <ul>
        @foreach($events as $event)
            <li>
                <strong>{{ $event->eventname }}</strong><br>
                {{ $event->description }}<br>
                @if($event->startdate)
                    <span>Begin: {{ \Carbon\Carbon::parse($event->startdate)->format('d/m/Y H:i') }}</span>
                @else
                    <span>Begin: Not defined</span>
                @endif
            </li>
        @endforeach
        </ul>
    @endif

    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="back-btn">Back to Homepage</a>
    </div>
</body>
</html>
