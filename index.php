<?php
include ("./ConexaoMySQL.php");

try{
    $u = new ConexaoMySQL('localhost','root','','bot-barbearia-zap'); // instancia da classe ConexãoMYSQL
    include ("./menu.php");
} catch(PDOException $e){
    echo 'Erro ao conectar ao banco de dados';
}

// INFORMAÇÕES DO CLIENTE E ÚTEIS
    date_default_timezone_set('America/Sao_Paulo');   
    $dia =  date('d-m-Y', time());
    $hora = date('H:i:s', time());
    $cliente_contato = $_GET['contato'];
    $cliente_msg = $_GET['msg'];
    $USER = $_GET['usuario'];

