<?php

    require_once '../../lib/Database/Connection.php';
    require_once '../Model/Turma.php';
    require_once '../Model/Discente.php';
 

    $id = $_GET['id'];

    $turma     = Turma::buscaTurma($id);
    $discentes = Discente::listarDiscentes($id);

    $primeiro_dia  = date('d/m/Y', strtotime($turma[0]['inicio']));
    $segundo_dia   = date('d/m/Y', strtotime($turma[0]['inicio'] . ' +1 day'));
    $terceiro_dia  = date('d/m/Y', strtotime($turma[0]['termino']));
   
    $eap = $turma[0]['turma'] . ' - ' .  $primeiro_dia . ' a ' . $terceiro_dia;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Assinatura</title>

    <!-- CSS dos ícones Bootstrap -->
    <link rel="stylesheet" href="../../public/assets/bootstrap/bootstrap-icons-1.11.3/font/bootstrap-icons.css">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../public/assets/bootstrap/css/bootstrap.min.css">


    <style>

        * {
            font-size: 12px;
        }
        
        body {
            background-color: gray;
            padding: 10px 20px;
            display: flex;
            justify-content: center;

        }

        .container__relatorio {
            box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
            background-color: #fff;
            padding: 10px;
            width: 793px;
            min-height: 1000px;
            /*min-height: 1122px;*/
        }

        .container_relatorio__cabecalho {
            text-align: center;
            margin-top: 10px;
        }

        .container_relatorio__cabecalho p{
            font-weight: 700;
            line-height: 10px;
        }

        .container__relatorio td,
        .container__relatorio th {
            border: 1px solid gray;
        }

        .container__relatorio table th {
            background-color: lightgrey;
        }

        #btn-imprimir {
            border-radius: 4px;
            width: 50px;
            height: 50px;
            position: fixed;
            margin-top: 10px;
            margin-left: 10px;
            display: grid;
            place-items: center;
            background-color: #0459a9;
            color: #fff;
            border: 2px solid #1a3697;
        }

        #btn-imprimir:hover {
            background-color: #1a3697;
        }


        @media print {

            body {
                /*imprimir cor*/
                -webkit-print-color-adjust: exact; /* Chrome, Safari */
                print-color-adjust: exact;         /* Firefox */
            }

            .container__relatorio {
                box-shadow: none;
            }

            .container__no_print {
                display: none;
            }
        }

        @page {
            size: auto;
            margin: 0mm;
        }
        

    </style>

</head>
<body>

        <div class="container__no_print">

            <button id="btn-imprimir"  onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" fill="currentColor"><path d="M640-640v-120H320v120h-80v-200h480v200h-80Zm-480 80h640-640Zm560 100q17 0 28.5-11.5T760-500q0-17-11.5-28.5T720-540q-17 0-28.5 11.5T680-500q0 17 11.5 28.5T720-460Zm-80 260v-160H320v160h320Zm80 80H240v-160H80v-240q0-51 35-85.5t85-34.5h560q51 0 85.5 34.5T880-520v240H720v160Zm80-240v-160q0-17-11.5-28.5T760-560H200q-17 0-28.5 11.5T160-520v160h80v-80h480v80h80Z"/></svg>
            </button>
        
        </div>

    <div class="container__relatorio">
        
        <div class="container_relatorio__cabecalho">
            <p>SECRETARIA DA SEGURANÇA PÚBLICA</p>
            <p>POLÍCIA MILITAR DO ESTADO DE SÃO PAULO</p>

            <p>CPI-7 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; GT</p>
            <br>
            <p><u><?php echo $eap ?></u></p>
            <br>
            <p>LISTA DE ASSINATURA</p>
            <br>
        </div>


        <table class="table table-borded">
            <tr>
                <th>Nº</th>
                <th>PT/GRAD</th>
                <th>RE</th>
                <th>NOME</th>
                <th>OPM</th>
                <th><?php echo  $primeiro_dia;?></th>
                <th><?php echo  $segundo_dia;?></th>
                <th><?php echo  $terceiro_dia;?></th>
            </tr>


        <?php

            foreach($discentes as $i => $discente) {
        ?>
            <tr>
                <td><?php echo  $i+1 ?></td>
                <td><?php if($discente['pt_gr']==='SD PM - 1C' or $discente['pt_gr']==='SD PM - 2C'){ echo 'SD PM'; }else{ echo  $discente['pt_gr'];}?></td>
                <td><?php echo  $discente['re'] . '-' . $discente['dg_re'];?></td>
                <td><?php echo  $discente['nome'];?></td>
                <td><?php echo  $discente['opm'];?></td>
                <td></td>
                <td></td>
                <td></td>

            </tr>           

        <?php
            }
        ?>

        </table>

    </div>   
    
</body>

<script>
    
    function imprimir()
    {
        
    }

</script>

</html>