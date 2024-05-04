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
        $clientes = $u->consultaClientes();
        $opcaoADM = $u->consultaOpcaoADM($telefone);

        if($status == "1"){
            $nome = $msg;
            $u->atualizaNome($telefone,$nome);
            $u->atualizaStatus($telefone,"3");

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
                    $resposta = "$nome, $info\n". "Temos ". $fila['qtd']. " Pessoas na fila ainda $confirmacao";
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                
                case "2":
                    if($fila['qtd']>0){
                        $fila_str = '';
                        $posicao = '1';
                        foreach ($fila['nome'] as $str){
                            $fila_str .= "$posicao.$str\n";
                            $posicao +=1;
                        }
                        $resposta = "Há ".$fila['qtd']." pessoas na sua frente. \n".$fila_str;
                    }else{
                        $resposta = "Não há ninguem na fila. \nDigite 1 e venha correndo...";
                    }
                    
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
            try{
                $u->atualizaOpcao($telefone,$opcao);
            }catch (Exception $e){
                $u->atualizaOpcao($telefone,'0');
            }
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
            $resposta = "Você já está na fila.\nDigite: *Sair* p/ sair da fila";
            $opcao = $msg;

            if($opcao == "Sair"){
                $u->atualizaStatus($telefone,"2");
                $u->removeFila($telefone);
                $resposta = "$nome, desculpa qualquer coisa e espero que volte logo...";
            }
        // ===== SUPER USER ===== //
        }elseif($status == "99"){
            $u->atualizaStatus($telefone,'100');
            $resposta = "Bem vindo, $nome.\n$menu_adm";
        }elseif($status == "100"){
            $opcao = $msg;
            try{
            $u->atualizaOpcao($telefone,$opcao);
            }catch (Exception $e){
            $u->atualizaOpcao($telefone,'0');
            }
            switch($opcao){
                case "0":
                    $u->atualizaStatus($telefone,'99');
                    $resposta = "Atendimentos encerrado, digite qualquer coisa para retomamos o atendimento.";
                    
                    break;
                case "1":
                    $u->atualizaStatus($telefone,'101');
                    if($fila['qtd']>0){
                        $resposta = "Há ".$fila['qtd']." pessoas na fila.\n0. Voltar\n";
                        foreach ($fila['nome'] as $posicao => $nome){
                            $resposta .= ($posicao + 1) . ". *$nome* (".$fila['telefone'][$posicao].")\n";
                        }
                    }else{
                        $resposta = "Não há ninguem na fila.\n0. Voltar";
                    }
                    break;
                case "2":
                    $u->atualizaStatus($telefone,"102");
                    if (!empty($clientes)) {
                        $resposta = "Lista de Clientes\n0. Voltar\n";
                        $posicao = 1;
                        foreach ($clientes as $cliente) {
                            $resposta .= "$posicao. " . $cliente['nome']." (".$cliente['telefone'] . ")\n";
                            $posicao+=1;
                        }
                    } else {
                        $resposta = "Não há clientes cadastrados.";
                    } 
                    break;
                default:
                $resposta = "Não entendi, poderia utilizar os índices.\n $menu_adm";       
            }
        }elseif($status == "101"){
            $opcao = $msg;
            if($opcao == '0'){
                $u->atualizaStatus($telefone,'100');
                $resposta = $menu_adm;
            }elseif($opcao <= $fila['qtd']){
                $opcaoADM = $fila['telefone'][$msg-1];
                $u->atualizaOpcaoADM($telefone,"$opcaoADM");
                $u->atualizaStatus($telefone,'101.1');
                $resposta = $opcaoADM."\n0 - *VOLTAR* \n1 - *CONCLUIR CORTE* \n2 - *REMOVER DA FILA*";
            }else{
                if($fila['qtd']>0){
                    $resposta = "Útilize os índices. \n0. Voltar\n";
                    foreach ($fila['nome'] as $posicao => $nome){
                        $resposta .= ($posicao + 1) . ". *$nome* (".$fila['telefone'][$posicao].")\n";
                    }
                }else{
                    $resposta = "Útilize os índices, não há ninguem na fila.\n 0. Voltar";
                }
            }
        }elseif($status == "101.1"){
            $opcao = $msg;
            try{
            $u->atualizaOpcao($telefone,$opcao);
            }catch (Exception $e){
            $u->atualizaOpcao($telefone,'0');
            }
            switch($opcao){
                case "0":
                    $u->atualizaStatus($telefone,'101');
                    if($fila['qtd']>0){
                        $resposta = "0. Voltar\n";
                        foreach ($fila['nome'] as $posicao => $nome){
                            $resposta .= ($posicao + 1) . ". *$nome* (".$fila['telefone'][$posicao].")\n";
                        }
                    }else{
                        $resposta = "Não há ninguem na fila.\n 0. Voltar";
                    }
                    break;
                case "1":
                    $u->registraCorte($opcaoADM,$dia,$hora);
                    $u->removeFila("$opcaoADM");
                    $u->atualizaStatus($telefone,'100');
                    $u->atualizaStatus($opcaoADM,'2');
                    $u->atualizaOpcaoADM($telefone,'0');
                    $resposta = $menu_adm;
                    break;
                case "2":
                    $u->removeFila("$opcaoADM");
                    $u->atualizaStatus($telefone,'100');
                    $u->atualizaStatus($opcaoADM,'2');
                    $u->atualizaOpcaoADM($telefone,'0');
                    $resposta = $menu_adm;
                    break;
                default:
                    $resposta = "Não entendi, poderia utilizar os índices.\n$opcaoADM\n0 - *VOLTAR* \n1 - *CONCLUIR CORTE* \n2 - *REMOVER DA FILA*";
            }  
        }elseif($status == '102'){
            $opcao = $msg;
            try{
                $u->atualizaOpcao($telefone,$opcao);
            }catch (Exception $e){
                $u->atualizaOpcao($telefone,'0');
                }
            if($opcao == '0'){
                $u->atualizaStatus($telefone,'100');
                $resposta = $menu_adm;
            }elseif($opcao <= count($clientes)){
                $opcaoADM = $clientes[$msg-1]['telefone'];
                $u->atualizaOpcaoADM($telefone,"$opcaoADM");
                $u->atualizaStatus($telefone,'102.1');
                $resposta = $clientes[$msg-1]['nome']." (".$clientes[$msg-1]['telefone'].") $configurar_cliente";
            }else{
                if(!empty($clientes)){
                    $resposta = "*Útilize os índices*\nLista de Clientes:\n0. *Voltar*\n";
                        $posicao = 1;
                        foreach ($clientes as $cliente) {
                            $resposta .= "$posicao. *" . $cliente['nome']."* (".$cliente['telefone'] . ")\n";
                            $posicao+=1;
                        }
                }else{
                    $resposta = "Útilize os índices, não há ninguem na fila.\n 0. Voltar";
                }
            }  
        }elseif($status == '102.1'){
            $opcao = $msg;
            switch($opcao){
                case "0":
                    $u->atualizaStatus($telefone,'102');
                    if (!empty($clientes)) {
                        $resposta = "Lista de Clientes \n0. Voltar\n";
                        $posicao = 1;
                        foreach ($clientes as $cliente) {
                            $resposta .= "$posicao. " . $cliente['nome']." (".$cliente['telefone'] . ")\n";
                            $posicao+=1;
                        }
                    } else {
                        $resposta = "Não há clientes cadastrados.";
                    } 
                    break;
                case "1":
                    $nome = $u->consultaNome("$opcaoADM");
                    $u->cadastraFila("$opcaoADM",$nome,$hora);
                    $u->atualizaStatus($telefone,'100');
                    $u->atualizaOpcaoADM($telefone,'0');
                    $resposta = $menu_adm;
                    break;
                case "2":
                    $nome = $u->consultaNome("$opcaoADM");
                    $u->atualizaStatus($telefone,'102.2');
                    
                    $resposta ="O novo nome de $nome será?";
                    break;
                default:
                    $nome = $u->consultaNome("$opcaoADM");
                    $resposta ="Não entendi, poderia usar os índices.\n".$nome." (".$opcaoADM.") $configurar_cliente";
                }
        }elseif($status == "102.2"){
            $u->atualizaNome($opcaoADM,$msg);
            $u->atualizaStatus($telefone,'100');
            $u->atualizaOpcaoADM($telefone,'0');

            $resposta = $menu_adm;
        }
        
    // PRIMEIRA VEZ DO CLIENTE
    }else{
        $u->cadastroCliente($telefone,'Cliente','1',$dia.' '.$hora,'','');
        
        // $resposta = $saudacao.$primerira_interacao;
        $resposta = 'saudacao';
        $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
    }
    echo $resposta

?>