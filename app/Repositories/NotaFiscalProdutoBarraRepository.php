<?php

namespace MGLara\Repositories;
    
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

use MGLara\Models\NotaFiscalProdutoBarra;

/**
 * Description of NotaFiscalProdutoBarraRepository
 * 
 * @property  Validator $validator
 * @property  NotaFiscalProdutoBarra $model
 */
class NotaFiscalProdutoBarraRepository extends MGRepository {
    
    public function boot() {
        $this->model = new NotaFiscalProdutoBarra();
    }
    
    //put your code here
    public function validate($data = null, $id = null) {
        
        if (empty($data)) {
            $data = $this->modell->getAttributes();
        }
        
        if (empty($id)) {
            $id = $this->model->codnotafiscalprodutobarra;
        }
        
        $this->validator = Validator::make($data, [
            'codnotafiscal' => [
                'numeric',
                'required',
            ],
            'codprodutobarra' => [
                'numeric',
                'required',
            ],
            'codcfop' => [
                'numeric',
                'required',
            ],
            'descricaoalternativa' => [
                'max:100',
                'nullable',
            ],
            'quantidade' => [
                'numeric',
                'required',
            ],
            'valorunitario' => [
                'numeric',
                'required',
            ],
            'valortotal' => [
                'numeric',
                'required',
            ],
            'icmsbase' => [
                'numeric',
                'nullable',
            ],
            'icmspercentual' => [
                'numeric',
                'nullable',
            ],
            'icmsvalor' => [
                'numeric',
                'nullable',
            ],
            'ipibase' => [
                'numeric',
                'nullable',
            ],
            'ipipercentual' => [
                'numeric',
                'nullable',
            ],
            'ipivalor' => [
                'numeric',
                'nullable',
            ],
            'icmsstbase' => [
                'numeric',
                'nullable',
            ],
            'icmsstpercentual' => [
                'numeric',
                'nullable',
            ],
            'icmsstvalor' => [
                'numeric',
                'nullable',
            ],
            'csosn' => [
                'max:4',
                'nullable',
            ],
            'codnegocioprodutobarra' => [
                'numeric',
                'nullable',
            ],
            'icmscst' => [
                'numeric',
                'nullable',
            ],
            'ipicst' => [
                'numeric',
                'nullable',
            ],
            'piscst' => [
                'numeric',
                'nullable',
            ],
            'pisbase' => [
                'numeric',
                'nullable',
            ],
            'pispercentual' => [
                'numeric',
                'nullable',
            ],
            'pisvalor' => [
                'numeric',
                'nullable',
            ],
            'cofinscst' => [
                'numeric',
                'nullable',
            ],
            'cofinsbase' => [
                'numeric',
                'nullable',
            ],
            'cofinsvalor' => [
                'numeric',
                'nullable',
            ],
            'csllbase' => [
                'numeric',
                'nullable',
            ],
            'csllpercentual' => [
                'numeric',
                'nullable',
            ],
            'csllvalor' => [
                'numeric',
                'nullable',
            ],
            'irpjbase' => [
                'numeric',
                'nullable',
            ],
            'irpjpercentual' => [
                'numeric',
                'nullable',
            ],
            'irpjvalor' => [
                'numeric',
                'nullable',
            ],
            'cofinspercentual' => [
                'numeric',
                'nullable',
            ],
            'codnotafiscalprodutobarraorigem' => [
                'numeric',
                'nullable',
            ],
        ], [
            'codnotafiscal.numeric' => 'O campo "codnotafiscal" deve ser um n??mero!',
            'codnotafiscal.required' => 'O campo "codnotafiscal" deve ser preenchido!',
            'codprodutobarra.numeric' => 'O campo "codprodutobarra" deve ser um n??mero!',
            'codprodutobarra.required' => 'O campo "codprodutobarra" deve ser preenchido!',
            'codcfop.numeric' => 'O campo "codcfop" deve ser um n??mero!',
            'codcfop.required' => 'O campo "codcfop" deve ser preenchido!',
            'descricaoalternativa.max' => 'O campo "descricaoalternativa" n??o pode conter mais que 100 caracteres!',
            'quantidade.numeric' => 'O campo "quantidade" deve ser um n??mero!',
            'quantidade.required' => 'O campo "quantidade" deve ser preenchido!',
            'valorunitario.numeric' => 'O campo "valorunitario" deve ser um n??mero!',
            'valorunitario.required' => 'O campo "valorunitario" deve ser preenchido!',
            'valortotal.numeric' => 'O campo "valortotal" deve ser um n??mero!',
            'valortotal.required' => 'O campo "valortotal" deve ser preenchido!',
            'icmsbase.numeric' => 'O campo "icmsbase" deve ser um n??mero!',
            'icmspercentual.numeric' => 'O campo "icmspercentual" deve ser um n??mero!',
            'icmsvalor.numeric' => 'O campo "icmsvalor" deve ser um n??mero!',
            'ipibase.numeric' => 'O campo "ipibase" deve ser um n??mero!',
            'ipipercentual.numeric' => 'O campo "ipipercentual" deve ser um n??mero!',
            'ipivalor.numeric' => 'O campo "ipivalor" deve ser um n??mero!',
            'icmsstbase.numeric' => 'O campo "icmsstbase" deve ser um n??mero!',
            'icmsstpercentual.numeric' => 'O campo "icmsstpercentual" deve ser um n??mero!',
            'icmsstvalor.numeric' => 'O campo "icmsstvalor" deve ser um n??mero!',
            'csosn.max' => 'O campo "csosn" n??o pode conter mais que 4 caracteres!',
            'codnegocioprodutobarra.numeric' => 'O campo "codnegocioprodutobarra" deve ser um n??mero!',
            'icmscst.numeric' => 'O campo "icmscst" deve ser um n??mero!',
            'ipicst.numeric' => 'O campo "ipicst" deve ser um n??mero!',
            'piscst.numeric' => 'O campo "piscst" deve ser um n??mero!',
            'pisbase.numeric' => 'O campo "pisbase" deve ser um n??mero!',
            'pispercentual.numeric' => 'O campo "pispercentual" deve ser um n??mero!',
            'pisvalor.numeric' => 'O campo "pisvalor" deve ser um n??mero!',
            'cofinscst.numeric' => 'O campo "cofinscst" deve ser um n??mero!',
            'cofinsbase.numeric' => 'O campo "cofinsbase" deve ser um n??mero!',
            'cofinsvalor.numeric' => 'O campo "cofinsvalor" deve ser um n??mero!',
            'csllbase.numeric' => 'O campo "csllbase" deve ser um n??mero!',
            'csllpercentual.numeric' => 'O campo "csllpercentual" deve ser um n??mero!',
            'csllvalor.numeric' => 'O campo "csllvalor" deve ser um n??mero!',
            'irpjbase.numeric' => 'O campo "irpjbase" deve ser um n??mero!',
            'irpjpercentual.numeric' => 'O campo "irpjpercentual" deve ser um n??mero!',
            'irpjvalor.numeric' => 'O campo "irpjvalor" deve ser um n??mero!',
            'cofinspercentual.numeric' => 'O campo "cofinspercentual" deve ser um n??mero!',
            'codnotafiscalprodutobarraorigem.numeric' => 'O campo "codnotafiscalprodutobarraorigem" deve ser um n??mero!',
        ]);

        return $this->validator->passes();
        
    }
    
