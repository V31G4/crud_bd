<?php
require_once('inc.connect.php');

(isset($_POST['nome']) and !empty($_POST['nome'])) ? $nome = $_POST['nome'] : $erro = TRUE;
(isset($_POST['ie_ativo']) and !empty($_POST['ie_ativo'])) ? $ie_ativo = $_POST['ie_ativo'] : $erro = TRUE;
(isset($_POST['ds_area_atuacao']) and !empty($_POST['ds_area_atuacao'])) ? $ds_area_atuacao = $_POST['ds_area_atuacao'] : $erro = TRUE;
(isset($_REQUEST['acao']) and !empty($_REQUEST['acao'])) ? $acao = $_REQUEST['acao'] : $erro = TRUE;
(isset($_POST['id_medico']) and !empty($_POST['id_medico'])) ? $id_medico = $_POST['id_medico'] : $erro = TRUE;

$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'insert':
        $query = 'CREATE (m:Medico {nome: "'.$nome.'", ie_ativo: "'.$ie_ativo.'", ds_area_atuacao: "'.$ds_area_atuacao.'"}) RETURN m.nome AS nome, m.ie_ativo AS ie_ativo, m.ds_area_atuacao AS ds_area_atuacao';

        $parameters = [
            'nome' => $nome,
            'ie_ativo' => $ie_ativo,
            'ds_area_atuacao' => $ds_area_atuacao
        ];
        $result = executeCypherQuery($query, $parameters);

        if ($result) {
            $msg = 'cadastrado';
        } else {
            $msg = 'erro';
        }
        break;

        case 'update':
            $query = 'MATCH (m:Medico { nome: "' . $nome . '" }) 
            SET m.nome = "' . $nome . '", 
                m.ie_ativo = "' . $ie_ativo . '", 
                m.ds_area_atuacao = "' . $ds_area_atuacao . '"';

            $result = executeCypherQuery($query, [
                'nome' => $nome,
                'ie_ativo' => $ie_ativo,
                'ds_area_atuacao' => $ds_area_atuacao
            ]);
        
            if ($result) {
                $msg = 'Dados atualizados com sucesso';
            } else {
                $msg = 'Erro ao atualizar os dados';
            }
        
            header("location: index.php?pg=medico&msg=" . $msg);
            break;

    case 'delete':
        if ($_GET['acao'] == 'delete' && isset($_GET['id_medico'])) {
            $id_medico = $_GET['id_medico'];
            $queryDeleteMedico = 'MATCH (m:Medico { nome: "' . $id_medico . '" }) DETACH DELETE m';
            $responseDeleteMedico = executeCypherQuery($queryDeleteMedico);

            if ($responseDeleteMedico) {
                $msg = 'Médico excluído com sucesso';
            } else {
                $msg = 'Erro na exclusão do médico';
            }
        }
}
header("location:index.php?pg=medico&msg=" . $msg);

?>