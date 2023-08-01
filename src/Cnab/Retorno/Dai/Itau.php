<?php

namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Dai;

use Illuminate\Support\Arr;
use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Eduardokum\LaravelBoleto\Cnab\Retorno\Dai\AbstractRetorno;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;

class Itau extends AbstractRetorno implements RetornoCnab240
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_ITAU;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '00' => 'Débito efetuado',
        '01' => 'Insuficiência de fundos - débito não efetuado',
        '02' => 'Débito cancelado',
        '03' => 'Débito autorizado pela agência - efetuado',
        'HA' => 'Lote não aceito',
        'HB' => 'Inscrição da empresa inválida para o contrato',
        'HC' => 'Convênio com a empresa inexistente/inválido para o contrato',
        'AA' => 'Controle inválido',
        'AB' => 'Tipo de operação inválido',
        'AC' => 'Tipo de serviço inválido',
        'AD' => 'Forma de lançamento inválida',
        'AF' => 'Código de convênio inválido',
        'AH' => 'Nr. sequencial do registro no lote inválido',
        'AI' => 'Código de segmento de detalhe inválido',
        'AJ' => 'Tipo de movimento inválido',
        'AL' => 'Código do banco inválido',
        'AM' => 'Agência mantedora da conta corrente do debitado inválida',
        'AN' => 'Conta corrente/dígito verificador do debitado inválido',
        'AP' => 'Data lançamento inválida',
        'AQ' => 'Tipo/quantidade da moeda inválida',
        'AR' => 'Valor do lançamento inválido',
        'AS' => 'Parcela vinculada',
        'BD' => 'Confirmação de agendamento',
        'IA' => 'Tipo do encargo inválido',
        'IB' => 'C/c com restrição',
        'IC' => 'C/c do debitado em liquidação',
        'ID' => 'Valor da mora / taxa da mora inválida',
        'IE' => 'Conta corrente do debitado encerrada',
        'IF' => 'Taxa da mora maior que 50,00000 %',
        'IG' => 'Complemento de histórico inválido',
        'IH' => 'Conta corrente para crédito não autorizada',
        'II' => 'Cancelamento não encontrado',
        'IK' => 'Valor do débito acima do limite',
        'IL' => 'Limite diário de débito ultrapassado',
        'IM' => 'Cpf/cnpj do debitado inválido',
        'IN' => 'Cpf/cnpj do debitado não pertence à conta corrente indicada',
        'IZ' => 'Reservado ( data da mora)',
        'TA' => 'Lote não aceito - totais do lote com diferença',
        'PE' => 'Débito pendente de autorização pelo debitado',
        'NA' => 'Débito não autorizado pelo debitado',
        'AT' => 'Débito autorizado pelo debitado',
        'RC' => 'Débito recusado pelo debitado ',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados' => 0,
            'entradas' => 0,
            'baixados' => 0,
            'protestados' => 0,
            'erros' => 0,
            'alterados' => 0,
            'outros' => 0,
        ];
    }

    /**
     * @param array $header
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setCodBanco($this->rem(1, 3, $header))
            ->setLoteServico($this->rem(4, 7, $header))
            ->setTipoRegistro($this->rem(8, 8, $header))
            ->setTipoInscricao($this->rem(18, 18, $header))
            ->setNumeroInscricao($this->rem(19, 32, $header))
            ->setConvenio($this->rem(33, 45, $header))
            ->setAgencia($this->rem(54, 57, $header))
            ->setConta($this->rem(66, 70, $header))
            ->setContaDv($this->rem(72, 72, $header))
            ->setNomeEmpresa($this->rem(73, 102, $header))
            ->setNomeBanco($this->rem(103, 132, $header))
            ->setCodigoRemessaRetorno($this->rem(143, 143, $header))
            ->setData($this->rem(144, 151, $header))
            ->setHoraGeracao($this->rem(152, 157, $header))
            ->setNumeroSequencialArquivo($this->rem(158, 163, $header))
            ->setVersaoLayoutArquivo($this->rem(164, 166, $header));

        return true;
    }

    /**
     * @param array $headerLote
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeaderLote(array $headerLote)
    {
        $this->getHeaderLote()
            ->setCodBanco($this->rem(1, 3, $headerLote))
            ->setNumeroLoteRetorno($this->rem(4, 7, $headerLote))
            ->setTipoRegistro($this->rem(8, 8, $headerLote))
            ->setTipoOperacao($this->rem(9, 9, $headerLote))
            ->setTipoServico($this->rem(10, 11, $headerLote))
            ->setVersaoLayoutLote($this->rem(14, 16, $headerLote))
            ->setTipoInscricao($this->rem(18, 18, $headerLote))
            ->setNumeroInscricao($this->rem(19, 32, $headerLote))
            ->setConvenio($this->rem(33, 45, $headerLote))
            ->setAgencia($this->rem(54, 57, $headerLote))
            ->setConta($this->rem(66, 70, $headerLote))
            ->setContaDv($this->rem(72, 72, $headerLote))
            ->setNomeEmpresa($this->rem(73, 102, $headerLote));

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();

        if ($this->getSegmentType($detalhe) == 'A') {

            $d->setOcorrenciaTipo($this->rem(15, 17, $detalhe))
                ->setPagador([
                    'nome' => $this->rem(44, 73, $detalhe),
                    'documento' => $this->rem(217, 230, $detalhe),
                ])
                ->setNumeroDocumento($this->rem(74, 88, $detalhe))
                ->setDataVencimento($this->rem(94, 101, $detalhe), 'dmY') // DATA AGENDADA DATA PARA O LANÇAMENTO DO DÉBITO
                ->setValorIOF(Util::nFloat($this->rem(105, 109, $detalhe)/100, 2, false))
                ->setValor(Util::nFloat($this->rem(120, 134, $detalhe)/100, 2, false))
                ->setNossoNumero($this->rem(135, 154, $detalhe))
                ->setDataOcorrencia($this->rem(155, 162, $detalhe))
                ->setValorRecebido(Util::nFloat($this->rem(163, 177, $detalhe)/100, 2, false))
                ->setValorMulta(Util::nFloat($this->rem(180, 196, $detalhe)/100, 2, false));

            /**
             * ocorrencias
            */
            $lista_de_ocorrencias = array_filter(array_map('trim', str_split($this->rem(231, 240, $detalhe), 2)));

            $d->setOcorrencia($lista_de_ocorrencias[0])
            ->setOcorrenciaDescricao(Arr::get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'));

            $ocorrencias_liquidado = array_intersect($lista_de_ocorrencias, ['00', '03']);
            $ocorrencias_entrada = array_intersect($lista_de_ocorrencias, ['BD', 'AT']);
            $ocorrencias_outros = array_intersect($lista_de_ocorrencias, ['02', 'II', 'PE']);

            if (!empty($ocorrencias_liquidado)) {
                $this->totais['liquidados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA)
                ->setDataCredito($this->rem(155, 162, $detalhe));

            } else if (!empty($ocorrencias_entrada)) {
                $this->totais['entradas']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);

            } else if (!empty($ocorrencias_outros)) {
                $this->totais['outros']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);

            } else if (in_array('NA', $lista_de_ocorrencias)) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);

            } else {
                $this->totais['erros']++;

                $errors = [];
                foreach ($lista_de_ocorrencias as $ocorrencia) {
                    $errors[] = $this->ocorrencias[$ocorrencia] ?? '';
                }

                $d->setError(Util::appendStrings(...$errors));
            }
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailerLote(array $trailer)
    {
        $this->getTrailerLote()
            ->setLoteServico($this->rem(4, 7, $trailer))
            ->setTipoRegistro($this->rem(8, 8, $trailer))
            ->setQtdRegistroLote((int) $this->rem(18, 23, $trailer))
            ->setValorTotalTitulosCobrancaSimples(Util::nFloat($this->rem(24, 41, $trailer)/100, 2, false))
        ;

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setNumeroLote($this->rem(4, 7, $trailer))
            ->setTipoRegistro($this->rem(8, 8, $trailer))
            ->setQtdLotesArquivo((int) $this->rem(18, 23, $trailer))
            ->setQtdRegistroArquivo((int) $this->rem(24, 29, $trailer));

        return true;
    }
}
