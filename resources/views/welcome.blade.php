<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.ico">

        <title>Zicco TECH</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <style>
            html {
                font-size: 14px;
            }
            @media (min-width: 768px) {
                html {
                    font-size: 16px;
                }
            }

            .container {
                max-width: 960px;
            }

            .pricing-header {
                max-width: 700px;
            }

            .card-deck .card {
                min-width: 220px;
            }

            .border-top { border-top: 1px solid #e5e5e5; }
            .border-bottom { border-bottom: 1px solid #e5e5e5; }

            .box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
        </style>
    </head>

    <body>

        <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
            <h5 class="my-0 mr-md-auto font-weight-normal">Zicco TECH</h5>
            <nav class="my-2 my-md-0 mr-md-3">
                <a class="p-2 text-dark" href="#">About us</a>
                <a class="p-2 text-dark" href="#">Contact us</a>
            </nav>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-outline-primary">Home</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>
                    <a class="p-2 text-dark"></a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                    @endif
                @endauth
            @endif
        </div>

        <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
            <h1 class="display-4"></h1>
            <p class="lead">We sell computer accessories at both wholesale and retail prices. Call us or visit our shop for more information.</p>
        </div>

        <div class="container">
            <div class="card-deck mb-3 text-center">
                <div class="card mb-4 box-shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">Catalogue</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Mouse</li>
                            <li>Adapters</li>
                            <li>Cables</li>
                            <li>Memory sticks</li>
                        </ul>
                        <button type="button" class="btn btn-lg btn-block btn-outline-primary">Location</button>
                    </div>
                </div>
                <div class="card mb-4 box-shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">Location</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Dominion Exhibition</li>
                            <li>1st Floor D6</li>
                            <li>Opp. Imenti House</li>
                            <li>Along Tom Mboya Street</li>
                        </ul>
                        <button type="button" class="btn btn-lg btn-block btn-primary">Visit</button>
                    </div>
                </div>
                <div class="card mb-4 box-shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">Contact us</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Tel: 0758571810</li>
                            <li>-</li>
                            <li>-</li>
                            <li>-</li>
                        </ul>
                        <button type="button" class="btn btn-lg btn-block btn-primary">Contact</button>
                    </div>
                </div>
            </div>

            <footer class="pt-4 my-md-5 pt-md-5 border-top">
                <div class="row">
                    <div class="col-12 col-md">
                        <small class="d-block mb-3 text-muted">Zicco Technologies | &copy; <?php echo date('Y'); ?> </small>
                    </div>
                </div>
            </footer>
        </div>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="../../../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script>
            Holder.addTheme('thumb', {
            bg: '#55595c',
            fg: '#eceeef',
            text: 'Thumbnail'
            });
        </script>
    </body>
</html>
