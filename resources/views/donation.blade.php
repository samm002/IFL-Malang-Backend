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
            <h1 class="display-4">Yuk Donasi!</h1>
            <p class="lead">Silahkan berdonasi melalui IFL Malang untuk saudara kita yang membutuhkan.</p>
        </div>
    </div>
    <div class="container">
        <form id="donationForm" action="/donate" method="POST">
        @csrf
            <legend>Donation</legend>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" class="form-control" id="name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">E-Mail</label>
                        <input type="email" name="email" class="form-control" id="email">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Campaign</label>
                      <select class="form-control" name="campaign_id" id="campaign_id">
                        <option value="" disabled selected hidden>Pilih campaign</option>
                        @forelse ($campaign as $item)
                            <option value="{{$item->id}}">{{$item->title}}</option>
                        @empty
                            <option value="">Tidak Ada Campaign</option>
                        @endforelse
                    </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="donation_amount">Jumlah Donasi</label>
                        <input type="number" name="donation_amount" class="form-control" id="donation_amount">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="donation_message">Pesan Donasi (Opsional)</label>
                        <textarea name="donation_message" cols="30" rows="3" class="form-control" id="donation_message"></textarea>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" id="pay-button" type="submit">Kirim</button>
        </form>
    </div>
    <script>
      document.getElementById('donationForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting normally
        
        var campaignId = document.getElementById('campaign_id').value; // Get the selected campaign ID
        var formAction = this.getAttribute('action');
        var newUrl = formAction + '/' + campaignId;
        this.setAttribute('action', newUrl);
        this.submit();
        // // Redirect to the donation endpoint with the campaign ID
        // window.location.href = 'donate/' + campaignId;
      });
    </script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>
