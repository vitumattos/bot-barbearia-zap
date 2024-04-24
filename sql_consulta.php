<?php
// CONSTANTES DO SERVIDOR BANCO DE DADOS
    $servidor = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'bot';
    $conn = mysqli_connect($servidor,$usuario,$senha,$banco);

// TENTANDO CONECTAR NO SERVIDOR
    if(!$conn)
    {
        echo "erro";
    }else{
        echo "tudo ok com o banco";
    }

// // INSERINDO DADOS NO BANCO
//     $nome = 'vitor';
//     $telefone = '21 9 99678 0551';
//     $sql = "INSERT INTO bot (nome, telefone) VALUE ('$nome','$telefone')";
//     $query = mysqli_query($conn, $sql);

//     if (!$query){
//         echo " Erro ao inserir";
        
//     }else{
//         echo "Novas informações inseridas com sucesso!";
//     }

// // ATUALIZANDO UM LINHA DO BANCO DE DADOS
//     $nome ='caio';
//     $sql = "UPDATE bot SET nome = '$nome' WHERE id=6";
//     $query = mysqli_query($conn,$sql);
//     if(!$query){
//         echo "Erro ao inserir";
//     }else{
//         echo "Novas informações atualizada com sucesso!";
//     }


// // BUSCA NO BANCO DE DADOS
//     $sql = "SELECT * FROM bot WHERE nome = 'vitor'";
//     $query = mysqli_query($conn,$sql);

//     while($rows_usuario = mysqli_fetch_array($query)){
//         $nome = $rows_usuario['nome'];
//         $telefone = $rows_usuario['telefone'];
//         echo $nome , $telefone;
//         echo '<br>';
//     }

// ?>