    public function used($id = null) {
        if (!empty($id)) {
            $this->findOrFail($id);
        }
        
        if ($this->model->NotaFiscalProdutoBarraS->count() > 0) {
            return 'Nota Fiscal Produto Barra sendo utilizada em "NotaFiscalProdutoBarra"!';
        }
        
        if ($this->model->EstoqueMovimentoS->count() > 0) {
            return 'Nota Fiscal Produto Barra sendo utilizada em "EstoqueMovimento"!';
        }
        
        return false;
    }
    
    public function listing($filters = [], $sort = [], $start = null, $length = null) {
        
        // Query da Entidade
        $qry = NotaFiscalProdutoBarra::query();
        
        // Filtros
         if (!empty($filters['codnotafiscalprodutobarra'])) {
            $qry->where('codnotafiscalprodutobarra', '=', $filters['codnotafiscalprodutobarra']);
        }

            $qry = $qry->join('tblnotafiscal', function($join) use ($filters) {
            $join->on('tblnotafiscal.codnotafiscal', '=', 'tblnotafiscalprodutobarra.codnotafiscal');
        });
        
        if (!empty($filters['notasfiscais_codnaturezaoperacao'])) {
            $qry = $qry->where('tblnotafiscal.codnaturezaoperacao', '=', $filters['notasfiscais_codnaturezaoperacao']);
        }

        if (!empty($filters['notasfiscais_codpessoa'])) {
            $qry = $qry->where('tblnotafiscal.codpessoa', '=', $filters['notasfiscais_codpessoa']);
        }

        if (!empty($filters['notasfiscais_codfilial'])) {
            $qry = $qry->where('tblnotafiscal.codfilial', '=', $filters['notasfiscais_codfilial']);
        }

        if (!empty($filters['notasfiscais_lancamento_de'])) {
            $qry = $qry->where('tblnotafiscal.saida', '>=', $filters['notasfiscais_lancamento_de']);
        }

        if (!empty($filters['notasfiscais_lancamento_ate'])) {
            $qry = $qry->where('tblnotafiscal.saida', '<=', $filters['notasfiscais_lancamento_ate']);
        }

        if (!empty($filters['notasfiscais_codproduto']))
        {
            $qry = $qry->join('tblprodutobarra', function($join) use ($filters) {
                $join->on('tblprodutobarra.codprodutobarra', '=', 'tblnotafiscalprodutobarra.codprodutobarra');
            });
            
            $qry = $qry->join('tblprodutovariacao', function($join) use ($filters) {
                $join->on('tblprodutovariacao.codprodutovariacao', '=', 'tblprodutobarra.codprodutovariacao');
            });
            
            $qry = $qry->where('tblprodutovariacao.codproduto', '=', $filters['notasfiscais_codproduto']);
        }

        if (!empty($filters['notasfiscais_codprodutovariacao'])) {
            $qry->where('tblprodutovariacao.codprodutovariacao', '=', $filters['notasfiscais_codprodutovariacao']);
        }


        $count = $qry->count();

        
        // Paginacao
        if (!empty($start)) {
            $qry->offset($start);
        }
        if (!empty($length)) {
            $qry->limit($length);
        }
        
        // Ordenacao
        foreach ($sort as $s) {
            $qry->orderBy($s['column'], $s['dir']);
        }
        
        //dd($qry->toSql());
        
        // Registros
        return [
            'recordsFiltered' => $count
            , 'recordsTotal' => NotaFiscalProdutoBarra::count()
            , 'data' => $qry->get()
        ];
        
    }
    
