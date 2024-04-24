<?php
include ("./menu.php");
// CONECTANDO AO SERVIDOR
    $servidor = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'bot-barbearia-zap';

    try{
        $conn = mysqli_connect($servidor,$usuario,$senha,$banco);
    }catch(PDOException $e){
        echo 'ERRO AO CONECTAR AO SERVIDOR';
    }

// INFORMAÇÕES DO CLIENTE
    $cliente_telefone = $_GET['telefone'];
    $cliente_msg = $_GET['msg'];
    $usuario = $_GET['usuario'];


//  VERIFICA SE O TELEFONE EXISTE NO BANCO DE DADOS E PEGA O STATUS ATUAL
    $sql = "SELECT * FROM usuario WHERE telefone = '$cliente_telefone'";
    $query = mysqli_query($conn,$sql);
    $total = mysqli_num_rows($query);

    while($rows_usuario = mysqli_fetch_array($query)){
        $status = $rows_usuario['status'];
    }

    // NÃO EXISTE, ENTÃO INSERI
    if($total ==0){ 
        $sql = "INSERT INTO usuario (telefone, status) VALUES ('$cliente_telefone','1')";
        $query = mysqli_query($conn,$sql);
        if($query){
            $resposta =  $saudação;
        }

    // EXISTE, ENTÃO...
    }elseif($total ==1){
        // status 1 == Cliente novo
        if($status ==1){ 
            $nome = $cliente_msg;
            $sql = "UPDATE usuario SET nome = '$nome' WHERE telefone='$cliente_telefone'";
            $query = mysqli_query($conn,$sql);
            if($query){
                $status = $status + 1;
                $sql = "UPDATE usuario SET status = '$status' WHERE telefone='$cliente_telefone'";
                $query = mysqli_query($conn,$sql);
                $resposta = "Olá $nome. $menu1";
            }
        // status 2 == Cliente já cadastrado 
        }elseif($status==2){
            $resposta = 'Oi sumido!';
        }
        
    }
    echo $resposta;


//  ARMAZENANDO AS CONVERSAS EM UM BANCO DE DADOS

    $dia =  date('d-m-Y', time());
    $hora = date('H:i:s', time());

    $sql = "INSERT INTO historico (telefone, msg_cliente,msg_bot,dia,hora) VALUES ('$cliente_telefone','$cliente_msg','$resposta','$dia','$hora')";
    $query = mysqli_query($conn,$sql);
?>
