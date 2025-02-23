<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        /* Body Styling */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;

            /* Background styling */
            background: linear-gradient(135deg, rgba(52, 121, 40, 0.8), rgba(40, 90, 32, 0.8)),
                url('bg.png') no-repeat center center / cover;
            color: #fff;
        }

        /* Container Styling */
        .landing-container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 40px 20px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            color: #333;
        }

        /* Header Section */
        .landing-container h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #347928;
        }

        .landing-container p {
            font-size: 1rem;
            margin-bottom: 20px;
            color: #555;
        }

        /* Buttons */
        .btn-landing {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 10px auto;
            padding: 12px;
            border-radius: 8px;
            text-transform: uppercase;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-admin {
            background-color: #347928;
            color: #fff;
        }

        .btn-admin:hover {
            background-color: #285a20;
        }

        .btn-accounting {
            background-color: #555;
            color: #fff;
        }

        .btn-accounting:hover {
            background-color: #333;
        }

        .btn-student {
            background-color: #ff6f61;
            /* You can choose a different color */
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-student:hover {
            background-color: #e55b4a;
            /* Darker shade on hover */
        }
    </style>
</head>

<body>
    <div class="landing-container">
        <h1>Welcome!</h1>
        <p>Choose your portal to proceed.</p>
        <a href="./admin/adminLogin.php" class="btn btn-landing btn-admin">Admin Login</a>
        <a href="./accounting/accounting_login.php" class="btn btn-landing btn-accounting">Accounting Login</a>
        <a href="./student/student_login.php" class="btn btn-landing btn-student">Student Login</a>
    </div>
</body>

</html>