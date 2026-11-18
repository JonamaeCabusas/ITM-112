<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>

  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(rgba(14, 15, 13, 0.7), rgba(14, 15, 13, 0.7)), 
                    url('{{ asset("bg.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 25px;
        
    }

   
    main {
        width: 100%;
        max-width: 430px;
        background: rgba(255, 255, 255, 0.08); 
        padding: 40px;
        border-radius: 20px;
        border: 1px solid blue;
    }
    h1, h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 10px;
        }

    p {
            color: white;
            font-size: 16px;
            margin-bottom: 25px;
        }

    form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

    
    input {
            width: 100%;
            padding: 14px 16px 14px 45px; 
            border: 1px solid yellow;
            border-radius: 10px;
            outline: none;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transition: 0.2s;
        }

        input::placeholder {
            color: pink;
        }

        input:focus {
            border-color: green;
            background: rgba(255, 255, 255, 0.1);
            
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: green;
            font-size: 18px;
        }

        button {
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: Green;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 15px ;
        }

        button:hover {
            opacity: 0.9;
            
        }

        a {
            color: blue;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        .form-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: pink;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-top: -10px;
        }
    </style>
</head>

<body>

    <main>

        {{-- Render the login or registration page content inside the shared layout. --}}
        {{ $slot }}

    </main>
</body>
</html>