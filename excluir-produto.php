<?php

  require "src/conexao-bd.php";
  require "src/model/produto.php";
  require "src/Repository/ProdutoRepository.php";

  $produtoRepository = new ProdutoRepository($pdo);
  $produtoRepository->deletarProduto($_POST["id"]);

  header("location: admin.php");

//   Café com Leite	café	A harmonia perfeita do café e do leite, uma experiência reconfortante	R$ 2.00