    public function calculaTributacao()
    {
        
        $trib = TributacaoNaturezaOperacao
                ::where('codtributacao', $this->ProdutoBarra->Produto->codtributacao)
                ->where('codtipoproduto', $this->ProdutoBarra->Produto->codtipoproduto)
                ->where('codnaturezaoperacao', $this->NotaFiscal->codnaturezaoperacao)
                ->whereRaw("('{$this->ProdutoBarra->Produto->Ncm->ncm}' ilike ncm || '%' or ncm is null)");
                
        if ($this->NotaFiscal->Pessoa->Cidade->codestado == $this->NotaFiscal->Filial->Pessoa->Cidade->codestado) {
            $trib->where('codestado', $this->NotaFiscal->Pessoa->Cidade->codestado);
            $filtroEstado = 'codestado = :codestado';
        } else {
            $trib->whereNull('codestado');
        }
        
        if (!($trib = $trib->first())) {
            echo '<h1>Erro Ao Calcular Tributacao</h1>';
            dd($this);
            return false;
        }
        
        //Traz codigos de tributacao
        $this->codcfop = $trib->codcfop;

        if ($this->NotaFiscal->Filial->crt == Filial::CRT_REGIME_NORMAL)
        {

            //CST's
            $this->icmscst = $trib->icmscst;
            $this->ipicst = $trib->ipicst;
            $this->piscst = $trib->piscst;
            $this->cofinscst = $trib->cofinscst;

            If (!empty($this->valortotal) && ($this->NotaFiscal->emitida))
            {
                    //Calcula ICMS				
                    If (!empty($trib->icmslpbase))
                        $this->icmsbase = round(($trib->icmslpbase * $this->valortotal)/100, 2);

                    $this->icmspercentual = $trib->icmslppercentual;

                    If ((!empty($this->icmsbase)) and (!empty($this->icmspercentual)))
                        $this->icmsvalor = round(($this->icmsbase * $this->icmspercentual)/100, 2);

                    //Calcula PIS
                    If ($trib->pispercentual > 0)
                    {
                        $this->pisbase = $this->valortotal;
                        $this->pispercentual = $trib->pispercentual;
                        $this->pisvalor = round(($this->pisbase * $this->pispercentual)/100, 2);
                    }

                    //Calcula Cofins
                    If ($trib->cofinspercentual > 0)
                    {
                        $this->cofinsbase = $this->valortotal;
                        $this->cofinspercentual = $trib->cofinspercentual;
                        $this->cofinsvalor = round(($this->cofinsbase * $this->cofinspercentual)/100, 2);
                    }

                    //Calcula CSLL
                    If ($trib->csllpercentual > 0)
                    {
                        $this->csllbase = $this->valortotal;
                        $this->csllpercentual = $trib->csllpercentual;
                        $this->csllvalor = round(($this->csllbase * $this->csllpercentual)/100, 2);
                    }

                    //Calcula IRPJ
                    If ($trib->irpjpercentual > 0)
                    {
                        $this->irpjbase = $this->valortotal;
                        $this->irpjpercentual = $trib->irpjpercentual;
                        $this->irpjvalor = round(($this->irpjbase * $this->irpjpercentual)/100, 2);
                    }

            }
        }
        else
        {
            $this->csosn = $trib->csosn;

            //Calcula ICMSs
            If (!empty($this->valortotal) && ($this->NotaFiscal->emitida)) {
                If (!empty($trib->icmsbase))
                    $this->icmsbase = round(($trib->icmsbase * $this->valortotal)/100, 2);

                $this->icmspercentual = $trib->icmspercentual;

                If ((!empty($this->icmsbase)) and (!empty($this->icmspercentual)))
                    $this->icmsvalor = round(($this->icmsbase * $this->icmspercentual)/100, 2);
            }
        }        
    }
    
}
