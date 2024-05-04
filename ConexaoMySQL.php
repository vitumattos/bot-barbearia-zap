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
    public function cadastroCliente($telefone,$nome,$status,$data_cadastro,$opcao, $opcao_adm){
        $cmd = "INSERT INTO cliente (telefone,nome,status,data_cadastro,opcao,opcao_adm) VALUE ('$telefone','$nome','$status','$data_cadastro','$opcao','$opcao_adm')";
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
    // ===== REGISTRA CORTE ===== //
    public function registraCorte($telefone,$dia,$hora){
        $cmd = "INSERT INTO historico_corte (telefone, dia, hora) VALUE ('$telefone','$dia','$hora')";
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

    // ===== CONSULTA OPCAO_ADM ===== //
    public function consultaOpcaoADM($telefone){
        $cmd = "SELECT opcao_adm FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if($query){
            $res =mysqli_fetch_assoc($query);
            $opcao = $res['opcao_adm'];
        }
        return $opcao;
    }
    // ===== CONSULTA NOME ===== //
    public function consultaNome($telefone){
        $cmd = "SELECT nome FROM cliente WHERE telefone= '$telefone'";
        $query = mysqli_query($this->conn,$cmd);

        if($query){
            $res = mysqli_fetch_assoc($query);
            $nome = $res['nome'];
        }
        return $nome;
    }
    // ===== CONSULTA CLIENTES ===== //
    public function consultaClientes(){
        $cmd = "SELECT telefone, nome,status FROM cliente";
        $query = mysqli_query($this->conn,$cmd);

        if($query){
            $clientes = array();
            while ($cliente = mysqli_fetch_assoc($query)){
                $clientes[] = $cliente;
            }
            return $clientes;
        } else {
            return array();
        }
    }
    // ===== CONSULTA FILA ===== //
    public function consultaFila(){
        $cmd = "SELECT * FROM fila";
        $query = mysqli_query($this->conn,$cmd);

        if($query){
            $fila = array();
            $clientes = array();
            $telefones = array();
            while ($res = mysqli_fetch_assoc($query)){
                $clientes[] = $res['nome'];
                $telefones[] = $res['telefone'];
            }
           
            $fila['qtd'] = count($clientes);
            $fila['nome'] = $clientes;
            $fila['telefone'] = $telefones;
        }else {
            $fila = array(
                "qtd" => 0,
                "nome" => array(),
                "telefone" => array()
            );
        }
        return $fila;
    }

// *********************** METODOS DE ATULIZAÇÃO *********************** //
    // ===== ATUALIZA NOME ===== //  
    public function atualizaNome($telefone,$nome){
        $cmd = "UPDATE cliente SET nome = '$nome' WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // ===== ATUALIZA STATUS ===== //  
    public function atualizaStatus($telefone,$status){
        $cmd = "UPDATE cliente SET status = '$status ' WHERE telefone = '$telefone'";
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
    // ===== ATUALIZA OPÇAO_ADM ===== //  
    public function atualizaOpcaoADM($telefone,$opcaoADM){
        $cmd = "UPDATE cliente SET opcao_adm = '$opcaoADM'  WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
    // *********************** METODOS DE REMOÇÃO *********************** //
    // ===== REMOVER FILA ===== //  
    public function removeFila($telefone){
        $cmd = "DELETE FROM fila WHERE telefone = '$telefone'";
        $query = mysqli_query($this->conn,$cmd);
        if ($query) {
            return true; // Atualização bem-sucedida
        } else {
            return false; // Falha na atualização
        }
    }
}
?>