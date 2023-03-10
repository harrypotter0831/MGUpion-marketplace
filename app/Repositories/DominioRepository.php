<?php

namespace MGLara\Repositories;
    
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use MGLara\Models\Filial;

/**
 * Description of DominioRepository
 * 
 * @property  Validator $validator
 * @property  Dominio $model
 */
class DominioRepository {

    /**
     * Verifica se usuario tem permissao
     * 
     * @param type $ability
     * @return boolean
     */
    public function authorize($ability) {
        //return true;
        //dd("DominioPolicy.{$ability}");
        if (!Gate::allows($ability, $this)) {
            abort(403);
        }
        return true;
    }
    
    public function parseNomeArquivo($arquivo, $data = null, $codfilial = null) {
        
        //Modelo {ano}{mes}-{empresa}-Estoque.txt
        if (!empty($data)) {
            $arquivo = str_replace('{ano}', $data->format('Y'), $arquivo);
            $arquivo = str_replace('{mes}', $data->format('m'), $arquivo);
            $arquivo = str_replace('{dia}', $data->format('d'), $arquivo);
        }
        
        //Busca Filial
        if (!empty($codfilial)) {
            $filial = Filial::findOrFail($codfilial);
            $arquivo = str_replace('{empresa}', $filial->empresadominio, $arquivo);
        }
        
        // retorna arquivo
        return $arquivo;
        
    }    
    
    public function padNumber ($input, $pad_length) {
        return $this->padAndCut($input, $pad_length, '0', STR_PAD_LEFT);
    }
    
    public function padText ($input, $pad_length) {
        return $this->padAndCut($input, $pad_length, ' ', STR_PAD_RIGHT);
    }
    
    public function padAndCut($input, $pad_length, $pad_string, $pad_type) {
        $ret = str_pad($input, $pad_length, $pad_string, $pad_type);
        if (strlen($ret) > $pad_length) {
            if ($pad_type == STR_PAD_LEFT) {
                $ret = substr($ret, $pad_length * -1);
            } else {
                $ret = substr($ret, 0, $pad_length);
            }
        }
        return $ret;
    }


