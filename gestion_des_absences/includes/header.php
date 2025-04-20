
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> header  </title>
    <style>
        body{
            margin: 0px;
        }
        tr{
            height : 80px;
            width : 100%;
            background-color:rgba(0, 110, 255, 0.93) ;
        }
        td{
            display: flex;
            height : 80px;
            width : 100%;
            font-family: 'Courier New', Courier, monospace;
            font-size: larger;
            font-weight: 900;
            color: rgb(41, 41, 42);
            padding: auto;
            justify-content: center;
            align-items: center;
        }
        table{
            margin: 0px;
            width : 100%;
            border: solid 2px rgba(166,23,89,0.3);
            border-radius:4px;
        }
    </style>
</head>
<body>
    <table cellspacing="0" cellpadding="1">
        <tr>
            <td ><?= $title ?></td>
        </tr>
    </table>
</body>
</html>