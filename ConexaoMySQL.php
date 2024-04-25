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
    // ===== CADASTRAR CLIENTE NOVO ===== //
    public function cadastroCliente($telefone,$nome,$status,$data_cadastro,$opcao1,$opcao2,$ultimo_corte,$QTD_corte,$sequencia){
        //  VERIFICA SE O TELEFONE EXISTE NO BANCO
        $existe = $this->consultaTelefone($telefone);
        // TELEFONE JÁ EXISTE
        if($existe){ 
            return false;
        // TELEFONE NÃO EXISTE  
        }else{
            $cmd = "INSERT INTO cliente (telefone,nome,status,data_cadastro,opcao1,opcao2,ultimo_corte,QTD_corte,sequencia) VALUE ('$telefone','$nome','$status','$data_cadastro','$opcao1','$opcao2','$ultimo_corte','$QTD_corte','$sequencia')";
            $query = mysqli_query($this->conn, $cmd);
            return true;
        }
    }
    // ===== REGISTRA CONVERSA ===== //
    public function registraConversa($telefone,$msg_cliente,$msg_bot,$dia,$hora){
        $cmd = "INSERT INTO historico (telefone, msg_cliente,msg_bot,dia,hora) VALUES ('$telefone','$msg_cliente','$msg_bot','$dia','$hora')";
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
            return false;
        // TELEFONE NÃO EXISTE  
        }else{
            return true;
        }
    }
    // ===== CONSULTA STATUS ===== //
    public function consultaStatus($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $row =mysqli_fetch_assoc($query);
            $status = $row['status'];
        }
        return $status;
    }
    // ===== CONSULTA OPCAO1 ===== //
    public function consultaOpcao1($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $row =mysqli_fetch_assoc($query);
            $opcao1 = $row['opcao1'];
        }
        return $opcao1;
    }
    // ===== CONSULTA NOME ===== //
    public function consultaNome($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $row =mysqli_fetch_assoc($query);
            $nome = $row['nome'];
        }
        return $nome;
    }
    // ===== CONSULTA SEQUENCIA ===== //
    public function consultaSequencia($telefone){
        $cmd = "SELECT * FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if(mysqli_num_rows($query)>0){
            $row =mysqli_fetch_assoc($query);
            $sequencia = $row['sequencia'];
        }
        return $sequencia;
    }

// *********************** METODOS DE ATULIZAÇÃO *********************** //
    // ===== CONSULTA NOME ===== //  
    public function atualizaNome($telefone,$nome,$status){
        $cmd = "UPDATE usuario SET nome = '$nome', status = $status  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // ===== CONSULTA STATUS ===== //  
    public function atualizaStatus($telefone,$status){
        $cmd = "UPDATE usuario SET status = $status  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // ===== CONSULTA OPÇAO1 ===== //  
    public function atualizaOpcao1($telefone,$opcao1){
        $cmd = "UPDATE usuario SET status = $opcao1  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // ===== CONSULTA SEQUENCIA ===== //  
    public function atualizaSequencia($telefone,$sequencia){
        $cmd = "UPDATE usuario SET status = $sequencia  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
}
?>