    public function exportaProdutos ($arquivo, $codfilial, $estoque = false, $mes = null) {

        ini_set('memory_limit', '300M');
        ini_set('max_execution_time', '0');

        // Busca Registros
        $sql = "
            select 
                  p.*
                , sld.saldoquantidade
                , sld.saldovalor
                , sld.customedio
                , um.sigla
                , ncm.ncm
            from (
                select 
                    iq_el.codfilial
                    , iq_pv.codproduto
                    , sum(iq_mes.saldoquantidade) as saldoquantidade
                    , sum(iq_mes.saldovalor) as saldovalor
                    , case when (sum(iq_mes.saldoquantidade) != 0 ) then sum(iq_mes.saldovalor) / sum(iq_mes.saldoquantidade) else null end as customedio
                from 	(select 
                        iq_iq_sld.*, 
                        (select iq_iq_mes.codestoquemes
                            from tblestoquemes iq_iq_mes
                            where iq_iq_mes.mes <= '{$mes->format('Y-m-d')}'
                            and iq_iq_mes.codestoquesaldo = iq_iq_sld.codestoquesaldo
                            order by iq_iq_mes.mes desc
                            limit 1
                        ) as codestoquemes
                    from tblestoquesaldo iq_iq_sld
                    where iq_iq_sld.fiscal = true
                    ) iq_sld
                inner join tblestoquemes iq_mes on (iq_mes.codestoquemes = iq_sld.codestoquemes)
                inner join tblestoquelocalprodutovariacao iq_elpv on (iq_elpv.codestoquelocalprodutovariacao = iq_sld.codestoquelocalprodutovariacao)
                inner join tblestoquelocal iq_el on (iq_el.codestoquelocal = iq_elpv.codestoquelocal)
                inner join tblprodutovariacao iq_pv on (iq_pv.codprodutovariacao = iq_elpv.codprodutovariacao)
                where iq_el.codfilial = $codfilial
                group by iq_el.codfilial
                    , iq_pv.codproduto
                having sum(iq_mes.saldoquantidade) > 0
                ) sld
            inner join tblproduto p on (p.codproduto = sld.codproduto)
            inner join tblunidademedida um on (um.codunidademedida = p.codunidademedida)
            inner join tblncm ncm on (ncm.codncm = p.codncm)
            ";

        $regs = DB::select($sql);

        //dados prontos
        $filial = Filial::findOrFail($codfilial);
        $observacao = $this->padText('MGLara @ ' . Carbon::now()->format('d/m/Y H:i:s'), 40);
        $data = $this->padText($mes->format('d/m/Y'), 10);
        
        //percorre linhas
        $linhas = [];
        foreach ($regs as $reg) {
            
            //Vers??o do Layout Fixo "6"
            //1 a 1
            $linha = '4';
            
            //C??digo do Produto
            //2 a 15
            $linha .= $this->padText(str_pad($reg->codproduto, 6, '0', STR_PAD_LEFT), 14);
                
            //C??digo da Empresa
            //16 a 22
            $linha .= $this->padNumber($filial->empresadominio, 7);

            //C??digo do Grupo - de acordo com Tipo do item (0=Mercadoria, 1=Mat??ria Prima, 2=Produto Intermedi??rio, 3=Produto em Fabrica????o, 4=Produto Acabado, 5=Embalagem, 6=Subproduto, 7=Material de Uso e Consumo, 8=Ativo Imobilizado, 9=Servi??os, 10=Outros Insumos, 99=Outras)
            //23 a 29
            switch ($reg->codtipoproduto) {
                case 8: //Imobilizado
                    $grupo = 3;
                    break;
                case 7: //Material de uso e consumo
                    $grupo = 2;
                    break;
                default: //para todo o resto
                    $grupo = 1;
            }
            $linha .= $this->padNumber($grupo, 7);
            
            //C??digo NBM
            //30 a 39
            $linha .= $this->padText('', 10);
            
            //Descri????o do Produto
            //40 a 79
            $linha .= $this->padText($reg->produto, 40);
                
            //Unidade de Medida
            //80 a 85
            $linha .= $this->padText($reg->sigla, 6);
            
            //Valor Unit??rio (3 casas decimais)
            //86 a 98
            $linha .= $this->padNumber($reg->preco * 1000, 13);
            
            //Quantidade Inicial (4 casas decimais)
            //99 a 109
            $linha .= $this->padNumber(0, 11);
                
            //Quantidade Final (4 casas decimais)
            //110 a 120
            //Este campo eh ignorado pelo importador, a quantidade do estoque deve ser passada na posicao 708
            $linha .= $this->padNumber(0, 11);
            
            //Valor Inicial do Estoque (3 casas decimais)
            //121 a 133
            $linha .= $this->padNumber(0, 13);

            //Valor Final do Estoque (3 casas decimais)
            //134 a 146
            $linha .= $this->padNumber($reg->saldovalor * 1000, 13);
                
            //Al??quota do IPI (2 casas decimais)
            //147 a 151
            $linha .= $this->padNumber(0, 5);

            //Observa????o
            //152 a 191
            $linha .= $observacao;
            
            //C??digo Ncm
            //192 a 199
            $linha .= $this->padText($reg->ncm, 8);

            //Branco
            //200 a 204
            $linha .= $this->padText('', 5);

            //Esp??cie (Para DNF)
            //205 a 206
            $linha .= $this->padNumber(0, 2);
            
            //Branco
            //207 a 211
            $linha .= $this->padText('', 5);

            //Unidade Padr??o (Para DNF)
            //212 a 213
            $linha .= $this->padNumber(0, 2);

            //Fator de Convers??o (Para DNF) (3 casas decimais)
            //214 a 227
            $linha .= $this->padNumber(0, 14);

            //Exporta Para DNF (S/N)
            //228 a 228
            $linha .= 'N';

            //C??digo da Situa????o Tribut??ria
            //229 a 235
            $linha .= $this->padNumber(0, 7);

            //Branco
            //236 a 252
            $linha .= $this->padText('', 17);

            //Branco
            //253 a 253
            $linha .= $this->padText('', 1);

            //C??digo EAN(DIC / Sergipe)
            //254 a 267
            $linha .= $this->padText('', 14);

            //C??digo de Produto Relevante (DIC/Sergipe)
            //268 a 274
            $linha .= $this->padNumber(0, 7);

            //Data do Saldo Final
            //275 a 284
            //ADICIONAR DATA
            $linha .= $data;            

            //C??digo do Produto conforme Anexos I ou II (DNF)
            $linha .= $this->padNumber(0, 7);

            //Capacidade Volum??trica(ml)(DNF)
            $linha .= $this->padNumber(0, 7);

            //Incentivo Fiscal(S / N)
            $linha .= 'N';

            //Gera informa????es para o GRF-CBT (S/N)
            $linha .= 'N';

            //C??digo do produto (GRF-CTB)
            $linha .= $this->padNumber(0, 7);
            
            //Gera informa????es para SCANC (S/N)
            $linha .= 'N';

            //C??digo do produto (SCANC)
            $linha .= $this->padNumber(0, 7);

            //Este produto cont??m Gasolina A (S/N) (SCANC)
            $linha .= 'N';

            //Unidade
            $linha .= 'UN';

            //Tipo Produto / Servi??o - Somente para DIEF (1 Mercadoria/2 Servico com incidencia de ICMS/3 Servico sem incidencia de ICMS).
            $linha .= '1';

            //Gera informa????es registro 88ST Sintegra (S/N)
            $linha .= 'N';

            //C??digo do produto na tabela SEFAZ (88ST Sintegra)
            $linha .= $this->padNumber(0, 7);

            //Al??quota ICMS (2 casas decimais)
            $linha .= $this->padNumber(0, 5);

            //Tipo (V=Ve??culos Novos, M=Medicamentos, A=Armas de Fogo, O=Outros)
            $linha .= 'O';

            //Tipo arma (0=Uso Permitido, 1=Uso Restrito)
            $linha .= '0';

            //Descri????o da arma
            $linha .= $this->padText('', 255);

            //G??nero
            $linha .= $this->padNumber(0, 7);

            //C??digo Servi??o
            //$linha .= $this->padNumber(0, 7)
            $linha .= $this->padText('', 7);

            //Classifica????o
            $linha .= $this->padNumber(0, 7);
            

            //Tipo do item (0=Mercadoria, 1=Mat??ria Prima, 2=Produto Intermedi??rio, 3=Produto em Fabrica????o, 4=Produto Acabado, 5=Embalagem, 6=Subproduto, 7=Material de Uso e Consumo, 8=Ativo Imobilizado, 9=Servi??os, 10=Outros Insumos, 99=Outras)
            $linha .= $this->padNumber($reg->codtipoproduto, 2);

            //Ncm Exterior
            $linha .= '00';

            //C??digo Imposto Importa????o
            $linha .= '00';

            //Produto Composto(S / N)
            $linha .= 'N';

            //Informa????es complementares do IPM da PDI (S/N) ??? Obs: Apenas para o Estado de GO
            $linha .= 'N';

            //C??digo produto/servi??o do IPM da DPI
            $linha .= '  ';
            
            //Cesta B??sica(S / N)
            $linha .= 'N';

            //C??digo do produto DAM
            $linha .= $this->padText('', 7);

            //C??digo de barras
            $linha .= $this->padNumber(0, 16);

            //Tipo medicamento (0=Similar, 1= Gen??rico, 2=??tico ou de marca)
            $linha .= '0';

            //Produto inclu??do no campo da Substitui????o Tribut??ria (S/N) ??? Obs: Apenas para o Estado de RS
            $linha .= 'N';

            //Data de in??cio da Substitui????o Tribut??ria ??? Obs: Apenas para o Estado de RS
            $linha .= $this->padText('', 10);

            //Produto com Pre??o Tabelado (S/N) ??? Obs: Apenas para o Estado de RS
            $linha .= ' ';

            //Valor Unit??rio da Substitui????o Tribut??ria(13,2) ??? Obs: Apenas para o Estado de RS
            $linha .= $this->padNumber(0, 13);

            //MVA da Substitui????o Tribut??ria (7,2) ??? Obs: Apenas para o Estado de RS
            $linha .= $this->padNumber(0, 7);

            //Grupo da Substitui????o Tribut??ria (01=Autope??as/02=Ra????es/03=Colch??es/04=Cosm??ticos/05=Arroz beneficiado/06=Rolamentos e Correias de transmiss??o/07=Tintas/08=Sucos de frutas e outras bebidas n??o alco??licas/09=Ferramentas/10=Materiais El??tricos/11=Materiais de Constru????o, Acabamento, Bricolagem ou Adorno/12=Bicicletas/13=Brinquedos/14=Materiais de Limpeza/15=Produtos Aliment??cios/16=Artefatos de Uso Dom??stico/17=Bebidas Quentes/18=Artigos de Papelaria/19=Instrumentos Musicais/20=Prod. Eletr??nicos, Eletroeletr??nicos, Eletrodom??sticos/21=PROT 160/09 Artefatos de uso Dom??stico/22=PROT 162/09 Prod. Elet. Eletroelet. e Eletrodom./23=PROT 163/09 Material de Limpeza/24=PROT 164/09 Produtos Aliment??cios/25=PROT 207/09 Artefatos de uso Dom??stico/26=PROT 208/09 Ferramentas/27=PROT 210/09 Materiais El??tricos/28=PROT 211/09 Mat. de Const. Acab. Bricol. Ou Adorno/29=PROT 212/09 Artigos de Papelaria/30=PROT 213/09 Produtos Aliment??cios/31=PROT 058/10 Artigos de Papelaria)??? Obs: Apenas para o Estado de RS
            $linha .= '0';

            //Equipamento de ECF(S/N) ??? Obs: Apenas para o Estado de PR
            $linha .= ' ';

            //Servi??o Tributado pelo ISSQN 680 680 1 Caractere Informar S ou N
            $linha .= 'N';

            //EX TIPI 681 683 3 Caractere
            $linha .= '   ';

            //Periodicidade do IPI 684 684 1 Caractere Informar D=Decendial/M=Mensal
            $linha .= 'M';

            //Classifica????o de Itens 685 691 7 Num??rico Somente para Energia El??trica, Servi??os de Comunica????o e Telecomunica????o
            $linha .= $this->padNumber(0, 7);

            //Quantidade Inicial (16,5) 692 707 16 Decimal 5
            $linha .= $this->padNumber(0, 16);

            //Quantidade Final (16,5) 708 723 16 Decimal 5
            $linha .= $this->padNumber($reg->saldoquantidade * 100000, 16);

            //Conta cont??bil(Do informante em seu poder) 724 730 7 Num??rico Conta cont??bil(Do informante em seu poder)
            $linha .= $this->padNumber(55, 7);

            //Conta cont??bil(Do informante em posse de terceiros) 731 737 7 Num??rico Conta cont??bil(Do informante em posse de terceiros)
            $linha .= $this->padNumber(0, 7);

            //Conta cont??bil(De terceiros em posse do informante) 738 744 7 Num??rico Conta cont??bil(De terceiros em posse do informante)
            $linha .= $this->padNumber(0, 7);

            //Unidade inventariada diferente da comercializada 745 745 1 Caractere Informar S ou N
            $linha .= 'N';

            //Reservado(Brancos) 746 791 46 Caractere
            $linha .= $this->padNumber('', 46);
            
            $linhas[] = $linha;
        }
        
        // monta coneudo e salva arquivo
        $conteudo = implode("\n", $linhas);
        $arquivo = $this->parseNomeArquivo($arquivo, $mes, $codfilial);
        $ret = Storage::disk('dominio')->put($arquivo, $conteudo);
        
        return $ret;
        
    }
    
}
