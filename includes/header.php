<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Header</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    header {
      height: 80px;
      width: 100%;
      background: linear-gradient(90deg, rgba(0,255,149,0.9), rgba(0,191,255,0.8));
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-bottom: 3px solid rgba(166, 23, 89, 0.3);
    }

    h1 {
      font-size: 24px;
      font-weight: bold;
      color: #2b2b2b;
      margin: 0;
    }
  </style>
</head>
<body>
  <header>
    <h1><?= $title ?></h1>
  </header>
</body>
</html>
