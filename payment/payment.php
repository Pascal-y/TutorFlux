<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>MTN MoMo Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .payment-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .payment-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #ffd700;
            border-color: #ffd700;
            color: #000;
        }
        .momo-icon{
            width: 120px;
            height: 120px;
            margin: 10px 120px;
        }
    </style>
</head>
<body>

<div class="payment-container">
<img src="../assets/MTN.jfif" alt="" class="momo-icon">
    <h2>MTN MoMo Payment</h2>
    <form method="POST" action="process_payment.php">
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount (XAF)</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="reason">Reason for Payment</label>
            <input type="text" class="form-control" id="reason" name="reason" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Pay Now</button>
    </form>
</div>

</body>
</html>
