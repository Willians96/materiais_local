<?php


class Turma {
    /*private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }*/

    public static function listarTodas() {
        $con = Connection::getConn();

        //busca os registros da tabela turmas e a quantidade discentes
    
        $sql = "SELECT 
                    t.*, 
                    (SELECT COUNT(*) FROM eap_discentes d WHERE d.turmas_id_turma = t.id_turma) AS total_discentes
                FROM eap_turmas t
                ORDER BY t.inicio DESC";
    
        $stmt = $con->prepare($sql);
        $stmt->execute();
    
        $resultado = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $id_turma = $row["id_turma"]; 

            $total['total_ead'] = self::verificaEadTurma($id_turma);
            $total['total_avaliacao'] = self::verificaAvaliacaoTurma($id_turma);
            $total['total_taf'] = self::verificaTafTurma($id_turma);
            $total['total_tat'] = self::verificaTatTurma($id_turma);


            if ($total['total_ead'] < $row["total_discentes"]) {
                $total['cor_ead'] = '#DEE2E6';
            } else {
                $total['cor_ead'] = '#198754';
            }

            if ($total['total_taf'] < $row["total_discentes"]) {
                $total['cor_taf'] = '#DEE2E6';
            } else {
                $total['cor_taf'] = '#198754';
            }


            if ($total['total_tat'] < $row["total_discentes"]) {
                $total['cor_tat'] = '#DEE2E6';
            } else {
                $total['cor_tat'] = '#198754';
            }

            if ($total['total_avaliacao'] < $row["total_discentes"]) {
                $total['cor_avaliacao'] = '#DEE2E6';
            } else {
                $total['cor_avaliacao'] = '#198754';
            }
            $resultado[] = array_merge($row, $total);

        }
   
