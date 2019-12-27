<?php

require_once('db_conn.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style>
        .top-100 {
            margin-top: 50px;
        }

        a {
            text-decoration: none;
        }

        .cart-span {
            background-color: #00c2ffb0;
            padding: 5px 10px;
            border-radius: 50%;
            color: white;
        }
    </style>
</head>

<body>

    <div class="col-md-8 col-md-offset-2 top-100">
        <div class="pull-right">
            <label>Cart</label> 
            <span class="cart-span">5</span><br>
            <a href="javascript:checkoutCart();">Checkout cart</a>
        </div>
    </div>     
    <div class="col-md-8 col-md-offset-2 top-100">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        
    </div>
    <div class="col-md-10">
        <button class="btn btn-default new-product pull-right">Add new product</button>
    </div>
    
    <div class="col-md-8 col-md-offset-2 top-100 edit-create" style="display: none;">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Name">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" placeholder="Description">
        </div>
        <div class="form-group">
            <label for="quantity">Name</label>
            <input type="text" class="form-control" id="quantity" placeholder="Quantity">
        </div>

        <input type="hidden" name="" id="id">

        <button class="btn btn-default saveBtn pull-right">Save</button>
        <button class="btn btn-default editBtn pull-right">Update</button>
    </div>

    <script>
        
        $(document).ready(function(){
            listProducts();
        });

        function listProducts() {
            $.get('ajax.php?method=list')
            .done(function(data){
                data = JSON.parse(data);
                $('tbody').html(data.html);
                $('.cart-span').text(data.sum_cart);
            })
        }

        function checkoutCart() {
            $.post('ajax.php', {method: 'checkout'})
            .done(function(data){
                listProducts();
            })
        }

        $('.new-product').on('click', function(){
            $('.edit-create').show();
            $('.editBtn').hide();
        })

        $('.saveBtn').on('click', function(){
            $.post('ajax.php', {
                method: 'create',
                name: $('#name').val(),
                description: $('#description').val(),
                quantity: $('#quantity').val()
            }
            
            )
            .done(function(data){
                listProducts();
            });
        });

        $('.editBtn').on('click', function(){
            $.post('ajax.php', {
                method: 'update',
                id: $('#id').val(),
                name: $('#name').val(),
                description: $('#description').val(),
                quantity: $('#quantity').val()
            }
            
            )
            .done(function(data){
                listProducts();
            });
        });

        $(document).on('click', '.delete', function(){
            $.post('ajax.php', {method: 'delete', id: $(this).attr('productId')})
            .done(function(){
                listProducts();
            })
        });

        $(document).on('click', '.edit', function(){
            $.get('ajax.php?method=getProduct&id='+$(this).attr('productId'))
            .done(function(data) {
                data = JSON.parse(data);
                
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#quantity').val(data.quantity);
                $('#id').val(data.id);

                $('.edit-create').show();
                $('.saveBtn').hide();
            });
        });

        $(document).on('click', '.cart', function(){
            $.post('ajax.php', {method: 'cart', id: $(this).attr('productId')})
            .done(function(){
                listProducts();
            })  
        })

    </script>

</body>

</html>