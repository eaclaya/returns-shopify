<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Preview</title>
</head>
<body>
    <div style="font-family: sans-serif; font-size: 1em;">
        <p>Exchange request from: {{$email}}</p>
        <p>Order number: {{$order_number}}</p>
        <p>{{ $content  }}</p>
        @if($order)
        <table>
            <tbody>
                @foreach($order['line_items'] as $item)
                <tr>
                    <td>
                        <p>{{$item['name']}}</p>
                        <p>{{$item['sku']}}</p>
                    </td>
                    <td>Quantity: {{$item['quantity']}}</td>
                    <td>Price: {{$item['price']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</body>
</html>