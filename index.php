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
        $opcao1 = $cliente_msg;

        if($situacao == '1'){ //cadastra o nome do cliente 
            $nome = $cliente_msg; 
            $u->atualizaNome($cliente_contato,$nome,'2');
            $resposta = "É uma prazer falar com você $nome.\n $menu1";
            // ===== PRIMEIRO MENU ===== //
        }elseif($situacao == '2'){
            $opcao1=$cliente_msg;
            if($opcao1 <='3' and $opcao1 > '0'){
                $u->atualizaOpcao1($cliente_contato,$opcao1);
                switch($opcao1){
                    case '1':
                        $u->atualizaStatus($cliente_contato,'3');
                        $resposta = "Tudo Joia, ".$nome;
                        $opcao1 = $cliente_msg;
                        break;
                    case '2':
                        $resposta = $u->consultaFila();
                        break;
                    case '3':
                        $resposta = $tabela_preco."\nDigite 1 e garanta sua vaga";
                        $u->atualizaStatus($cliente_contato,'3');
                        break;
                    default:
                        $resposta = "Não entendi $nome, pode repetir uma das opções acima";
                        $u->registraConversa($cliente_contato,$cliente_msg,$resposta,$dia,$hora);
                }
                        
            }
        }elseif($situacao == '3' and $opcao1 == '1'){
            $resposta = $informacao;
            $opcao1 = $cliente_msg;
            $u->atualizaOpcao1($cliente_contato,$opcao1);
            $u->atualizaStatus($cliente_contato,'4');

        }elseif($situacao == '4' and $opcao1 == '1'){
            $resposta = $u->consultaFila();
            $u->atualizaOpcao1($cliente_contato,$opcao1);
            $u->atualizaStatus($cliente_contato,'5');

        }elseif($situacao == '5' and $opcao1 =='1'){
            $resposta = $entrou;
            $u->cadastraFila($cliente_contato,'',$hora);
        }else{
            $resposta = "Não entendi $nome, poderia usar os índices acima";
        }
                    

    // PRIMEIRA VEZ DO CLIENTE
    }else{
        $resposta = $saudação;
        $u->cadastroCliente($cliente_contato,'','1',$dia.' '.$hora,'','','');
        $u->registraConversa($cliente_contato,$cliente_msg,$resposta,$dia,$hora);

    }
    echo $resposta

?>