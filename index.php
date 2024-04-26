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

    // CLIENTE CADASTRADO
    if($u->consultaTelefone($cliente_contato)){
        $situacao = $u->consultaStatus($cliente_contato);
        $nome = $u->consultaNome($cliente_contato);

        if($situacao == '1'){ //cadastra o nome do cliente 
            $nome = $cliente_msg; 
            $u->atualizaNome($cliente_contato,$nome,'2');
            $resposta = "É uma prazer falar com você $nome.\n $menu1";         
            // ===== PRIMEIRO MENU ===== //          
        }elseif($situacao == '2'){
            $opcao1=$cliente_msg;

            switch($opcao1){
                case '1':
                    $resposta = 'resposta errada';
                    break;
                case '2':
                    $resposta = 'resposta errada';
                    break;
                case '3':
                    $resposta = 'tabela';
                    break;
                default:
                    $resposta = "Não entendi $nome, pode repetir uma das opções acima";
                    $u->registraConversa($cliente_contato,$cliente_msg,$resposta,$dia,$hora);
            }
            
        }
    

    // PRIMEIRA VEZ DO CLIENTE
    }else{
        $resposta = $saudação;
        $u->cadastroCliente($cliente_contato,'','1',$dia.' '.$hora,'','','');
        $u->registraConversa($cliente_contato,$cliente_msg,$resposta,$dia,$hora);

    }
    echo $resposta

?>