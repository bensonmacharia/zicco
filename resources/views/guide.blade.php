<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.ico">

        <title>Demo TECH</title>

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
            <h5 class="my-0 mr-md-auto font-weight-normal">Demo TECH</h5>
            <nav class="my-2 my-md-0 mr-md-3">
                <a class="p-2 text-dark" href="{{ url('/') }}">Home</a>
                <a class="p-2 text-dark" href="{{ url('/home/user-guide') }}">User Guide</a>
                <a class="p-2 text-dark" href="#">About us</a>
                <a class="p-2 text-dark" href="#">Contact us</a>
            </nav>
        </div>

        <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto">
            <h1 class="display-5">User Guide</h3>
            <p class="lead"></p>
        </div>

        <div class="container">
            <div class="row mt-2">
                <h5 class="pl-3">User Registration/Login</h5>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="list-group" id="list-tab" role="tablist">
                        <a class="list-group-item list-group-item-action active" id="list-register-list" data-toggle="list" href="#list-register" role="tab" aria-controls="register">Step 1: Register</a>
                        <a class="list-group-item list-group-item-action" id="list-login-list" data-toggle="list" href="#list-login" role="tab" aria-controls="login">Step 2: Login</a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="list-register" role="tabpanel" aria-labelledby="list-register-list"><ul><li>New users should go here: <a href="{{ route('register') }}">https://demo.utanzu.com/register</a> to register. </li><li>For the user Role option, select <b>Admin</b></li></ul></div>
                        <div class="tab-pane fade" id="list-login" role="tabpanel" aria-labelledby="list-login-list"><ul><li>Existing users should go here: <a href="{{ route('login') }}">https://demo.utanzu.com/login</a> to login with username and password.</li><li>In case the user has forgotten their password, they should go here: <a href="http://demo.utanzu.com/password/reset">http://demo.utanzu.com/password/reset</a>  to request for a password reset where instructions will be sent to their email address.</li><li>Remember to check your email spam folder if the password reset link is not on your inbox.</li></ul></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <h5 class="pl-3">Add Product List</h5>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="list-group" id="list-tab" role="tablist">
                        <a class="list-group-item list-group-item-action" id="list-category-list" data-toggle="list" href="#list-category" role="tab" aria-controls="category">Step 1: Add Product Category</a>
                        <a class="list-group-item list-group-item-action" id="list-product-list" data-toggle="list" href="#list-product" role="tab" aria-controls="product">Step 2: Add Product</a>
                        <a class="list-group-item list-group-item-action" id="list-stock-list" data-toggle="list" href="#list-stock" role="tab" aria-controls="stock">Step 3: Add Stock</a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade" id="list-category" role="tabpanel" aria-labelledby="list-category-list"><ul><li>From the left side menu, select Add Product to open the Manage categories page.</li><li>Press the Add Category button at the top and fill in the product category name e.g. Mouse, Adpaters, HDD Casings e.t.c</li><li>Press the Save button when done.</li></ul></div>
                        <div class="tab-pane fade" id="list-product" role="tabpanel" aria-labelledby="list-product-list"><ul><li>From the left side menu, select Add Product to open the Manage products page.</li><li>Press the Add Product button at the top and fill in the product details</li><li>The Avg. price is the selling price for each unit.</li><li>The product image upload filed is optional. Not a must you upload the image.</li><li>Press the Save button when done.</li></ul></div>
                        <div class="tab-pane fade" id="list-stock" role="tabpanel" aria-labelledby="list-stock-list"><ul><li>From the left side menu, select Add Stock to open the Add stock page.</li><li>Press the Add Stock button at the top and fill in the stock details</li><li>The Total Cost amount field should be filled with the total cost of ordering all the units including transport and clearance costs.</li><li>Press the Save button when done.</li></ul></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <h5 class="pl-3">Record Sales</h5>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="list-group" id="list-tab" role="tablist">
                        <a class="list-group-item list-group-item-action" id="list-sale-list" data-toggle="list" href="#list-sale" role="tab" aria-controls="sale">Step 1: Record Sale</a>
                        <a class="list-group-item list-group-item-action" id="list-customer-list" data-toggle="list" href="#list-customer" role="tab" aria-controls="customer">Step 2: Add Customer</a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade" id="list-sale" role="tabpanel" aria-labelledby="list-sale-list"><ul><li>From the left side menu, select Record Sale to open the Manage sales page.</li><li>Press the New Sale button at the top and fill in the sale details.</li><li>The Price amount should be the amount charged for each unit.</li><li>If the customer is not listed in the dropdown menu, add the user following the Add customer instructions (Step 2)</li><li>Amount paid field should contain the total amount the customer has paid for the products not including credit amount</li><li>Receipt No. and Invoice No. fileds are optional.</li><li>Press the Save button when done.</li></ul></div>
                        <div class="tab-pane fade" id="list-customer" role="tabpanel" aria-labelledby="list-customer-list"><ul><li>From the left side menu, select Customers to open the Manage customers page.</li><li>Press the Add button at the top and fill in the customer details</li><li>For the status field, select Active.</li><li>Press the Save button when done and the customer should now be visible when you are populating a sale.</li></ul></div>
                    </div>
                </div>
            </div>

            <footer class="pt-4 my-md-5 pt-md-5 border-top">
                <div class="row">
                    <div class="col-12 col-md">
                        <small class="d-block mb-3 text-muted">Demo Technologies | &copy; <?php echo date('Y'); ?> </small>
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
