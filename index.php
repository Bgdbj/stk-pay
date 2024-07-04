<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment</title>
</head>
<body>
    <h1>M-Pesa Payment</h1>
    <form id="paymentForm">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br><br>
        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required><br><br>
        <button type="submit">Pay</button>
    </form>
    
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const amount = document.getElementById('amount').value;
            const phone = document.getElementById('phone').value;

            fetch('process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ amount, phone }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment request sent successfully!');
                } else {
                    alert('Payment request failed: ' + data.error);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
