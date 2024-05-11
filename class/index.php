<?php
    include ("./ConexaoMySQL.php");

    try{
        $u = new ConexaoMySQL('localhost','root','','bot-barbearia-zap'); // instancia da classe ConexãoMYSQL
        $u->criarTabelaBD(); // criando as tabelas se não existir
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

            $resposta = json_encode(array("status"=>"1","case"=>"","menu"=>"principal","frase"=>"prazer","nome"=>$nome,"fila"=>""));
            $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);  
        }elseif($status == "2"){
            $u->atualizaStatus($telefone,'3');
            
            $resposta = json_encode(array("status"=>"2","case"=>"","menu"=>"principal","frase"=>"bem-vindo","nome"=>$nome,"fila"=>""));
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
                    $resposta = json_encode(array("status"=>"3","case"=>"confirmacao","menu"=>"","frase"=>"info","nome"=>$nome,"fila"=>""));
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                
                case "2":
                    $resposta = json_encode(array("status"=>"3","case"=>"fila","menu"=>"","frase"=>"","nome"=>"","fila"=>json_encode($fila)));
                    
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                
                case "3":
                    $resposta = json_encode(array("status"=>"3","case"=>"tabela","menu"=>"","frase"=>"","nome"=>"","fila"=>""));
                    $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
                    break;
                default:
                    $resposta = json_encode(array("status"=>"3","case"=>"default","menu"=>"principal","frase"=>"Utilize os índices","nome"=>$nome,"fila"=>""));
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
                $resposta = json_encode(array("status"=>"4","case"=>"entrar","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>""));
                $u->atualizaStatus($telefone,"5");
                $u->cadastraFila($telefone,$nome,$hora);
            }elseif($opcao == "2"){
                $resposta = json_encode(array("status"=>"4","case"=>"encerrar","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>""));
                $u->atualizaStatus($telefone,"2");
                $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
            }else{
                $resposta = json_encode(array("status"=>"4","case"=>"confirmacao","menu"=>"","frase"=>"nao entendi","nome"=>$nome,"fila"=>""));
                
                $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
            }
        }elseif($status == "5"){
            $resposta = json_encode(array("status"=>"5","case"=>"","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>$fila['qtd_fila']));

            $opcao = $msg;

            if($opcao == "Sair"){
                $u->atualizaStatus($telefone,"2");
                $u->removeFila($telefone);
                $resposta = json_encode(array("status"=>"sair","case"=>"","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>""));
            }
        // ===== SUPER USER ===== //
        }elseif($status == "99"){
            $u->atualizaStatus($telefone,'100');
            $resposta = json_encode(array("status"=>"99","case"=>"","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
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
                    $u->atualizaOpcaoADM($telefone,'0');
                    $resposta = json_encode(array("status"=>"100","case"=>"0","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>""));
                    break;
                case "1":
                    $u->atualizaStatus($telefone,'101');
                    $resposta = json_encode(array("status"=>"100","case"=>"1","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>json_encode($fila)));
                    break;
                case "2":
                    $u->atualizaStatus($telefone,"102");
                    $resposta = json_encode(array("status"=>"100","case"=>"2","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>json_encode($clientes)));
                    break;
                default:
                    $resposta = json_encode(array("status"=>"100","case"=>"default","menu"=>"admin","frase"=>"Utilize os índices","nome"=>$nome,"fila"=>""));
            }
        }elseif($status == "101"){
            $opcao = $msg;
            if($opcao == '0'){
                $u->atualizaStatus($telefone,'100');
                $u->atualizaOpcaoADM($telefone,'0');
                $resposta = json_encode(array("status"=>"101","case"=>"0","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
            }elseif($opcao <= $fila['qtd_fila']){
                $opcaoADM = $fila['telefone'][$msg-1];
                $u->atualizaOpcaoADM($telefone,"$opcaoADM");
                $u->atualizaStatus($telefone,'101.1');
                $resposta = json_encode(array("status"=>"101","case"=>"","menu"=>"admin_lista","frase"=>"$opcaoADM","nome"=>$nome,"fila"=>""));
            }else{
                $resposta = json_encode(array("status"=>"100","case"=>"1","menu"=>"","frase"=>"out","nome"=>$nome,"fila"=>json_encode($fila)));

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
                    $u->atualizaStatus($telefone,'100');
                    $u->atualizaOpcaoADM($telefone,'0');
                    $resposta = json_encode(array("status"=>"101.1","case"=>"0","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
                    break;
                case "1":
                    $u->registraCorte($opcaoADM,$dia,$hora);
                    $u->removeFila("$opcaoADM");
                    $u->atualizaStatus($telefone,'100');
                    $u->atualizaStatus($opcaoADM,'2');
                    $resposta = json_encode(array("status"=>"101.1","case"=>"concluido","menu"=>"admin","frase"=>"","nome"=>$opcaoADM,"fila"=>""));
                    $u->atualizaOpcaoADM($telefone,'0');
                    break;
                case "2":
                    $u->removeFila("$opcaoADM");
                    $u->atualizaStatus($telefone,'100');
                    $u->atualizaStatus($opcaoADM,'2');
                    $resposta = json_encode(array("status"=>"101.1","case"=>"REMOVIDO","menu"=>"admin","frase"=>"","nome"=>$opcaoADM,"fila"=>""));
                    $u->atualizaOpcaoADM($telefone,'0');
                    break;
                default:
                $resposta = json_encode(array("status"=>"101.1","case"=>"out","menu"=>"admin_lista","frase"=>"$opcaoADM ","nome"=>$nome,"fila"=>""));
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
                $resposta = json_encode(array("status"=>"102","case"=>"0","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
            }elseif($opcao <= count($clientes)){
                $opcaoADM = $clientes[$msg-1]['telefone'];
                $u->atualizaOpcaoADM($telefone,"$opcaoADM");
                $u->atualizaStatus($telefone,'102.1');
                $resposta = json_encode(array("status"=>"102","case"=>"","menu"=>"admin_cliente","frase"=>"$opcaoADM ","nome"=>$nome,"fila"=>""));
            }else{
                $resposta = json_encode(array("status"=>"100","case"=>"2","menu"=>"","frase"=>"out ","nome"=>$nome,"fila"=>json_encode($clientes)));
            }  
        }elseif($status == '102.1'){
            $opcao = $msg;
            switch($opcao){
                case "0":
                    $u->atualizaStatus($telefone,'100');
                    $resposta = json_encode(array("status"=>"102.1","case"=>"0","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
                    break;
                case "1":
                    $nome = $u->consultaNome("$opcaoADM");
                    $u->cadastraFila("$opcaoADM",$nome,$hora);
                    $u->atualizaStatus($telefone,'100');
                    $resposta = json_encode(array("status"=>"102.1","case"=>"add_fila","menu"=>"admin","frase"=>"","nome"=>$opcaoADM,"fila"=>""));
                    $u->atualizaOpcaoADM($telefone,'0');
                    break;
                case "2":
                    $nome = $u->consultaNome("$opcaoADM");
                    $u->atualizaStatus($telefone,'102.2');
                    
                    $resposta = json_encode(array("status"=>"102.1","case"=>"2","menu"=>"","frase"=>"","nome"=>$nome,"fila"=>""));
                    break;
                default:
                    $nome = $u->consultaNome("$opcaoADM");
                    $resposta = json_encode(array("status"=>"102.1","case"=>"out","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
                }
        }elseif($status == "102.2"){
            $u->atualizaNome($opcaoADM,$msg);
            $u->atualizaStatus($telefone,'100');
            $u->atualizaOpcaoADM($telefone,'0');

            $resposta = json_encode(array("status"=>"102.2","case"=>"0","menu"=>"admin","frase"=>"","nome"=>$nome,"fila"=>""));
        }
        
    // PRIMEIRA VEZ DO CLIENTE
    }else{
        $u->cadastroCliente($telefone,'Cliente','1',$dia.' '.$hora,'','');

        $resposta = json_encode(array("status"=>"primeira vez"));
        $u->registraConversa($telefone,$msg,$resposta,$dia,$hora);
    }
    echo $resposta

?>