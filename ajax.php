<?php

require_once('db_conn.php');


if($_SERVER['REQUEST_METHOD'] == 'GET') {

    if($_GET['method'] == 'list') {
        $sql = 'SELECT products.*, cart.quantity as cart_quantity from products join cart on products.id=cart.products_id';
        $products = $db->prepare($sql);
        $products->execute();
        $productsArray = $products->fetchAll();
        $html = '';
        foreach($productsArray as $product) {
            $html .= '
                <tr>
                    <td>'.$product['name'].'</td>
                    <td>'.$product['description'].'</td>
                    <td>'.$product['quantity'].'</td>
                    <td>';

            $remaining = $product['quantity'] - $product['cart_quantity'];

            if($remaining > 0)
                $html .= '<button class="btn btn-success cart" productId="'.$product['id'].'">Add to cart</button>';
            else
                $html .= '<button class="btn btn-success" disabled>Add to cart</button>';
                        
            $html .=    '<button class="btn btn-warning edit" productId="'.$product['id'].'">Edit</button>
                        <button class="btn btn-danger delete" productId="'.$product['id'].'">Delete</button>
                    </td>
                <tr>
            ';
        }

        $sql_cart = 'SELECT SUM(quantity) as sum FROM cart';
        $cart = $db->prepare($sql_cart);
        $cart->execute();
        $sum = $cart->fetch();

        echo json_encode(['html' => $html, 'sum_cart' => $sum['sum']]);
    } else if($_GET['method'] == 'getProduct') {
        $sql = 'select * from products where id=:id';
        $products = $db->prepare($sql);
        $products->bindParam('id', $_GET['id']);
        $products->execute();

        $product = $products->fetch();

        echo json_encode($product);
    }

} else {
    
    if($_POST['method'] == 'create') {
        
        $sql = 'INSERT INTO products (name, description, quantity) VALUES (:name, :description, :quantity)';
        $product = $db->prepare($sql);
        $product->bindParam(':name', $_POST['name']);
        $product->bindParam(':description', $_POST['description']);
        $product->bindParam(':quantity', $_POST['quantity']);

        $product->execute();

        $id = $db->lastInsertId();

        $sql_cart = 'INSERT INTO cart (products_id, quantity) VALUES (:id, 0)';
        $cart = $db->prepare($sql_cart);
        $cart->bindParam(':id', $id);
        $cart->execute();

        echo 'success';

    } else if($_POST['method'] == 'delete') {
        $sql = 'delete from products where id = :id limit 1';
        $product = $db->prepare($sql);
        $product->bindParam('id', $_POST['id']);
        $product->execute();

        $sql_cart = 'delete from cart where products_id = :id limit 1';
        $cart = $db->prepare($sql_cart);
        $cart->bindParam('id', $_POST['id']);
        $cart->execute();

        echo 'success';
    } else if($_POST['method'] == 'update') {
        $sql = 'UPDATE products set name=:name, description=:description, quantity=:quantity where id=:id';
        $product = $db->prepare($sql);
        $product->bindParam(':name', $_POST['name']);
        $product->bindParam(':description', $_POST['description']);
        $product->bindParam(':quantity', $_POST['quantity']);
        $product->bindParam(':id', $_POST['id']);

        $product->execute();

        echo 'success';
    } else if($_POST['method'] == 'cart') {
        $sqlCheck = 'select * from cart where products_id=:id';
        $productCheck = $db->prepare($sqlCheck);
        $productCheck->bindParam(':id', $_POST['id']);
        $productCheck->execute();

        $check = $productCheck->fetchAll();

        if(count($check) > 0) {
            $quantity = $check[0]['quantity'];
            $quantity+=1;
            $sql = 'UPDATE cart set quantity=:quantity where products_id=:id';
            $product = $db->prepare($sql);
            $product->bindParam(':quantity', $quantity);
            $product->bindParam(':id', $_POST['id']);
            $product->execute();
        } else {
            $sql = 'insert into cart (products_id, quantity) values (:id, 1)';
            $product = $db->prepare($sql);
            $product->bindParam(':id', $_POST['id']);
            $product->execute();
        }
    } else if($_POST['method'] == 'checkout') {

        $prod = $db->prepare('SELECT products.*, cart.quantity as cart_q FROM products join cart on products.id = cart.products_id where cart.quantity > 0');
        $prod->execute();
        $products = $prod->fetchAll();
        foreach ($products as $product) {
            $new_quantity = $product['quantity'] - $product['cart_q'];
            $update_product = 'UPDATE products SET quantity=:q where id=:id';
            $prod = $db->prepare($update_product);
            $prod->bindParam(':q', $new_quantity);
            $prod->bindParam(':id', $product['id']);
            $prod->execute();

            $update_cart = 'UPDATE cart SET quantity=0 where products_id=:id';
            $cart = $db->prepare($update_cart);
            $cart->bindParam(':id', $product['id']);
            $cart->execute();
        }

        echo 'success';
    }

}