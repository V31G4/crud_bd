<?php
require_once('inc.connect.php');

(isset($_POST['nome']) and !empty($_POST['nome'])) ? $nome = $_POST['nome'] : $erro = TRUE;
(isset($_POST['celular']) and !empty($_POST['celular'])) ? $celular = $_POST['celular'] : $erro = TRUE;
(isset($_POST['telefone']) and !empty($_POST['telefone'])) ? $telefone = $_POST['telefone'] : $erro = TRUE;
(isset($_POST['email']) and !empty($_POST['email'])) ? $email = $_POST['email'] : $erro = TRUE;
(isset($_POST['uf']) and !empty($_POST['uf'])) ? $uf = $_POST['uf'] : $erro = TRUE;
(isset($_REQUEST['acao']) and !empty($_REQUEST['acao'])) ? $acao = $_REQUEST['acao'] : $erro = TRUE;
(isset($_REQUEST['id_paciente']) and !empty($_REQUEST['id_paciente'])) ? $id_paciente = $_REQUEST['id_paciente'] : $erro = TRUE;

$nome_foto = $_FILES['foto']['name'];
$tmp_nome = $_FILES['foto']['tmp_name'];
$data_agora = date("YmdHis");
$dir = 'img/pacientes/';

switch ($acao) {
    case 'insert':
        $queryInsert = 'CREATE (p:Paciente {nome: "'.$nome.'", celular: "'.$celular.'", telefone: "'.$telefone.'", email: "'.$email.'", uf: "'.$uf.'"})
        RETURN p';
        $responseInsert = executeCypherQuery($queryInsert);
        if ($responseInsert) {
            $msg = 'cadastrado';
        } else {
            $msg = 'erro';
        }
        break;

        case 'update':
            $query = 'MATCH (p:Paciente) WHERE p.nome = "' . $id_paciente . '" 
            SET p.nome = "'.$nome.'",
                p.id_consulta = "'.$id_consulta.'", 
                p.celular = "'.$celular.'", 
                p.telefone = "'.$telefone.'", 
                p.email = "'.$email.'", 
                p.uf = "'.$uf.'"';

            echo $query;
            $result = executeCypherQuery($query, [
                'nome' => $nome,
                'id_consulta' => $id_consulta,
                'celular' => $celular,
                'telefone' => $telefone,
                'email' => $email,
                'uf' => $uf,
                'foto' => $foto
            ]);

                if ($query) {
                    $msg = 'alterado';
                } else {
                    $msg = 'erro na atualização do Paciente';
                }
        break;

        case 'delete':
            if ($_GET['acao'] == 'delete' && isset($_GET['id_paciente'])) {
                $id_paciente = $_GET['id_paciente'];
            
                // Execute a consulta Cypher para excluir o paciente com base no ID recebido
                $queryDeletePaciente = 'MATCH (p:Paciente {nome: "' . $id_paciente . '"}) DELETE p';
                $responseDeletePaciente = executeCypherQuery($queryDeletePaciente);
            
                if ($responseDeletePaciente) {
                    $msg = 'Paciente excluído com sucesso';
                } else {
                    $msg = 'Erro na exclusão do paciente';
                }
            }
}

header("location:index.php?pg=paciente&msg=" . $msg);

?>