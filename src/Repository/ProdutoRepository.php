<?php

use PDO;

class ProdutoRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function criarObjetos($array): Produto
    {
        return new Produto(
            $array["id"],
            $array["tipo"],
            $array["nome"],
            $array["descricao"],
            $array["preco"],
            $array["imagem"],
        );
    }
    public function opcoesCafe(): array
    {
        $sql1 = "SELECT * FROM serenatto.produtos WHERE tipo  = 'café' ORDER BY preco";
        $stmt = $this->pdo->query($sql1);
        $produtosCafe = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dadosCafe = array_map(function($cafe) {
            return $this->criarObjetos($cafe);
        }, $produtosCafe);

        return $dadosCafe;
    }
    public function opcoesAlmoco(): array
    {
        $sql2 = "SELECT * FROM serenatto.produtos WHERE tipo  = 'almoço' ORDER BY preco";
        $stmt = $this->pdo->query($sql2);
        $produtosAlmoco= $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dadosAlmoco = array_map(function($almoco) {
           return $this->criarObjetos($almoco);
        }, $produtosAlmoco);
        
        return $dadosAlmoco;
    }
    public function buscarTodos(): array
    {
        $sql3 = "SELECT * FROM serenatto.produtos ORDER BY preco";
        $stmt = $this->pdo->query($sql3);
        $resProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $todosProdutos = array_map(function($produto) {
            return $this->criarObjetos($produto);
        },$resProdutos);

        return $todosProdutos;
    }
    public function deletarProduto(int $id):void
    {
        $sqlDelete = "DELETE FROM serenatto.produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sqlDelete);
        $stmt->execute([":id" => $id]);
    }
    public function salvar(Produto $produto):void
    {
        $this->pdo->beginTransaction();
        $sql = "INSERT INTO serenatto.produtos (tipo, nome, descricao, preco, imagem) VALUES (:tipo, :nome, :descricao, :preco, :imagem)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":tipo"=> $produto->getTipo(),
            ":nome"=> $produto->getNome(),
            ":descricao"=> $produto->getDescricao(),
            ":preco"=> $produto->getPreco(),
            ":imagem"=> $produto->getImagem(),
        ]);
        $this->pdo->commit();
    }
    public function buscar(int $id): Produto
    {
        $sql = "SELECT * FROM serenatto.produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id"=> $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->criarObjetos($result);
    }
    public function atualizar(Produto $produto):void
    {
        $sql = "UPDATE serenatto.produtos 
                SET tipo = :tipo, 
                    nome = :nome,
                    descricao = :descricao,
                    preco = :preco
                WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":tipo"=> $produto->getTipo(),
            ":nome"=> $produto->getNome(),
            ":descricao"=> $produto->getDescricao(),
            ":preco"=> $produto->getPreco(),
            ":id"=> $produto->getId()
        ]);

        if($produto->getImagem() !== 'logo-serenatto.png'){
            
            $this->atualizarFoto($produto);
        }

    }
    private function atualizarFoto(Produto $produto)
    {
        $sql = "UPDATE produtos SET imagem = ? WHERE id = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $produto->getImagem());
        $statement->bindValue(2, $produto->getId());
        $statement->execute();
    }
}