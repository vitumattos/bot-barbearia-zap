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
        $telefone = $_GET['contato'];
        $msg = $_GET['msg'];
        $USER = $_GET['usuario'];

    // CLIENTE CADASTRADO
    if($u->consultaTelefone($telefone)){
        $nome = $u->consultaNome($telefone);
        $status = $u->consultaStatus($telefone);
        $opcao = $u->consultaOpcao($telefone);
        $fila = $u->consultaFila();

        if($status == "1"){
            $nome = $msg;
            $u->atualizaNome($telefone,$nome,"3");

            $resposta = "É uma prazer falar com você $nome.\n$menu";
            $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);  
        }elseif($status == "2"){
            $u->atualizaStatus($telefone,'3');
            
            $resposta = "Bem vindo, $nome.\n$menu";
            $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
        }elseif($status == "3"){
            $opcao = $msg;
            try{
                $u->atualizaOpcao($telefone,$opcao);
            }catch (Exception $e){
                $u->atualizaOpcao($telefone,'0');
            }

            switch($opcao){
                case "1":
                    $u->atualizaStatus($telefone,'4');
                    $resposta = $nome. " ".$info."\n".$fila. "\n". $confirmacao;
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                
                case "2":
                    $resposta = $fila;
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                
                case "3":
                    $resposta = $tabela_preco;
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                default:
                $resposta = "$nome, poderia usar os índices das opções abaixo.\n".$menu;
                $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                }
        }elseif($status == "4"){
            $opcao = $msg;
            if($opcao == "1"){
                $resposta = $entrou;
                $u->atualizaStatus($telefone,"5");
                $u->cadastraFila($telefone,$nome,$hora);
            }elseif($opcao == "2"){
                $resposta = "Obrigado! Encerramos nossa conversa aqui";
                $u->atualizaStatus($telefone,"2");
                $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
            }else{
                $resposta = "Não entendi, poderia usar os índices.\n". $confirmacao;
                $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
            }
        }elseif($status == "5"){
            $resposta = "Você já está na fila.";
        }
        
                 

    // PRIMEIRA VEZ DO CLIENTE
    }else{
        $u->cadastroCliente($telefone,'Cliente','1',$dia.' '.$hora,'');
        
        $resposta = $saudacao.$primerira_interacao;
        $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
    }
    echo $resposta

?>