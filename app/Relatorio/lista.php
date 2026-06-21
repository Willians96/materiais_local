<?php

    require_once '../../lib/Database/Connection.php';
    require_once '../Model/Turma.php';
    require_once '../Model/Discente.php';
 

    $id    = $_GET['id'];
    $opcao = $_GET['opcao'];

    $turma     = Turma::buscaTurma($id);
    $discentes = Discente::listarDiscentes($id);

    $primeiro_dia  = date('d/m/Y', strtotime($turma[0]['inicio']));
    $segundo_dia   = date('d/m/Y', strtotime($turma[0]['inicio'] . ' +1 day'));
    $terceiro_dia  = date('d/m/Y', strtotime($turma[0]['termino']));
   
    $eap = $turma[0]['turma'] . ' - ' .  $primeiro_dia . ' a ' . $terceiro_dia;

    //Definição das colunas dependendo da opção
    if ($opcao == 1) {
        $titulo = 'LISTA DE PRESENÇA';

        $col_1_th = "<th>$primeiro_dia</th>";
        $col_1_td = "<td></td>";

        $col_2_th = "<th>$segundo_dia</th>";
        $col_2_td = "<td></td>";

        $col_3_th = "<th>$terceiro_dia</th>";
        $col_3_td = "<td></td>";

    } else if ($opcao == 2) {
        $titulo = 'PREVISÃO DE ALMOÇO';

        $col_1_th = "<th>$segundo_dia</th>";
        $col_1_td = "<td></td>";

        $col_2_th = "<th>$terceiro_dia</th>";
        $col_2_td = "<td></td>";

        $col_3_th = "";
        $col_3_td = "";

    } else if ($opcao == 3) {
        $titulo = 'LISTA DE CONTATOS';


        $col_1_th = "<th>TELEFONE</th>";
        $col_1_td = "<td></td>";

        $col_2_th = "";
        $col_2_td = "";

        $col_3_th = "";
        $col_3_td = "";


    } else if ($opcao == 4) {
        $titulo = 'TERMO DE RESPONSABILIDADE';

        $col_1_th = "<th>Nº CADEIRA</th>";
        $col_1_td = "<td></td>";

        $col_2_th = "<th>ASSINATURA</th>";
        $col_2_td = "<td></td>";

        $col_3_th = "";
        $col_3_td = "";


    } else if ($opcao == 5) {
        $titulo = 'LISTA DE ASSINATURA';

        $col_1_th = "<th>IAS <br> SIM/NÃO</th>";
        $col_1_td = "<td></td>";

        $col_2_th = "<th>EAD <br> SIM/NÃO</th>";
        $col_2_td = "<td></td>";

        $col_3_th = "<th>ASSINATURA</th>";
        $col_3_td = "<td></td>";


    }
    
    else {
        $titulo = 'LISTA';

        $col_1_th = "<th style='width: 200px;'></th>";
        $col_1_td = "<td></td>";

        $col_2_th = "";
        $col_2_td = "";

        $col_3_th = "";
        $col_3_td = "";

    }

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
            padding: 20px 40px;
        }

        .container_relatorio__cabecalho {
            text-align: center;
            margin-top: 5px;
        }

        .container_relatorio__cabecalho p{
            font-weight: 700;
            line-height: 10px;
        }

        .container__relatorio td,
        .container__relatorio th {
            border: 1px solid gray;
            vertical-align: middle;
            padding: 3px;
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
                padding: 10px;
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
            <p><?php echo $titulo; ?></p>
            <br>
        </div>


        <table class="table table-borded">
            <tr>
                <th>Nº</th>
                <th>PT/GRAD</th>
                <th>RE</th>
                <th>NOME</th>
                <th>OPM</th>
                <?php echo  $col_1_th;?>
                <?php echo  $col_2_th;?>
                <?php echo  $col_3_th;?>
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
                <?php 
                    if ($opcao == 1) {
                        echo '<td></td><td></td><td></td>';
                    }
                    else if ($opcao == 2 ) {
                        if ($discente['rancho_dia_2'] === 0 ) {
                            $rancho_dia_2 = 'Não';
                        } else if ($discente['rancho_dia_2'] === 1 ) {
                            $rancho_dia_2 = 'Sim';
                        } else {
                            $rancho_dia_2 = '';
                        }
                        if ($discente['rancho_dia_3'] === 0 ) {
                            $rancho_dia_3 = 'Não';
                        } else if ($discente['rancho_dia_3'] === 1 ) {
                            $rancho_dia_3 = 'Sim';
                        } else {
                            $rancho_dia_3 = '';
                        }
                        echo '<td>' . $rancho_dia_2 . '</td> <td>' . $rancho_dia_3 . '</td>';
                    } else if($opcao == 3) {
                        echo  '<td>' . $discente['telefone'] . '</td>';
                    } 
                    else if($opcao == 4 ) {
                        if ($discente['numero_cadeira'] != '' ) {
                            $cadeira = $discente['numero_cadeira'];
                        } else {
                            $cadeira = '';
                        }
                        echo '<td>' . $cadeira . '</td> <td>' . $discente['data_termo_responsabilidade'] . '</td>';
                    }
                    else if($opcao == 5 ) {
                        if ($discente['ias_em_dia'] === 0 ) {
                            $ias = 'Não';
                        } else if ($discente['ias_em_dia'] === 1 ) {
                            $ias = 'Sim';
                        } else {
                            $ias = '';
                        }
                        if ($discente['ead_em_dia'] === 0 ) {
                            $ead = 'Não';
                        } else if ($discente['ead_em_dia'] === 1 ) {
                            $ead = 'Sim';
                        } else {
                            $ead = '';
                        }
                        echo '<td>' . $ias . '</td> <td>' . $ead . '</td><td></td>';
                    }                    
                    else {
                        echo  $col_1_td;                        
                    }
                ?>
               <!-- <?php echo  $col_2_td;?>
                <?php echo  $col_3_td;?> -->

            </tr>           

        <?php
            }
        ?>

        </table>

    </div>   
    
</body>

</html>