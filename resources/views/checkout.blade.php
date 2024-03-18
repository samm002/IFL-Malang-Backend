<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Donation Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            min-height: 75rem;
        }
    </style>
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{config('midtrans.client_key')}}"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">IFL Malang</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="/donation">Donation <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="jumbotron">
        <div class="container">
            <h1 class="display-4">Detail Pesanan</h1>
        </div>
    </div>
    <div class="container">
      <table>
          <tr>
            <td>Nama</td>
            <td> : {{ $donation->name }}</td>
          </tr>
          <tr>
            <td>Email</td>
            <td> : {{ $donation->email }}</td>
          </tr>
          <tr>
            <td>Jumlah Donasi</td>
            <td> : {{ $donation->donation_amount }}</td>
          </tr>
          <tr>
            <td>Pesan Donasi</td>
            <td> : {{ $donation->donation_message }}</td>
          </tr>
        </table>     
        <button class="btn btn-primary" id="pay-button">Donasi</button>  
    </div>

    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Invoice details will be displayed here -->
          <div id="invoiceDetails"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    // For example trigger on button clicked, or any time you need
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
      // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
      window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
          /* You may add your own implementation here */
          alert("payment success!"); console.log(result);
          $.ajax({
          url: '/api/v1/invoiceView/' + result.order_id, // assuming you have a route for fetching the invoice details
          method: 'GET',
          success: function(response) {
            // Populate the modal with the received invoice details
            var invoiceDetails = response.invoice;
            $('#invoiceDetails').html('<p>Donation ID: ' + invoiceDetails.donation_id + '</p>' +
                                      '<p>Date: ' + invoiceDetails.date + '</p>' +
                                      '<p>Time: ' + invoiceDetails.time + '</p>' +
                                      '<p>Payment Method: ' + invoiceDetails.payment_method + '</p>' +
                                      '<p>Amount: ' + invoiceDetails.donation_amount + '</p>');
            // Show the modal
            $('#invoiceModal').modal('show');
          },
          error: function(xhr, status, error) {
            // Handle errors if any
            console.error(error);
          }
        });
        },
        onPending: function(result){
          /* You may add your own implementation here */
          alert("wating your payment!"); console.log(result);
        },
        onError: function(result){
          /* You may add your own implementation here */
          alert("payment failed!"); console.log(result);
        },
        onClose: function(){
          /* You may add your own implementation here */
          alert('you closed the popup without finishing the payment');
        }
      })
    });
  </script>
</body>
</html>
