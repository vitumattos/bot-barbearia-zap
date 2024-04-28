<?php
class ConexaoMySQL{
    // ===== PROPIEDADES ===== //
    public $conn;

    // ===== MÉTODO CONSTRUTOR ===== //
    public function __construct($HOST,$LOGIN,$PASSWORD,$DBNAME)
    {
        try{
            //Conectando com o BD
            $this->conn = new mysqli($HOST,$LOGIN,$PASSWORD,$DBNAME);
        } catch(Exception $e){
            echo 'Erro com a conexão com o banco de dados.';
            echo 'Erro generico: ' . $e->getMessage();
        }
        
    }
    // *********************** METODOS DE CADASTROS *********************** //
    // ===== REGISTRAR CLIENTE NOVO ===== //
    public function cadastroCliente($telefone,$nome,$status,$data_cadastro,$opcao){
       
        $cmd = "INSERT INTO cliente (telefone,nome,status,data_cadastro,opcao) VALUE ('$telefone','$nome','$status','$data_cadastro','$opcao')";
        $query = mysqli_query($this->conn, $cmd);

    }
    // ===== REGISTRA CONVERSA ===== //
    public function registraConversa($telefone,$msg_cliente,$msg_bot,$dia,$hora){
        $cmd = "INSERT INTO historico_conversa (telefone, msg_cliente,msg_bot,dia,hora) VALUES ('$telefone','$msg_cliente','$msg_bot','$dia','$hora')";
        $query = mysqli_query($this->conn, $cmd);
    }
    // ===== REGISTRA NA FILA ===== //
    public function cadastraFila($telefone,$nome,$hora){
        $cmd = "INSERT INTO fila (telefone, nome, hora) VALUE ('$telefone','$nome','$hora')";
        $query = mysqli_query($this->conn, $cmd);
    }

// *********************** METODOS DE CONSULTA *********************** //
    // ===== CONSULTA TELEFONE ===== //
    public function consultaTelefone($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        $total = mysqli_num_rows($query);
        // TELEFONE JÁ EXISTE
        if($total > 0){ 
            return true;
        // TELEFONE NÃO EXISTE  
        }else{
            return false;
        }
    }
    // ===== CONSULTA STATUS ===== //
    public function consultaStatus($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $res =mysqli_fetch_assoc($query);
            $status = $res['status'];
        }
        return $status;
    }
    // ===== CONSULTA OPCAO ===== //
    public function consultaOpcao($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $res =mysqli_fetch_assoc($query);
            $opcao = $res['opcao'];
        }
        return $opcao;
    }
    // ===== CONSULTA NOME ===== //
    public function consultaNome($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $res = mysqli_fetch_assoc($query);
            $nome = $res['nome'];
        }
        return $nome;
    }
    // ===== CONSULTA FILA ===== //
    public function consultaFila(){
        $cmd = "SELECT * FROM fila";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            while ($res = mysqli_fetch_assoc($query)){
                $fila[] = $res['nome'];
            }
            $fila_str = '';
                foreach ($fila as $str){
                    $fila_str .= $str." -> ";
                }
            $fila = array(
                "qtd" => mysqli_num_rows($query),
                "pessoas" => $fila_str
            );
            $tempo_espera = $fila['qtd']*25;
            return "Há ".$fila['qtd']." pessoas na sua frente. \n".$fila['pessoas']."\nO tempo de espera é: ".$tempo_espera." minutos";
        }else {
            return "Não há ninguem na fila. \nDigite 1 e venha correndo...";
    }
}

// *********************** METODOS DE ATULIZAÇÃO *********************** //
    // ===== ATUALIZA NOME ===== //  
    public function atualizaNome($telefone,$nome,$status){
        $cmd = "UPDATE cliente SET nome = '$nome', status = $status  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // ===== ATUALIZA STATUS ===== //  
    public function atualizaStatus($telefone,$status){
        $cmd = "UPDATE cliente SET status = $status  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // ===== ATUALIZA OPÇAO ===== //  
    public function atualizaOpcao($telefone,$opcao){
        $cmd = "UPDATE cliente SET opcao = $opcao  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
}
?>