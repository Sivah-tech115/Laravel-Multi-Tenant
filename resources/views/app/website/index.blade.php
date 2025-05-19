<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }
        header img {
            height: 40px;
        }
        header input {
            padding: 5px;
            width: 200px;
        }
        .container {
            display: flex;
            margin: 20px;
        }
        .sidebar {
            width: 20%;
            padding: 10px;
        }
        .sidebar h3 {
            margin-top: 0;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 10px 0;
            color: #666;
        }
        .main-content {
            width: 80%;
            padding: 10px;
        }
        .main-content h1 {
            margin-top: 0;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filters select {
            padding: 5px;
        }
        .pagination {
            display: flex;
            gap: 5px;
        }
        .pagination a {
            padding: 5px 10px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
        }
        .pagination a.active {
            background-color: #333;
            color: white;
        }
        .product-grid {
            display: flex;
            gap: 20px;
        }
        .product {
            width: 33%;
            text-align: center;
        }
        .product img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Comprapesa Logo">
        </div>
        <input type="text" placeholder="Cerca prodotti...">
    </header>

    <div class="container">
        <div class="sidebar">
            <h3>Categorie prodotto</h3>
            <ul>
                <li>355RIDER</li>
                <li>Acer IT</li>
                <li>BABYMARKT IT</li>
                <li>Decathlon IT</li>
                <li>GLAMST IT</li>
                <li>Idealo IT</li>
                <li>Lentiapio IT</li>
            </ul>
        </div>

        <div class="main-content">
            <h1>Shop</h1>
            <div class="filters">
                <div>
                    <label for="popularity">Popolarità</label>
                    <select id="popularity">
                        <option>Visualizzazione di 1-12 di 127971 risultati</option>
                    </select>
                </div>
                <div class="pagination">
                    <a href="#" class="active">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">4</a>
                    <a href="#">...</a>
                    <a href="#">10663</a>
                    <a href="#">10664</a>
                    <a href="#">10665</a>
                </div>
            </div>

            <div class="product-grid">
                <div class="product">
                    <img src="product1.jpg" alt="Black Adidas Shirt">
                </div>
                <div class="product">
                    <img src="product2.jpg" alt="Red Sports Shirt">
                </div>
                <div class="product">
                    <img src="product3.jpg" alt="Striped Backpack">
                </div>
            </div>
        </div>
    </div>
</body>
</html>