        return $resultado;
    }


    public static function turmasEapConcluidas($id_eap)
    {
        $con = Connection::getConn();
        $hoje = date('Y-m-d');

        $sql = "SELECT COUNT(*) AS total
                FROM eap_turmas 
                WHERE eap_id_eap = :id_eap
                AND termino < :hoje;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_eap', $id_eap);
        $stmt->bindValue(':hoje',   $hoje);
        $stmt->execute();
        $resultado = $stmt->fetchColumn(); // pega o valor da contagem

        return $resultado;
    }


    public static function verificaEadTurma($id_turma)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) 
                FROM eap_discentes
                WHERE nota_ead is not null and turmas_id_turma = :id_turma";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(":id_turma", $id_turma);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }


    public static function verificaAvaliacaoTurma($id_turma)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) 
                FROM eap_discentes
                WHERE avaliacao_nota is not null and turmas_id_turma = :id_turma";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(":id_turma", $id_turma);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }


    public static function verificaTatTurma($id_turma)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) 
                FROM eap_discentes
                WHERE
                    (pontuacao_tat != '' or nota_tat != '' or conceito_tat != '')  
                    and turmas_id_turma = :id_turma";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(":id_turma", $id_turma);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }


    public static function verificaTafTurma($id_turma)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) 
                FROM eap_discentes
                WHERE
                    (pontuacao_taf != '' or conceito_taf != '')  
                    and turmas_id_turma = :id_turma";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(":id_turma", $id_turma);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    


    public function incluirTurma($dados) {

        $inicio = new DateTime($dados['inicio']);
        $inicio->modify('+1 day');
        $dataTaf = $inicio->format('Y-m-d');

        $id_eap = Config::configuracoes()['eap_id_eap'];

        $con = Connection::getConn();

        $sql = "INSERT INTO eap_turmas (turma, tipo, inicio, termino, usuario, data_taf, data_tat, eap_id_eap) 
                VALUES (:turma, :tipo, :inicio, :termino, :usuario, :taf, :tat, :eap_id_eap)";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':turma', $dados['turma']);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':inicio', $dados['inicio']);
        $stmt->bindValue(':termino', $dados['termino']);
        $stmt->bindValue(':usuario', $_SESSION['usuario']); 
        $stmt->bindValue('taf', $dataTaf);
        $stmt->bindValue('tat', $dados['inicio']);
        $stmt->bindValue('eap_id_eap', $id_eap);

    
        return $stmt->execute();
    }


    public function removerTurma($id) {
        $con = Connection::getConn();

        $sql = "DELETE FROM eap_turmas WHERE id_turma = :id_turma";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_turma', $id);

        return $stmt->execute();
    }


    public function editarTurma($dados)
    {
        $con = Connection::getConn();
    
        $sql = "UPDATE eap_turmas 
                SET turma = :turma, 
                    tipo = :tipo, 
                    inicio = :inicio, 
                    termino = :termino,
                    usuario = :usuario,
                    data_tat = :data_tat,
                    data_taf = :data_taf
                WHERE id_turma = :id";
    
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':turma', $dados['turma']);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':inicio', $dados['inicio']);
        $stmt->bindValue(':termino', $dados['termino']);
        $stmt->bindValue(':usuario', $_SESSION['usuario']); 
        $stmt->bindValue(':data_tat', $dados['tat']);
        $stmt->bindValue(':data_taf', $dados['taf']);
        $stmt->bindValue(':id', $dados['id']); // id_turma
    
        return $stmt->execute();
    }


    public function incluirPMTurma($dados, $idTurma)
    {
        $con = Connection::getConn();

        $sql = "INSERT INTO eap_discentes (
                    ptgr, re, dgre, nome, guerra, email, opm, codopm, funcao, ias, turmas_id_turma, usuario
                ) VALUES (
                    :ptgr, :re, :dgre, :nome, :guerra, :email, :opm, :codopm, :funcao, :ias, :turmas_id_turma, :usuario
                )";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':ptgr', $dados['ptgr']);
        $stmt->bindValue(':re', $dados['re']);
        $stmt->bindValue(':dgre', $dados['dgre']);
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':guerra', $dados['guerra']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':opm', $dados['opm']);
        $stmt->bindValue(':codopm', $dados['codopm']);
        $stmt->bindValue(':funcao', $dados['funcao']);
        $stmt->bindValue(':ias', $dados['ias']);
        $stmt->bindValue(':turmas_id_turma', $idTurma);
        $stmt->bindValue(':usuario', $_SESSION['usuario']);

        return $stmt->execute();
    }


    public static function buscaTurma($id)
    {
        $con = Connection::getConn();

    
        $sql = "SELECT 
                    t.*, 
                    (SELECT COUNT(*) FROM eap_discentes d WHERE d.turmas_id_turma = t.id_turma) AS total_discentes
                FROM eap_turmas t
                WHERE id_turma = :id
                ORDER BY t.inicio DESC
                LIMIT 1";
    
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    
        $resultado = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[] = $row;
        }
   
        return $resultado;
    }

    public static function gerarXML($id_turma)
    {
       
        $discentes = Discente::listarDiscentes($id_turma);
        $turma = Turma::buscaTurma($id_turma)[0] ?? [];
   
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="NBI ' . $turma['turma'] . ' ' . substr($turma['inicio'], 0, 5). '.xml"');
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '
                <?mso-application progid="Excel.Sheet"?>
                <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
                <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
                <Author>Edson</Author>
                <LastAuthor>Edson</LastAuthor>
                <Created>2013-12-28T16:35:21Z</Created>
                <LastSaved>2014-01-05T20:29:24Z</LastSaved>
                <Company>Hewlett-Packard</Company>
                <Version>15.00</Version>
                </DocumentProperties>
                <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
                <AllowPNG/>
                </OfficeDocumentSettings>
                <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
                <WindowHeight>11715</WindowHeight>
                <WindowWidth>28800</WindowWidth>
                <WindowTopX>0</WindowTopX>
                <WindowTopY>0</WindowTopY>
                <ProtectStructure>False</ProtectStructure>
                <ProtectWindows>False</ProtectWindows>
                </ExcelWorkbook>
                <Styles>
                <Style ss:ID="Default" ss:Name="Normal">
                <Alignment ss:Vertical="Bottom"/>
                <Borders/>
                <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
                <Interior/>
                <NumberFormat/>
                <Protection/>
                </Style>
                <Style ss:ID="s62">
                <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
                <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
                </Style>
                <Style ss:ID="s63">
                <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
                <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
                </Style>
                <Style ss:ID="s64">
                <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
                <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
                <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
                </Style>
                <Style ss:ID="s65">
                <NumberFormat ss:Format="Short Date"/>
                </Style>
                </Styles>
                <Worksheet ss:Name="Plan1">
                <Table ss:ExpandedColumnCount="15" ss:ExpandedRowCount="30" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="15">
                <Column ss:Width="35.25"/>
                <Column ss:AutoFitWidth="0" ss:Width="82.5"/>
                <Column ss:Width="57"/>
                <Column ss:Width="21.75"/>
                <Column ss:Width="162"/>
                <Column ss:Width="48.75"/>
                <Column ss:Width="46.5"/>
                <Column ss:Width="55.5"/>
                <Column ss:Width="61.5"/>
                <Column ss:Width="55.5"/>
                <Column ss:Index="12" ss:AutoFitWidth="0" ss:Width="63"/>
                <Column ss:Width="56.25" ss:Span="2"/>
                <Row ss:AutoFitHeight="0">
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">OPM</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">Posto Grad</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">RE SEM DIG</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">DIG</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">Nome</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">nota_eap</Data>
                </Cell>
                <Cell ss:StyleID="s63">
                <Data ss:Type="String">Conceito</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">pontos_taf</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">conceito</Data>
                </Cell>
                <Cell ss:StyleID="s62">
                <Data ss:Type="String">pontos_tat</Data>
                </Cell>
                <Cell ss:StyleID="s63">
                <Data ss:Type="String">conceito</Data>
                </Cell>
                <Cell ss:StyleID="s64">
                <Data ss:Type="String">inicio_eap</Data>
                </Cell>
                <Cell ss:StyleID="s64">
                <Data ss:Type="String">fim_eap</Data>
                </Cell>
                <Cell ss:StyleID="s64">
                <Data ss:Type="String">data_taf</Data>
                </Cell>
                <Cell ss:StyleID="s64">
                <Data ss:Type="String">data_tat</Data>
                </Cell>
                </Row>';


                foreach ($discentes as $d) {

                    $nota_eap = (float)($d['nota_ead'] ?? 0) + (float)($d['avaliacao_nota'] ?? 0);

                    if ($nota_eap < 5) {
                        $conceito_eap = 'Insuficiente';
                    } elseif ($nota_eap < 7) {
                        $conceito_eap = 'Regular';
                    } elseif ($nota_eap < 8.5) {
                        $conceito_eap = 'Bom';
                    } elseif ($nota_eap < 9.6) {
                        $conceito_eap = 'MB';
                    } else {
                        $conceito_eap = 'Excep.';
                    }; 

                    echo '
                        <Row ss:AutoFitHeight="0">
                            <Cell>
                                <Data ss:Type="String">'. $d['opm'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="String">'. $d['pt_gr'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="Number">'. $d['re'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="String">'. $d['dg_re'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="String">'. $d['nome'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="Number">'. $nota_eap .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="String"> '. $conceito_eap .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="Number">'. $d['pontuacao_taf'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="String">'. $d['conceito_taf'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="Number">'. $d['pontuacao_tat'] .'</Data>
                            </Cell>
                            <Cell>
                                <Data ss:Type="String">'. $d['conceito_tat'] .'</Data>
                            </Cell>
                            <Cell ss:StyleID="s65">
                                <Data ss:Type="DateTime">'. $turma['inicio'] .'</Data>
                            </Cell>
                            <Cell ss:StyleID="s65">
                                <Data ss:Type="DateTime">'. $turma['termino'] .'</Data>
                            </Cell>
                            <Cell ss:StyleID="s65">
                                <Data ss:Type="DateTime">'. $turma['data_taf'] .'</Data>
                            </Cell>
                            <Cell ss:StyleID="s65">
                                <Data ss:Type="DateTime">'. $turma['data_tat'] .'</Data>
                            </Cell>
                        </Row>';
                }


                echo '
                
                </Table>
                <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
                <PageSetup>
                <Header x:Margin="0.31496062000000002"/>
                <Footer x:Margin="0.31496062000000002"/>
                <PageMargins x:Bottom="0.78740157499999996" x:Left="0.511811024" x:Right="0.511811024" x:Top="0.78740157499999996"/>
                </PageSetup>
                <Unsynced/>
                <Print>
                <ValidPrinterInfo/>
                <PaperSizeIndex>9</PaperSizeIndex>
                <HorizontalResolution>600</HorizontalResolution>
                <VerticalResolution>600</VerticalResolution>
                </Print>
                <Selected/>
                <Panes>
                <Pane>
                <Number>3</Number>
                <ActiveRow>17</ActiveRow>
                <ActiveCol>7</ActiveCol>
                </Pane>
                </Panes>
                <ProtectObjects>False</ProtectObjects>
                <ProtectScenarios>False</ProtectScenarios>
                </WorksheetOptions>
                </Worksheet>
                <Worksheet ss:Name="Plan2">
                <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="15">
                <Row ss:AutoFitHeight="0"/>
                </Table>
                <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
                <PageSetup>
                <Header x:Margin="0.31496062000000002"/>
                <Footer x:Margin="0.31496062000000002"/>
                <PageMargins x:Bottom="0.78740157499999996" x:Left="0.511811024" x:Right="0.511811024" x:Top="0.78740157499999996"/>
                </PageSetup>
                <Unsynced/>
                <ProtectObjects>False</ProtectObjects>
                <ProtectScenarios>False</ProtectScenarios>
                </WorksheetOptions>
                </Worksheet>
                <Worksheet ss:Name="Plan3">
                <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="15">
                <Row ss:AutoFitHeight="0"/>
                </Table>
                <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
                <PageSetup>
                <Header x:Margin="0.31496062000000002"/>
                <Footer x:Margin="0.31496062000000002"/>
                <PageMargins x:Bottom="0.78740157499999996" x:Left="0.511811024" x:Right="0.511811024" x:Top="0.78740157499999996"/>
                </PageSetup>
                <Unsynced/>
                <ProtectObjects>False</ProtectObjects>
                <ProtectScenarios>False</ProtectScenarios>
                </WorksheetOptions>
                </Worksheet>
                </Workbook>';

        exit;  
    }


    public static function gerarXML__back__($id_turma)
    {
        $con = Connection::getConn();

        $sql = "SELECT pt_gr, re, nome, opm, nota_ead 
                FROM eap_discentes 
                WHERE turmas_id_turma = :id";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id', $id_turma, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$dados) {
            return 'Nenhum discente encontrado.';
        }

        // Criando o XML
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('discentes');
        $dom->appendChild($root);

        foreach ($dados as $discente) {
            $item = $dom->createElement('discente');

            $item->appendChild($dom->createElement('pt_gr', $discente['pt_gr']));
            $item->appendChild($dom->createElement('re', $discente['re']));
            $item->appendChild($dom->createElement('nome', $discente['nome']));
            $item->appendChild($dom->createElement('opm', $discente['opm']));
            $item->appendChild($dom->createElement('nota_ead', $discente['nota_ead']));

            $root->appendChild($item);
        }

        // Caminho do arquivo
        $caminho = "app/xml/discentes_turma_$id_turma.xml";
        $dom->save($caminho);

        return $caminho; // Pode retornar o caminho para download, se quiser
    }


    public static function desabilitaTodasAvaliacoes()
    {
        $con = Connection::getConn();
        
        // Desabilitar apenas do ano ou id eap
        $sql = "UPDATE eap_turmas SET habilita_avaliacao = 0";
        $stmt = $con->prepare($sql);
        
        return $stmt->execute();
    }


    public static function habilitaDesabilitaAvaliacao($dados)
    {
        $con = Connection::getConn();

        $id_turma = $dados['id_turma'];
        
        if (isset($dados['habilita_avaliacao'])) {
            $habilita_avaliacao = 1;
        } else {
            $habilita_avaliacao = 0;
        }

        $sql = "UPDATE eap_turmas 
                SET
                    habilita_avaliacao = :habilita
                WHERE 
                    id_turma = :id_turma;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':habilita', $habilita_avaliacao);
        $stmt->bindValue(':id_turma', $id_turma);

        return $stmt->execute();
        
    }

    
    public static function gravarBI($dados)
    {
        $con = Connection::getConn();

        $sql = "UPDATE eap_turmas
                SET
                    num_bi = :num_bi,
                    ano_bi = :ano_bi
                WHERE
                    id_turma = :id_turma;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':num_bi', $dados['num-bi']);
        $stmt->bindValue(':ano_bi', $dados['ano-bi']);
        $stmt->bindValue(':id_turma', $dados['id_turma']);

        return $stmt->execute();        
    }



    public static function turmaAtual()
    {
        $con = Connection::getConn();
        $hoje = date('Y-m-d');

        // 1) Turma em andamento (inicio <= hoje <= termino)
        $sql = "SELECT * 
                  FROM eap_turmas
                 WHERE inicio <= :hoje 
                   AND termino >= :hoje
              ORDER BY inicio DESC 
                 LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->execute();
        $turma = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2) Se não achou em andamento → pega a anterior
        if (!$turma) {
            $sql = "SELECT * 
                      FROM eap_turmas
                     WHERE termino < :hoje
                  ORDER BY termino DESC 
                     LIMIT 1";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':hoje', $hoje);
            $stmt->execute();
            $turma = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $turma ?: null;
    }

    public static function turmaAnterior()
    {
        $con = Connection::getConn();
        $hoje = date('Y-m-d');

        $sql = "SELECT * 
                  FROM eap_turmas
                 WHERE termino < :hoje
              ORDER BY termino DESC 
                 LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function turmaProxima()
    {
        $con = Connection::getConn();
        $hoje = date('Y-m-d');

        $sql = "SELECT * 
                  FROM eap_turmas
                 WHERE inicio > :hoje
              ORDER BY inicio ASC 
                 LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function listarTurmaBusca($busca)
    {
        $con = Connection::getConn();

        $sql = "SELECT 
                    t.*, 
                    (SELECT COUNT(*) 
                    FROM eap_discentes d 
                    WHERE d.turmas_id_turma = t.id_turma) AS total_discentes
                FROM eap_turmas t
                INNER JOIN eap_discentes d ON d.turmas_id_turma = t.id_turma
                WHERE d.nome LIKE :busca
                OR d.guerra LIKE :busca
                OR d.cpf LIKE :busca
                OR d.re LIKE :busca
                GROUP BY t.id_turma
                ORDER BY t.inicio DESC";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':busca', "%{$busca}%", PDO::PARAM_STR);
        $stmt->execute();

        $resultado = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id_turma = $row["id_turma"]; 

            $total['total_ead'] = self::verificaEadTurma($id_turma);
            $total['total_avaliacao'] = self::verificaAvaliacaoTurma($id_turma);
            $total['total_taf'] = self::verificaTafTurma($id_turma);
            $total['total_tat'] = self::verificaTatTurma($id_turma);


            if ($total['total_ead'] < $row["total_discentes"]) {
                $total['cor_ead'] = '#DEE2E6';
            } else {
                $total['cor_ead'] = '#198754';
            }

            if ($total['total_taf'] < $row["total_discentes"]) {
                $total['cor_taf'] = '#DEE2E6';
            } else {
                $total['cor_taf'] = '#198754';
            }


            if ($total['total_tat'] < $row["total_discentes"]) {
                $total['cor_tat'] = '#DEE2E6';
            } else {
                $total['cor_tat'] = '#198754';
            }

            if ($total['total_avaliacao'] < $row["total_discentes"]) {
                $total['cor_avaliacao'] = '#DEE2E6';
            } else {
                $total['cor_avaliacao'] = '#198754';
            }
            $resultado[] = array_merge($row, $total);
        }

        return $resultado;
    }


    
    
}

?>
